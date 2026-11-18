<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use App\Models\Proveedor;
use App\Traits\Email;
use Livewire\WithFileUploads;

use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\View;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Juridica extends Component
{
    // Traits para subir archivos y enviar emails
    use WithFileUploads, Email;

    // Variables para los datos del formulario y modelos
    public $item, $desc, $cant = 0, $vUnit = 0, $vTotal = 0, $dias, $otros;
    public $proveedor, $file_cot, $oc_helisa, $justificacion_rechazo, $gr;
    public $observaciones_remision, $observaciones_anulacion, $observaciones_negociacion, $justificacion_productor;

    // Variables para la orden y presupuesto seleccionados
    public $presupuesto, $orden_compra;

    public $cod_oc;

    // Variables útiles para la gestión de items y proveedores
    public $ocItems = [], $selectedItem, $maxCant, $maxValor, $maxDias, $maxOtros, $items = [], $proveedores = [];

    public $edit = false;

    // Renderiza la vista y carga proveedores
    public function render()
    {
        $this->getProveedores();
        return view('livewire.productor.ordenes.juridica');
    }

    // Inicializa items si hay una orden de compra existente
    public function mount (){
        if ($this->orden_compra){
            if (method_exists($this, 'getItems')) {
                $this->getItems();
            }

            if ($this->orden_compra->estado_id == 2) {
                $this->cod_oc = $this->orden_compra->cod_oc ?: $this->orden_compra->codigoOC();
            }

            if(empty($this->orden_compra->gr)){
                $this->gr = $this->orden_compra->codigoGR();
            } else {
                $this->gr = $this->orden_compra->gr;
            }
        }
    }

    /*
        * Añade una nueva fila a la lista de items en la orden de compra.
    */
    public function newItem(){
        // Valida los datos del formulario antes de agregar el item
        $this->validate([
            'item' => 'required',
            'desc' => 'required',
            'cant' => "required|numeric|max:$this->maxCant",
            'dias' => "required|numeric",
            'otros' => "required|numeric",
            'vUnit' => 'required|numeric',
            'vTotal' => "required|numeric|max:$this->maxValor"
        ]);

        $this->getVTotal();

        // Buscamos directamente el modelo ItemPresupuesto en la BD / Presupuesto
        $dbItemPresto = $this->presupuesto->presupuestoItems->find($this->item);

        if (!$dbItemPresto) {
            $this->addError('customError', 'El ítem seleccionado no es válido.');
            return;
        }

        if (is_null($this->selectedItem)){
            // Valida que el item no esté repetido
            if (!$this->validateItems($this->item)){
                $this->resetFields();
                $this->addError('customError', 'Este item ya fué registrado.');
                return redirect()->back();
            }

            // Agrega el nuevo item al arreglo usando directamente los datos del modelo
            array_push($this->ocItems, [
                'id' => count($this->ocItems),
                'item' => $dbItemPresto->id,               // ID del item en DB
                'num_item' => $dbItemPresto->num_item,     // Número de ítem real de la BD
                'desc' => $this->desc,
                'cant' => $this->cant,
                'dias' => $this->dias,
                'otros' => $this->otros,
                'vUnit' => $this->vUnit,
                'vTotal' => $this->vTotal
            ]);
        } else {
            // Edita el item seleccionado en el array usando los datos de BD
            $this->ocItems[$this->selectedItem]['item'] = $dbItemPresto->id;
            $this->ocItems[$this->selectedItem]['num_item'] = $dbItemPresto->num_item;
            $this->ocItems[$this->selectedItem]['desc'] = $this->desc;
            $this->ocItems[$this->selectedItem]['cant'] = $this->cant;
            $this->ocItems[$this->selectedItem]['dias'] = $this->dias;
            $this->ocItems[$this->selectedItem]['otros'] = $this->otros;
            $this->ocItems[$this->selectedItem]['vUnit'] = $this->vUnit;
            $this->ocItems[$this->selectedItem]['vTotal'] = $this->vTotal;
        }
        
        $this->resetFields();
    }

    // Elimina un item de la lista de items de la OC
    public function delete($id){
        unset($this->ocItems[$id]);
        $this->resetFields();
    }

    /*
        * Trae la información del item seleccionado para ser editado.
    */
    public function getSelectedItem($id){
        $this->selectedItem = $this->ocItems[$id]['id']; //Guarda la posición en el arreglo

        // Carga los datos del item seleccionado en el formulario
        $this->item = $this->ocItems[$id]['item'];
        $this->desc = $this->ocItems[$id]['desc'];
        $this->cant = $this->ocItems[$id]['cant'];
        $this->dias = $this->ocItems[$id]['dias'];
        $this->otros = $this->ocItems[$id]['otros'];
        $this->vUnit = $this->ocItems[$id]['vUnit'];
        $this->vTotal = $this->ocItems[$id]['vTotal'];

        // Establece los máximos permitidos para el item
        $this->presupuesto->presupuestoItems->map(function ($item){
            if ($this->item == $item->id){
                $this->maxCant = $item->cantidad;
                $this->maxDias = $item->dia;
                $this->maxOtros = $item->otros;
                $this->maxValor = $item->v_total;
            }
        })->first();
    }

    // Obtiene los proveedores disponibles para el presupuesto
    public function getProveedores(){
        $proveedores_presupuesto = [];
        $proveedores_db = Proveedor::select('id', 'tercero')->get();

        // Recorre los items únicos del presupuesto para obtener proveedores
        foreach ($this->presupuesto->presupuestoItems->unique('proveedor') as $item){
            if ($proveedores_id = @unserialize($item->proveedor)){
                foreach ($proveedores_id as $proveedor_id) {
                    array_push($proveedores_presupuesto, $proveedores_db->find($proveedor_id));
                }
            }else {
                array_push($proveedores_presupuesto, $proveedores_db->find($item->proveedor));
            }
        }

        $this->proveedores = collect($proveedores_presupuesto);
    }

    // Valida que el item no esté repetido en la OC
    public function validateItems($itemDB){
        $validator = true;
        foreach ($this->ocItems as $key => $item) {
            if ($item['item'] == $itemDB){
                return false;
            }
        }
        return $validator;
    }

    /*
        * Trae y muestra la orden de compra de la base de datos (si ya está creada).
    */
    public function getItems(){
        // Carga datos generales de la OC
        $this->proveedor = $this->orden_compra->proveedor_id;
        $this->file_cot = $this->orden_compra->archivo_cot;
        $this->justificacion_rechazo = $this->orden_compra->justificacion_rechazo;
        $this->observaciones_remision = $this->orden_compra->observacion_remision;

        // Carga los items de la OC
        foreach ($this->orden_compra->ordenItems as $item){
            // Opcional: si prefieres buscarlo directo del presupuesto relacionado en vez del guardado:
            // $dbItemPresto = $this->presupuesto->presupuestoItems->find($item->item_id);

            array_push($this->ocItems, [
                'id' => count($this->ocItems),
                'item' => $item->item_id,
                'num_item' => $item->num_item, // Extraído de la BD de la orden/ítem
                'desc' => $item->desc_oc,
                'cant' => $item->cant_oc,
                'dias' => $item->dias_oc,
                'otros' => $item->otros_oc,
                'vUnit' => $item->vunit_oc,
                'vTotal' => $item->vtotal_oc
            ]);
        }
    }

    // Calcula el valor total del item
    public function getVTotal(){
        $this->vUnit = trim($this->vUnit);
        $this->vUnit = str_replace(",",'', $this->vUnit);

        $this->validate([
            'cant' => 'required|numeric',
            'vUnit' => 'required|numeric'
        ]);

        // Calcula el valor total del item
        $this->vTotal = ($this->cant * $this->vUnit);

        $this->updatedVTotal();
    }

    // Envía la orden de compra para aprobación de controller (crea o actualiza)
    public function enviarAprobacion()
    {
        $this->validate([
            'proveedor' => 'required',
            'justificacion_productor' => 'required|string',
            'file_cot' => 'required|file|mimes:pdf,xls,xlsx|max:10000'
        ]);

        if (count($this->ocItems) == 0) {
            $this->addError('customError', 'No puedes enviar una orden de compra vacía.');
            return redirect()->back();
        }

        if ($this->orden_compra) {

            // Actualiza la OC existente
            $this->orden_compra->estado_id = 8;
            $this->orden_compra->fecha_envio_produccion = now();
            $this->orden_compra->proveedor_id = $this->proveedor;
            $this->orden_compra->justificacion_productor = $this->justificacion_productor;
            $this->orden_compra->archivo_cot = $this->file_cot->store('public/ordenes_juridicas');

            $this->orden_compra->save();

            $this->deleteItems($this->orden_compra->id);
            $this->storeItems($this->orden_compra->id);

        } else {

            // Crea una nueva OC
            $orden = new OrdenCompra;

            $orden->tipo_oc = 1;
            $orden->estado_id = 8;
            $orden->fecha_envio_produccion = now();
            $orden->presupuesto_id = $this->presupuesto->id;
            $orden->proveedor_id = $this->proveedor;
            $orden->justificacion_productor = $this->justificacion_productor;
            $orden->archivo_cot = $this->file_cot->store('public/ordenes_juridicas');

            $orden->save();

            $this->storeItems($orden->id);
        }

        $this->resetFields();
        $this->resetOcInfo();

        $this->emit('ordenCreada');

        return redirect()->back()->with(
            'success',
            'Orden de compra enviada a aprobación.');
    }

    // Guarda los items de la OC en la base de datos
    public function storeItems($orden_id){
        foreach ($this->ocItems as $item) {
            $itemsOrden = new OcItem;
            $itemsOrden->oc_id = $orden_id;
            $itemsOrden->item_id = $item['item'];
            $itemsOrden->num_item = $item['num_item'];
            $itemsOrden->desc_oc = $item['desc'];
            $itemsOrden->cant_oc = $item['cant'];
            $itemsOrden->dias_oc = $item['dias'];
            $itemsOrden->otros_oc = $item['otros'];
            $itemsOrden->vunit_oc = $item['vUnit'];
            $itemsOrden->vtotal_oc = $item['vTotal'];
            $itemsOrden->save();
        }
    }

    // Elimina los items de la OC en la base de datos
    public function deleteItems($orden_id){
        $items = OcItem::where('oc_id', $orden_id)->get();

        $items->map(function ($item){
            $item->delete();
        });
    }

    // Cambia el estado de la orden de compra (aprobada, rechazada, GR, anulada)
    public function cambioEstado($estado){    
        $messaje = '';

        if ($estado == 1) {
            $this->validate([
                'observaciones_negociacion' => 'nullable|string|max:1000'
            ]);

            try {
                // Generamos el PDF automáticamente de manera idéntica a tu controlador web
                $this->orden_compra->load([
                    'proveedor',
                    'presupuesto.gestion.contacto',
                    'presupuesto.presupuestoItems',
                    'ordenItems',
                ]);

                $dompdf = new Dompdf(['enable_remote' => true]);
                $html = View::make('exports.orden_compra_pdf', [
                    'orden' => $this->orden_compra,
                ])->render();

                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pdfOutput = $dompdf->output();

                // Definimos la ruta y nombre del archivo automático
                $nombreArchivo = 'orden_helisa_' . ($this->orden_compra->cod_oc ?? $this->orden_compra->id) . '_' . time() . '.pdf';
                $rutaAlmacenamiento = 'public/ordenes_juridicas_helisa/' . $nombreArchivo;

                // Guardamos el PDF generado en el Storage
                Storage::put($rutaAlmacenamiento, $pdfOutput);

                DB::transaction(function () use ($rutaAlmacenamiento) {
                    $orden = OrdenCompra::lockForUpdate()->findOrFail($this->orden_compra->id);

                    if (!empty($orden->cod_oc)) {
                        throw new \RuntimeException('Esta orden ya fue aprobada.');
                    }

                    $orden->archivo_orden_helisa      = $rutaAlmacenamiento;
                    $orden->observaciones_negociacion = $this->observaciones_negociacion;
                    $orden->fecha_aprobacion          = now();
                    $orden->cod_oc                    = $orden->codigoOC();
                    $orden->estado_id                 = 1;
                    $orden->save();

                    $this->orden_compra = $orden;
                });
            } catch (\Throwable $e) {
                $this->addError('customError', $e->getMessage());
                return;
            }

            $this->mailOrdenAprobada($this->orden_compra);
            $messaje = 'Orden de compra APROBADA y generada automáticamente con código ' . $this->orden_compra->cod_oc . '.';
            
        }elseif ($estado == 3) {
            // ORDEN RECHAZADA
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->orden_compra->justificacion_rechazo = $this->justificacion_rechazo;
            $messaje = 'Orden de compra RECHAZADA.';
            
        } elseif ($estado == 5) {
            // GR GENERADO
            $this->validate([
                'observaciones_remision' => 'nullable|string|max:1000'
            ]);

            try {
                // 1. Generamos el PDF primero (fuera del lock para optimizar tiempo)
                $this->orden_compra->load([
                    'proveedor',
                    'presupuesto.gestion.contacto',
                    'presupuesto.presupuestoItems',
                    'ordenItems',
                ]);

                $dompdf = new Dompdf(['enable_remote' => true]);
                $html = View::make('exports.good_recive_pdf', [
                    'orden' => $this->orden_compra,
                ])->render();

                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pdfOutput = $dompdf->output();
                $nombreArchivo = 'good_receive_' . ($this->orden_compra->id) . '_' . time() . '.pdf';
                $rutaAlmacenamiento = 'public/good_receives/' . $nombreArchivo;

                Storage::put($rutaAlmacenamiento, $pdfOutput);

                // Actualizar la orden dentro de una transacción.
                DB::transaction(function () use ($rutaAlmacenamiento, $estado) {
                    // Bloqueamos la fila de esta orden específica
                    $orden = OrdenCompra::lockForUpdate()->findOrFail($this->orden_compra->id);

                    if (!empty($orden->gr)) {
                        throw new \RuntimeException('Este Good Receive ya fue generado.');
                    }

                    // Código basado en el ID de la orden.
                    $orden->gr = $orden->codigoGR();
                    $orden->archivo_gr = $rutaAlmacenamiento;
                    $orden->observacion_remision = $this->observaciones_remision;
                    $orden->estado_id = $estado;
                    $orden->save();

                    $this->orden_compra = $orden;
                    $this->gr = $orden->gr; // Sincronizamos la propiedad visual
                });

            } catch (\Throwable $e) {
                //dd($e);
                $this->addError('customError', $e->getMessage());
                return;
            }

        $this->mailGrGenerado($this->orden_compra);
        $messaje = 'Good Receive ' . $this->orden_compra->gr . ' guardado y enviado con éxito.';

        } elseif ($estado == 6) {
            // ORDEN ANULADA
            $this->validate([
                'observaciones_anulacion' => 'required|string|max:1000'
            ]);

            $this->orden_compra->observaciones_anulacion = $this->observaciones_anulacion;
            $this->mailOrdenAnulada($this->orden_compra);
            $messaje = 'Orden de compra ANULADA.';
        }

        $this->orden_compra->estado_id = $estado;
        $this->orden_compra->update();

        return redirect()->route('ordenes-compra')->with('success', $messaje);

    }

    public function cargarGRParaAprobar($id){
        $this->orden_compra = OrdenCompra::find($id);
        $this->gr = $this->orden_compra->gr ?: $this->orden_compra->codigoGR();
    }    

    public function selecionarGR($id){
        $this->orden_compra = OrdenCompra::find($id);
        $this->gr = $this->orden_compra->gr ?: $this->orden_compra->codigoGR();
    }

    public function cargarOrdenParaAprobar($id)
    {
        $this->orden_compra = OrdenCompra::find($id);
        $this->cod_oc = $this->orden_compra->codigoOC();
    }
    
    public function seleccionarOrden($id)
    {
        $this->orden_compra = OrdenCompra::find($id);
        $this->cod_oc = $this->orden_compra->codigoOC();
    }

    // El método mejorado para evitar sobreposiciones usando transacciones y locks






    // Elimina la orden de compra y sus items
    public function deleteOrden(){
        $this->orden_compra->ordenItems->map(function ($item){
            $item->delete();
        });
        $this->orden_compra->delete();

        $this->emit('ordenCreada');
        return redirect()->back()->with('success', 'Orden de compra eliminada.');
    }

    /* UPDATES: Métodos que se ejecutan al actualizar campos del formulario */

    // Al actualizar el item seleccionado
    public function updatedItem(){
        $this->validate([
            'item' => 'required'
        ]);

        $dbItemPresto = $this->presupuesto->presupuestoItems->find($this->item);

        // Valida disponibilidad
        if (!$dbItemPresto->disponible){
            $this->addError('customError', 'Este item no está disponible para ser consumido.');
            $this->resetFields();
            return redirect()->back();
        }

        // Calcula cantidades y valores disponibles
        $contCant = 0;
        $acumVTotal = 0;
        foreach ($dbItemPresto->consumidos as $item) {
            if (!($item->OrdenCompra->estado_id == 6)){
                $contCant += $item->cant_oc;
                $acumVTotal += $item->vtotal_oc;
            }
        }

        $this->cant = (($dbItemPresto->cantidad * $dbItemPresto->dia * $dbItemPresto->otros) - $contCant);
        $this->vTotal = ($dbItemPresto->v_total - $acumVTotal);

        if ($this->cant == 0 || $this->vTotal == 0){
            $this->addError('customError', 'Este item ya fué consumido.');
            $this->resetFields();
            return redirect()->back();
        }

        // Carga datos del item
        $this->desc = $dbItemPresto->descripcion;
        $this->vUnit = $dbItemPresto->v_unitario;
        $this->dias = $dbItemPresto->dia;
        $this->otros = $dbItemPresto->otros;

        $this->maxCant = $this->cant;
        $this->maxValor = $this->vTotal;

        $this->getVTotal();
    }

    // Al actualizar la descripción
    public function updatedDesc(){
        $this->validate([
            'desc' => 'required'
        ]);
    }

    // Al actualizar la cantidad
    public function updatedCant(){
        $this->validate([
            'cant' => "required|numeric|max:$this->maxCant",
        ]);

        if ($this->cant < 0){
            $this->cant = 0;
        }

        $this->getVTotal();
    }

    // Al actualizar los días
    public function updatedDias(){
        $this->validate([
            'dias' => "required|numeric|max:$this->maxDias",
        ]);

        if ($this->dias < 0){
            $this->dias = 0;
        }

        $this->getVTotal();
    }

    // Al actualizar otros valores
    public function updatedOtros(){
        $this->validate([
            'otros' => "required|numeric|max:$this->maxOtros",
        ]);

        if ($this->otros < 0){
            $this->otros = 0;
        }

        $this->getVTotal();
    }

    // Al actualizar el valor unitario
    public function updatedVUnit(){
        $this->vUnit = trim($this->vUnit);
        $this->vUnit = str_replace(",",'', $this->vUnit);

        $this->validate([
            'vUnit' => "required|numeric"
        ]);

        if ($this->vUnit < 0){
            $this->vUnit = 0;
        }

        $this->getVTotal();
    }

    // Al actualizar el valor total
    public function updatedVTotal(){
        $this->validate([
            'vTotal' => "required|numeric|max:$this->maxValor"
        ]);
    }

    // Al actualizar el proveedor
    public function updatedProveedor(){
        $this->validate([
            'proveedor' => 'required|numeric',
        ]);

        $this->mount();
        $this->ocItems = [];
        $this->resetFields();
    }

    // Al actualizar la justificación de rechazo
    public function updatedJustificacionRechazo(){
        $this->validate([
            'justificacion_rechazo' => 'required|string|max:1000',
        ]);
    }

    // Al actualizar el archivo de cotización
    public function updatedFile_cot(){
        $this->validate([
            'file_cot' => 'required|file|mimes:pdf|max:10000'
        ]);
    }

    // Al actualizar el GR
    public function updatedGr(){
        $this->validate([
            'gr' => 'required|string'
        ]);
    }

    // Al actualizar las observaciones de remisión
    public function updatedObservacionesRemision(){
        $this->validate([
            'observaciones_remision' => 'nullable|string|max:1000'
        ]);
    }
    /*****/

    // Limpia los campos del formulario de items
    public function resetFields(){
        $this->item = "";
        $this->desc = "";
        $this->cant = 0;
        $this->dias = 0;
        $this->otros = 0;
        $this->vUnit = 0;
        $this->vTotal = 0;

        $this->selectedItem = null;
    }

    // Limpia los campos generales de la OC
    public function resetOcInfo(){
        $this->proveedor = "";
        $this->file_cot = null;
        $this->justificacion_productor = "";
        $this->ocItems = [];
    }

    public function generarPdfReal()
    {
        // Valida que exista la orden o los datos necesarios
        if (!$this->orden_compra) {
            session()->flash('error', 'No hay una orden de compra seleccionada.');
            return;
        }

        // Carga las relaciones necesarias para la vista del PDF
        $this->orden_compra->load('ordenItems', 'proveedorRelacion', 'presupuesto.gestion');

        // Genera el PDF con una vista blade dedicada (ej: pdf.orden-compra)
        $pdf = Pdf::loadView('pdf.orden-compra', [
            'orden_compra' => $this->orden_compra,
            'presupuesto' => $this->presupuesto,
            'ocItems' => $this->ocItems
        ]);

        // Retorna el PDF para visualizarse en el navegador (stream)
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Orden_Compra_' . ($this->orden_compra->cod_oc ?? 'Borrador') . '.pdf');
    }
}
