<?php

namespace App\Http\Livewire\Productor\Ordenes;

use App\Models\Año;
use App\Models\OcItem;
use App\Models\OrdenCompra;
use App\Models\PresupuestoProyecto;
use App\Models\Proveedor;
use App\Services\PdfService;
use App\Traits\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Nomina extends Component
{
    // Traits para subir archivos y enviar emails
    use WithFileUploads, Email;

    // Variables para los datos del formulario y modelos
    public $item, $desc, $cant = 0, $vUnit = 0, $vTotal = 0, $dias, $otros;
    public $proveedor, $file_cot, $justificacion_rechazo, $cod_oc, $gr;
    public $observaciones_remision, $observaciones_anulacion, $observaciones_negociacion, $observaciones_revision_lider,
        $rechazo_revision_lider, $observaciones_revision_gerencia, $rechazo_revision_gerencia, $observaciones_revision_evidencias, $rechazo_revision_evidencias;

    // Variables para la orden y presupuesto seleccionados
    public $presupuesto, $orden_nomina, $orden_id;

    // Variables útiles para la gestión de items y proveedores
    public $nominaItems = [], $selectedItem, $maxCant, $maxValor, $maxDias, $maxOtros, $items = [], $proveedores = [], $centros_costo = [], $centro_costo;

    public $edit = false;

    public function render()
    {
        $this->getCentrosCosto();
        return view('livewire.productor.ordenes.nomina');
    }

    public function mount() {
        if ($this->orden_id) {
            $this->orden_nomina = OrdenCompra::where('id', $this->orden_id)->first();
            $this->presupuesto = $this->orden_nomina->presupuesto;
        }

        if ($this->orden_nomina) {
            $this->getProveedores();
            $this->getItems();
        }
    }

    /*
        * Añade una nueva fila a la lista de items en la nomina.
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
            array_push($this->nominaItems, [
                'id' => count($this->nominaItems),
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
            $this->nominaItems[$this->selectedItem]['displayItem'] = $this->getDisplayItem($this->item);
            $this->nominaItems[$this->selectedItem]['desc'] = $this->desc;
            $this->nominaItems[$this->selectedItem]['cant'] = $this->cant;
            $this->nominaItems[$this->selectedItem]['dias'] = $this->dias;
            $this->nominaItems[$this->selectedItem]['otros'] = $this->otros;
            $this->nominaItems[$this->selectedItem]['vUnit'] = $this->vUnit;
            $this->nominaItems[$this->selectedItem]['vTotal'] = $this->vTotal;
        }
        $this->resetFields();
    }

    // Elimina un item de la lista de items de la OC
    public function delete($id){
        unset($this->nominaItems[$id]);
        $this->resetFields();
    }

    public function getCentrosCosto() {
        // Centros de costo del productor autenticado
        $this->centros_costo = PresupuestoProyecto::with('presupuestoItems')
            ->where([
                ['productor', Auth::user()->id],
                ['estado_id', 1]
            ])
            ->whereHas('presupuestoItems', function ($item){
                $item->select('id', 'cantidad', 'dia', 'otros', 'v_unitario', 'v_total', 'proveedor')
                    ->where('disponible', 1)
                    ->where(function ($q) {
                        $q->where('proveedor', 'LIKE', '%s:1:"1"%')
                            ->orWhere('proveedor', 'LIKE', '%s:1:"2"');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /*
        * Trae la información del item seleccionado para ser editado.
    */
    public function getSelectedItem($id){
        $this->selectedItem = $this->nominaItems[$id]['id']; //Guarda la posición en el arreglo

        // Carga los datos del item seleccionado en el formulario
        $this->item = $this->nominaItems[$id]['item'];
        $this->desc = $this->nominaItems[$id]['desc'];
        $this->cant = $this->nominaItems[$id]['cant'];
        $this->dias = $this->nominaItems[$id]['dias'];
        $this->otros = $this->nominaItems[$id]['otros'];
        $this->vUnit = $this->nominaItems[$id]['vUnit'];
        $this->vTotal = $this->nominaItems[$id]['vTotal'];

        // Establece los máximos permitidos para el item: el saldo real
        // (total - consumido en otras órdenes no anuladas), no los valores brutos.
        $dbItemPresto = $this->presupuesto->presupuestoItems->find($this->item);
        if ($dbItemPresto) {
            $contCant = 0;
            $acumVTotal = 0;
            foreach ($dbItemPresto->consumidos as $consumido) {
                $esOtraOrden = !$this->orden_nomina || $consumido->oc_id != $this->orden_nomina->id;
                if ($esOtraOrden && $consumido->OrdenCompra->estado_id != 6) {
                    $contCant += $consumido->cant_oc;
                    $acumVTotal += $consumido->vtotal_oc;
                }
            }
            $this->maxCant = (($dbItemPresto->cantidad * $dbItemPresto->dia * $dbItemPresto->otros) - $contCant);
            $this->maxDias = $dbItemPresto->dia;
            $this->maxOtros = $dbItemPresto->otros;
            $this->maxValor = ($dbItemPresto->v_total - $acumVTotal);
        }
    }

    // Obtiene los proveedores disponibles para el presupuesto
    public function getProveedores() {
        $proveedores_presupuesto = [];
        $proveedores_db = Proveedor::select('id', 'tercero')
            ->where('categoria_id', 1)
            ->get();

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
        foreach ($this->nominaItems as $key => $item) {
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
        $this->proveedor = $this->orden_nomina->proveedor_id;
        $this->file_cot = $this->orden_nomina->archivo_cot;
        $this->justificacion_rechazo = $this->orden_nomina->justificacion_rechazo;
        $this->observaciones_remision = $this->orden_nomina->observacion_remision;

        if ($this->orden_nomina->estado_id == 2){
            // Generar código de OC
            $cod_cc = $this->presupuesto->cod_cc;
            $cod_cc_explode = explode("-", $cod_cc);
            $this->cod_oc = "OC" . $this->orden_nomina->id;
        }
        if ($this->orden_nomina->estado_id == 4){
            // Generar código de GR
            $cod_cc = $this->presupuesto->cod_cc;
            $cod_cc_explode = explode("-", $cod_cc);
//            $this->gr = "GR" . substr($cod_cc_explode[0], 0, -3);
            $this->gr = "GR".$this->orden_nomina->id;
        }

        // Carga los items de la OC
        foreach ($this->orden_nomina->ordenItems as $item){
            array_push($this->nominaItems, [
                'id' => count($this->nominaItems),
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

    /* UPDATES: Métodos que se ejecutan al actualizar campos del formulario */

    // Al actualizar el centro de costo seleccionado
    public function updatedCentroCosto() {
        $this->presupuesto = PresupuestoProyecto::find($this->centro_costo);
        $this->getProveedores();
        $this->resetOcInfo();
    }

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
        $this->nominaItems = [];
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

        $this->nominaItems = [];
    }

    // Envía la orden de nomina para aprobación de controller (crea o actualiza)
    public function enviarAprobacion() {
        $this->validate([
            'proveedor' => 'required',
            'file_cot' => 'required|file|mimes:pdf,xls,xlsx|max:10000'
        ]);

        if (count($this->nominaItems) == 0){
            $this->addError('customError', 'No puedes enviar una orden de nómina vacía.');
            return redirect()->back();
        }

        // Si la orden está creada, actualiza
        if ($this->orden_nomina){
            $estado_id = 0;
            // SI EL ESTADO ACTUAL ES EDITABLE, SE ENVIA A VALIDACIÓN DE CONTROLLER
            if ($this->orden_nomina->estado_id == 3) {
                $estado_id = 2;

                // Se envia notificación a Controller
                $this->ocJuridicaRevisionController($this->orden_nomina);
            }
            // SI EL ESTADO ES Rechazo revisión lider o Rechazo revisión gerencia, SE ENVIA NUEVAMENTE A REVISIÓN LIDER PRODUCCIÓN
            elseif ($this->orden_nomina->estado_id == 11 || $this->orden_nomina->estado_id == 12) {
                $estado_id = 8;

                // Se envia notificación a Lideres de producción
                $this->ocJuridicaRevisionLiderProd($this->orden_nomina);
            }
            else {
                // Estado actual sin transición definida: no forzar un estado
                // inválido (antes quedaba estado_id = 0 y violaba la FK).
                $this->addError('customError', 'La orden no está en un estado válido para reenviarse a aprobación.');
                return redirect()->back();
            }

            $this->orden_nomina->estado_id = $estado_id;
            $this->orden_nomina->fecha_envio_produccion = now();
            $this->orden_nomina->proveedor_id = $this->proveedor;
            $this->orden_nomina->archivo_cot = $this->file_cot->store('public/ordenes_juridicas');
            $this->orden_nomina->update();

            $this->deleteItems($this->orden_nomina->id);
            $this->storeItems($this->orden_nomina->id);
        }
        else{
            // Si no existe, crea una nueva OC (estado Revisión lider producción)
            $orden = new OrdenCompra;
            $orden->tipo_oc = 3;
            $orden->estado_id = 8;
            $orden->fecha_envio_produccion = now();
            $orden->presupuesto_id = $this->presupuesto->id;

            $orden->proveedor_id = $this->proveedor;
            $orden->archivo_cot = $this->file_cot->store('public/ordenes_juridicas');

            $orden->save();
            $this->storeItems($orden->id);

            // Se envia notificación a Lideres de producción
            $this->ocJuridicaRevisionLiderProd($orden);
        }

        $this->resetFields();
        $this->resetOcInfo();
        return redirect()->route('ordenes-nomina-prod')->with('success', 'Orden de nómina enviada a aprobación.');
    }

    // Guarda los items de la OC en la base de datos
    public function storeItems($orden_id){
        foreach ($this->nominaItems as $item) {
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
        $redirect_route = 'ordenes-compra';

        // GUARDIA DE TRANSICIONES: cada estado destino define qué roles pueden
        // solicitarlo y desde qué estado actual es válido. Cualquier valor no
        // contemplado se rechaza (los métodos públicos de Livewire son
        // invocables desde el navegador aunque el botón no se muestre).
        $reglas = [
            1  => ['roles' => [1],    'desde' => [2]],      // Controller aprueba
            2  => ['roles' => [1],    'desde' => [9]],      // Gerencia aprueba (pasa a 1 abajo)
            3  => ['roles' => [1],    'desde' => [2, 9]],   // Rechazo controller/gerencia
            4  => ['roles' => [1, 6], 'desde' => null],     // Revisión remisión aprobada (líder)
            5  => ['roles' => [1],    'desde' => [4]],      // GR generado
            6  => ['roles' => [1],    'desde' => null],     // Anulada
            9  => ['roles' => [6],    'desde' => [8]],      // Líder aprueba -> gerencia
            11 => ['roles' => [6],    'desde' => [8]],      // Rechazo líder
            12 => ['roles' => [1],    'desde' => [9]],      // Rechazo gerencia
            13 => ['roles' => [1, 6], 'desde' => null],     // Rechazo revisión remisión
            14 => ['roles' => [1],    'desde' => null],     // Revisión remisión controller
        ];

        if (!isset($reglas[$estado])) {
            $this->addError('customError', 'Transición de estado no permitida.');
            return redirect()->back();
        }
        if (!in_array(Auth::user()->rol, $reglas[$estado]['roles'])) {
            $this->addError('customError', 'No tienes permisos para realizar esta acción.');
            return redirect()->back();
        }
        if ($reglas[$estado]['desde'] && !in_array($this->orden_nomina->estado_id, $reglas[$estado]['desde'])) {
            $this->addError('customError', 'La orden no está en un estado válido para esta acción.');
            return redirect()->back();
        }

        if ($estado == 1) {
            // ORDEN APROBADA (revisión de controller)
            $this->validate([
                'observaciones_negociacion' => 'required|string|max:1000'
            ]);

            $this->orden_nomina->observaciones_negociacion = $this->observaciones_negociacion;
            $this->orden_nomina->fecha_aprobacion = now();

            // Generar código de OC
            $cod_cc = $this->presupuesto->cod_cc;
            $this->orden_nomina->cod_oc = "OC".$this->orden_nomina->id;
            $crear_pdf_oc = $pdfService->generarPdfOC($this->orden_nomina, "public/ordenes_juridicas_helisa");
            $this->orden_nomina->archivo_orden_helisa = $crear_pdf_oc;

            $this->ocJuridicaAprobada($this->orden_nomina);
            $messaje = 'Orden de compra APROBADA.';
        }
        elseif ($estado == 2) {
            // REVISIÓN GERENCIA OC APROBADA
            $this->validate([
                'observaciones_revision_gerencia' => 'required|string|max:1000'
            ]);

            $this->orden_nomina->observaciones_revision_gerencia = $this->observaciones_revision_gerencia;
            $this->orden_nomina->fecha_aprobacion = now();

            // Generar código de OC
            $cod_cc = $this->presupuesto->cod_cc;
            $this->orden_nomina->cod_oc = "OC".$this->orden_nomina->id;
            $crear_pdf_oc = $pdfService->generarPdfOC($this->orden_nomina, "public/ordenes_juridicas_helisa");
            $this->orden_nomina->archivo_orden_helisa = $crear_pdf_oc;

            // Se envia notificación de aprobación
            $this->ocJuridicaAprobada($this->orden_nomina);

            // Cambio de estado a id 1 (Aprobado)
            $estado = 1;

            $messaje = 'Revisión Orden de compra APROBADA.';
        }
        elseif($estado == 3) {
            // ORDEN RECHAZADA
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->orden_nomina->justificacion_rechazo = $this->justificacion_rechazo;
            $messaje = 'Orden de compra RECHAZADA.';
        }
        elseif ($estado == 4) {
            // REVISIÓN REMISIÓN APROBADA (LIDER DE PRODUCCIÓN)
            $this->validate([
                'observaciones_revision_evidencias' => 'required|string|max:1000',
            ]);

            $this->orden_nomina->observaciones_revision_evidencias = $this->observaciones_revision_evidencias;

            // Se envia notificación a Controller
            $this->ocJuridicaRevisionRemiController($this->orden_nomina);

            $messaje = 'Revisión Orden de compra APROBADA.';
            $redirect_route = 'ordenes-compra-lid';
        }
        elseif ($estado == 5) {
            // GR GENERADO
            $this->validate([
                'gr' => 'nullable|string'
            ]);

            $this->orden_nomina->gr = $this->gr;
            $this->ocJuridicaGrGenerado($this->orden_nomina);
            $messaje = 'Good Receive guardado y enviado con éxito.';
        }
        elseif ($estado == 6) {
            // ORDEN ANULADA
            $this->validate([
                'observaciones_anulacion' => 'required|string|max:1000'
            ]);

            $this->orden_nomina->observaciones_anulacion = $this->observaciones_anulacion;
            $this->ocJuridicaAnulada($this->orden_nomina);
            $messaje = 'Orden de compra ANULADA.';
        }
        elseif ($estado == 9) {
            // REVISIÓN LIDER APROBADA -> SIEMPRE PASA A REVISIÓN DE GERENCIA.
            // (Decisión de negocio 11-sep-2026: se elimina el umbral de
            // auto-aprobación por monto; toda nómina pasa por revisión.)
            $this->validate([
                'observaciones_revision_lider' => 'required|string|max:1000'
            ]);

            // Se envia notificación a los Gerentes
            $this->ocJuridicaRevisionGerencia($this->orden_nomina);

            $this->orden_nomina->observaciones_revision_lider = $this->observaciones_revision_lider;
            $messaje = 'Revisión Orden de compra APROBADA.';
            $redirect_route = 'ordenes-compra-lid';
        }
        elseif ($estado == 11) {
            // RECHAZO VALIDACIÓN LIDER DE PRODUCCIÓN
            $this->validate([
                'rechazo_revision_lider' => 'required|string|max:1000',
            ]);

            $this->orden_nomina->rechazo_revision_lider = $this->rechazo_revision_lider;

            // Se envia notificación al productor
            $this->ocJuridicaRechazoLiderProd($this->orden_nomina);

            $messaje = 'Revisión Orden de compra RECHAZADA.';
            $redirect_route = 'ordenes-compra-lid';
        }
        elseif ($estado == 12) {
            // RECHAZO REVISIÓN GERENCIA
            $this->validate([
                'rechazo_revision_gerencia' => 'required|string|max:1000',
            ]);

            $this->orden_nomina->rechazo_revision_gerencia = $this->rechazo_revision_gerencia;

            // Se envia notificación al productor
            $this->ocJuridicaRechazoGerencia($this->orden_nomina);

            $messaje = 'Revisión Orden de compra RECHAZADA.';
        }
        elseif ($estado == 13) {
            // RECHAZO REVISIÓN REMISIÓN (LIDER DE PRODUCCIÓN)
            $this->validate([
                'rechazo_revision_evidencias' => 'required|string|max:1000',
            ]);

            $this->orden_nomina->rechazo_revision_evidencias = $this->rechazo_revision_evidencias;

            // Se envia notificación al productor
            $this->ocJuridicaRechazoGerencia($this->orden_nomina);

            $messaje = 'Revisión Orden de compra RECHAZADA.';
            $redirect_route = 'ordenes-compra-lid';
        }
        elseif ($estado == 14) {
            $this->validate([
                'observaciones_remision' => 'nullable|string|max:1000'
            ]);

            $this->orden_nomina->observacion_remision = $this->observaciones_remision;

            // REVISIÓN REMISIÓN CONTROLLER APROBADA
            $messaje = 'Revisión Orden de compra APROBADA.';
        }

        $this->orden_nomina->estado_id = $estado;
        $this->orden_nomina->update();

        return redirect()->route($redirect_route)->with('success', $messaje);
    }
}
