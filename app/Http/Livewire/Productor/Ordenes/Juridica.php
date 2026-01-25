<?php

namespace App\Http\Livewire\Productor\Ordenes;

use App\Services\PdfService;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use App\Models\Proveedor;
use App\Traits\Email;
use Livewire\WithFileUploads;

class Juridica extends Component
{
    // Traits para subir archivos y enviar emails
    use WithFileUploads, Email;

    // Variables para los datos del formulario y modelos
    public $item, $desc, $cant = 0, $vUnit = 0, $vTotal = 0, $dias, $otros;
    public $proveedor, $file_cot, $oc_helisa, $justificacion_rechazo, $cod_oc, $gr;
    public $observaciones_remision, $observaciones_anulacion, $observaciones_negociacion;

    // Variables para la orden y presupuesto seleccionados
    public $presupuesto, $orden_compra;

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
            $this->getItems();
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

        if (is_null($this->selectedItem)){
            // Valida que el item no esté repetido
            if (!$this->validateItems($this->item)){
                $this->resetFields();
                $this->addError('customError', 'Este item ya fué registrado.');
                return redirect()->back();
            }

            // Agrega el nuevo item al arreglo de items de la OC
            array_push($this->ocItems, [
                'id' => count($this->ocItems),
                'item' => $this->item, // id del item en DB
                'displayItem' => $this->getDisplayItem($this->item),
                'desc' => $this->desc,
                'cant' => $this->cant,
                'dias' => $this->dias,
                'otros' => $this->otros,
                'vUnit' => $this->vUnit,
                'vTotal' => $this->vTotal
            ]);
        }else {
            // Edita el item seleccionado
            $this->ocItems[$this->selectedItem]['displayItem'] = $this->getDisplayItem($this->item);
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
                $this->maxValor = $item->v_unitario;
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
        if ($this->orden_compra->estado_id == 4){
            $this->gr = "GR".$this->orden_compra->id;
        } 
        // Carga los items de la OC
        foreach ($this->orden_compra->ordenItems as $item){
            array_push($this->ocItems, [
                'id' => count($this->ocItems),
                'item' => $item->item_id,
                'displayItem' => $this->getDisplayItem($item->item_id),
                'desc' => $item->desc_oc,
                'cant' => $item->cant_oc,
                'dias' => $item->dias_oc,
                'otros' => $item->otros_oc,
                'vUnit' => $item->vunit_oc,
                'vTotal' => $item->vtotal_oc
            ]);
        }
    }

    // Obtiene el número de item para mostrar al usuario
    public function getDisplayItem($id){
        foreach ($this->presupuesto->presupuestoItems as $key => $item) {
            if ($id == $item->id){
                return $key+1;
            }
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
    public function enviarAprobacion(){
        $this->validate([
            'proveedor' => 'required',
            'file_cot' => 'required|file|mimes:pdf,xls,xlsx|max:10000'
        ]);

        if (count($this->ocItems) == 0){
            $this->addError('customError', 'No puedes enviar una orden de compra vacía.');
            return redirect()->back();
        }

        // Si la orden está creada, actualiza
        if ($this->orden_compra){
            $this->orden_compra->estado_id = 2;
            $this->orden_compra->fecha_envio_produccion = now();
            $this->orden_compra->proveedor_id = $this->proveedor;
            $this->orden_compra->archivo_cot = $this->file_cot->store('public/ordenes_juridicas');
            $this->orden_compra->update();

            $this->deleteItems($this->orden_compra->id);
            $this->storeItems($this->orden_compra->id);
        }else{
            // Si no existe, crea una nueva OC
            $orden = new OrdenCompra;
            $orden->tipo_oc = 1;
            $orden->fecha_envio_produccion = now();
            $orden->presupuesto_id = $this->presupuesto->id;

            $orden->proveedor_id = $this->proveedor;
            $orden->archivo_cot = $this->file_cot->store('public/ordenes_juridicas');

            $orden->save();
            $this->storeItems($orden->id);
        }

        $this->resetFields();
        $this->resetOcInfo();
        $this->emit('ordenCreada');
        return redirect()->back()->with('success', 'Orden de compra enviada a aprobación.');
    }

    // Guarda los items de la OC en la base de datos
    public function storeItems($orden_id){
        foreach ($this->ocItems as $item) {
            $itemsOrden = new OcItem;
            $itemsOrden->oc_id = $orden_id;
            $itemsOrden->item_id = $item['item'];
            $itemsOrden->display_item = $item['displayItem'];
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
    public function cambioEstado($estado, PdfService $pdfService){
        $messaje = '';

        if ($estado == 1){
            // ORDEN APROBADA
            $this->validate([
                'observaciones_negociacion' => 'required|string|max:1000'
            ]);

            $this->orden_compra->observaciones_negociacion = $this->observaciones_negociacion;
            $this->orden_compra->fecha_aprobacion = now();            
            $this->orden_compra->cod_oc = "C".$this->orden_compra->id;
            $crear_pdf_oc = $pdfService->generarPdfOC($this->orden_compra, "public/ordenes_juridicas_helisa");
            $this->orden_compra->archivo_orden_helisa = $crear_pdf_oc;

            $this->mailOrdenAprobada($this->orden_compra);
            $messaje = 'Orden de compra APROBADA.';
        }elseif($estado == 3){
            // ORDEN RECHAZADA
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->orden_compra->justificacion_rechazo = $this->justificacion_rechazo;
            $messaje = 'Orden de compra RECHAZADA.';
        }elseif ($estado == 5) {
            // GR GENERADO
            $this->validate([
                'gr' => 'nullable|string',
                'observaciones_remision' => 'nullable|string|max:1000'
            ]);

            $this->orden_compra->gr = $this->gr;
            $this->orden_compra->observacion_remision = $this->observaciones_remision;
            $this->mailGrGenerado($this->orden_compra);
            $messaje = 'Good Receive guardado y enviado con éxito.';
        }elseif ($estado == 6) {
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

    // Al actualizar el archivo de Helisa
    public function updatedOc_helisa(){
        $this->validate([
            'oc_helisa' => 'required|file|mimes:pdf|max:10000',
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

        $this->ocItems = [];
    }
}
