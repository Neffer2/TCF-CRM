<?php

namespace App\Http\Livewire\Productor\Ordenes;

use App\Services\PdfService;
use Livewire\Component;
use App\Models\Tercero;
use App\Models\PresupuestoProyecto;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use App\Models\NaturalInfo;
use App\Traits\SMS;
use App\Traits\Email;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Return_;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Natural extends Component
{
    // Incluye traits para subir archivos, paginación y SMS personalizados
    use WithFileUploads, WithPagination, SMS, Email;

    // Variables públicas para modelos y datos del formulario
    public $tercero, $nombre, $apellido, $correo, $cedula, $telefono, $ciudad, $banco,
            $search_nombre, $search_cedula, $search_telefono,
            $selected_item, $presupuesto, $item_presupuesto, $descripcion, $cantidad, $dias, $otros, $valor_unitario = 0, $valor_total = 0,
            $tipo_servicio, $tipo_contrato, $cod_oc, $oc_helisa, $justificacion_rechazo, $toggleRechazo = false,
            $observaciones_revision_lider, $rechazo_revision_lider, $observaciones_revision_gerencia, $rechazo_revision_gerencia,
            $observaciones_revision_evidencias, $rechazo_revision_evidencias;

    // Variables útiles para almacenar colecciones y límites
    public $terceros = [], $ciudades, $items = [], $presupuestos = [], $items_presupuesto = [], $servicios = [], $bancos = [],
            $limiteCantidad, $limiteDias, $limiteOtros, $limiteValorUnitario, $limiteValorTotal,
            $queriedOrden;

    // Variables para identificar el productor y la orden actual
    public $productor, $orden_id;

    /*
        * EVIDENCIAS
    */

    // Renderiza la vista y carga terceros y presupuestos
    public function render()
    {
        $this->getTerceros();
        $this->getPresupuestos();
        return view('livewire.productor.ordenes.natural');
    }

    // Inicializa variables y carga datos si hay una orden existente
    public function mount(){
        $this->items = collect();
        $this->ciudades = app('ciudades');
        $this->servicios = app('servicios');
        $this->bancos = app('bancos');

        if ($this->orden_id){
            $this->queriedOrden = OrdenCompra::where('id', $this->orden_id)->first();
            $this->getData();
        }
    }

    // Obtiene la lista de terceros filtrando por estado y búsqueda
    public function getTerceros(){
        $filtros = [];
        array_push($filtros, ['estado', 1]);

        if ($this->search_cedula){
            array_push($filtros, ['cedula', 'like', '%' . $this->search_cedula . '%']);
        }

        if ($this->search_nombre){
            array_push($filtros, ['nombre', 'like', '%' . $this->search_nombre . '%']);
        }

        if ($this->search_telefono){
            array_push($filtros, ['telefono', 'like', '%' . $this->search_telefono . '%']);
        }

        return $this->terceros = Tercero::select('id', 'nombre', 'apellido', 'cedula', 'cert_bancaria', 'rut')->where($filtros)->limit(50)->get();
    }

    // Obtiene presupuestos del productor que tengan items disponibles para cuenta de cobro
    public function getPresupuestos(){
        $this->presupuestos = PresupuestoProyecto::with('presupuestoItems')->select('id', 'cod_cc')
                            ->where([['productor', $this->productor->id], ['estado_id', 1]])
                            ->whereHas('presupuestoItems', function ($item){
                                $item->select('id', 'cantidad', 'dia', 'otros', 'v_unitario', 'v_total', 'proveedor')
                                    ->where([['proveedor', 'LIKE', '%s:1:"3"%'], ['disponible', 1]]);
                            })
                            ->get();
    }

    /**
     * CRUD ITEMS OC *
    **/
    // Agrega un nuevo item a la orden de compra
    public function newItem(){
        $this->validate([
            'presupuesto' => 'required',
            'item_presupuesto' => 'required',
            'descripcion' => 'required|string',
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad,
            'dias' => 'required|numeric|min: 1|max:'.$this->limiteDias,
            'otros' => 'required|numeric|min: 1|max:'.$this->limiteOtros,
            'valor_unitario' => 'required|min: 1numeric|max:'.$this->limiteValorUnitario,
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal,
            'tipo_servicio' => 'required|string',
            'tipo_contrato' => 'required|string',
        ]);

        $presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first();
        $item = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();

        // Crea el nuevo item con los datos seleccionados
        $newItem = [
            'proyecto' => [
                    'id' => $presupuesto->id,
                    'nombre' => $presupuesto->gestion->nom_proyecto_cot,
                    'cod_cc' => $presupuesto->cod_cc
                ],
            'item' => [
                'id' => $item->id,
                'nombre' => $item->descripcion,
                'cod_cc' => $presupuesto->cod_cc,
                'display_item' => $item->displayItem()
            ],
            'tipo_servicio' => $this->tipo_servicio,
            'tipo_contrato' => $this->tipo_contrato,
            'desc' => $this->descripcion,
            'cant' => $this->cantidad,
            'dias' => $this->dias,
            'otros' => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total' => $this->valor_total,
        ];

        $this->items->push($newItem);
        // Limpia los campos del formulario
        $this->resetFields([
            'presupuesto',
            'item_presupuesto',
            'descripcion',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'tipo_servicio',
            'tipo_contrato',
        ]);
    }

    // Carga los datos de un item seleccionado para editar
    public function getItem($itemId){
        $this->selected_item = $itemId;
        $item = $this->items[$itemId];

        $this->presupuesto = $item['proyecto']['id'];
        $this->updatedPresupuesto();

        $this->item_presupuesto = $item['item']['id'];
        $this->descripcion = $item['desc'];
        $this->cantidad = $item['cant'];
        $this->dias = $item['dias'];
        $this->otros = $item['otros'];
        $this->valor_unitario = $item['valor_unitario'];
        $this->valor_total = $item['valor_total'];
        $this->tipo_servicio = $item['tipo_servicio'];
        $this->tipo_contrato = $item['tipo_contrato'];
    }

    // Edita un item existente en la orden de compra
    public function actionEdit(){
        $this->validate([
            'presupuesto' => 'required',
            'item_presupuesto' => 'required',
            'descripcion' => 'required|string',
            'cantidad' => 'required',
            'dias' => 'required',
            'otros' => 'required',
            'valor_unitario' => 'required',
            'valor_total' => 'required',
            'tipo_servicio' => 'required|string',
            'tipo_contrato' => 'required|string',
        ]);

        $presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first();
        $item = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();
        $this->items[$this->selected_item] = [
            'proyecto' => [
                    'id' => $presupuesto->id,
                    'nombre' => $presupuesto->gestion->nom_proyecto_cot,
                    'cod_cc' => $presupuesto->cod_cc
                ],
            'item' => [
                'id' => $item->id,
                'nombre' => $item->descripcion,
                'cod_cc' => $presupuesto->cod_cc,
                'display_item' => $item->displayItem()
            ],
            'desc' => $this->descripcion,
            'cant' => $this->cantidad,
            'dias' => $this->dias,
            'otros' => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total' => $this->valor_total,
            'tipo_servicio' => $this->tipo_servicio,
            'tipo_contrato' => $this->tipo_contrato,
        ];

        // Limpia los campos y deselecciona el item
        $this->resetFields([
            'presupuesto',
            'item_presupuesto',
            'descripcion',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'tipo_servicio',
            'tipo_contrato',
        ]);

        unset($this->selected_item);
    }

    // Elimina un item de la orden de compra
    public function deleteItem($itemId){
        unset($this->items[$itemId]);
    }
    /* * --------------------- * */

    /**
     *  UPLOAD OC
    **/
    // Crea una nueva orden de compra con los items seleccionados
    public function uploadOC(){
        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items a la orden de compra');
            return back();
        }

        $this->validate([
            'tercero' => 'required'
        ]);

        $tercero = Tercero::where('id', $this->tercero)->first();
        $tercero->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'cedula' => $this->cedula,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
            'ciudad' => $this->ciudad,
            'banco' => $this->banco
        ]);

        // Crea la orden de compra (tipo natural)
        $orden = OrdenCompra::create([
            'tipo_oc' => 2,
            'estado_id' => 8,
//            'estado_id' => 3,
            'presupuesto_id' => null,
            'proveedor_id' => 3,
        ]);

        // Crea la información adicional de la orden natural
        $natural = NaturalInfo::create([
            'oc_id' => $orden->id,
            'tercero_id' => $tercero->id,
            'productor_id' => $this->productor->id
        ]);

        // Crea los items de la orden
        $OcItem = new OcItem();
        foreach ($this->items as $item){
            $OcItem->create([
                'oc_id' => $orden->id,
                'item_id' => $item['item']['id'],
                'desc_oc' => $item['desc'],
                'cant_oc' => $item['cant'],
                'dias_oc' => $item['dias'],
                'otros_oc' => $item['otros'],
                'vunit_oc' => $item['valor_unitario'],
                'vtotal_oc' => $item['valor_total'],
                'tipo_servicio' => $item['tipo_servicio'],
                'tipo_contrato' => $item['tipo_contrato'],
            ]);
        }

        // Envía mensaje al tercero si tiene teléfono
//        if ($tercero->telefono){ $this->oc_natura_creada($tercero, $orden->id); }

        // Limpia todos los campos y colecciones
        $this->resetFields([
            'presupuesto',
            'item_presupuesto',
            'descripcion',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'tipo_servicio',
            'tipo_contrato',
            'tercero',
            'nombre',
            'apellido',
            'correo',
            'cedula',
            'telefono',
            'ciudad',
            'banco',
        ]);

        unset($this->tercero);
        unset($this->selected_item);
        $this->items = collect();
        return back()->with('success', 'Orden de compra creada correctamente');
    }
    /* * --------------------- * */

    /**
     *  Queried Data
    **/
    // Actualiza una orden de compra existente con los nuevos items y estado
    public function updateOC(){
        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items a la orden de compra');
            return back();
        }

        $this->validate([
            'tercero' => 'required'
        ]);

        // Si la orden tiene contrato, cambia el estado y envía mensaje
        if ($this->queriedOrden->naturalInfo->contrato && $this->queriedOrden->evidencias->isEmpty()){
            $this->queriedOrden->update([
                'estado_id' => 7,
            ]);

            if ($this->queriedOrden->naturalInfo->tercero->telefono){
                $this->oc_evidencias($this->queriedOrden->naturalInfo->tercero, $this->queriedOrden->id);
            }
        }
        else {
            // Si la orden de compra ya tiene evidencias, actualiza el estado a 2: Revisión controller
            $this->queriedOrden->update([
                'estado_id' => 2,
                'fecha_envio_produccion' => now()
            ]);

            //Mail notificación controller
            $this->ocNaturalRevisionController($this->queriedOrden);
        }

        // Elimina los items actuales y crea los nuevos
        foreach ($this->queriedOrden->ordenItems as $item){
            $item->delete();
        }

        $OcItem = new OcItem();
        foreach ($this->items as $item){
            $OcItem->create([
                'oc_id' => $this->queriedOrden->id,
                'item_id' => $item['item']['id'],
                'desc_oc' => $item['desc'],
                'cant_oc' => $item['cant'],
                'dias_oc' => $item['dias'],
                'otros_oc' => $item['otros'],
                'vunit_oc' => $item['valor_unitario'],
                'vtotal_oc' => $item['valor_total'],
                'tipo_servicio' => $item['tipo_servicio'],
                'tipo_contrato' => $item['tipo_contrato'],
            ]);
        }

        // Limpia los campos y colecciones
        $this->resetFields([
            'presupuesto',
            'item_presupuesto',
            'descripcion',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'tipo_servicio',
            'tipo_contrato',
            'tercero',
            'nombre',
            'apellido',
            'correo',
            'cedula',
            'telefono',
            'ciudad',
            'banco',
        ]);

        unset($this->tercero);
        unset($this->selected_item);
        $this->items = collect();
        return redirect()->route('ordenes-prod')->with('success', 'Información guardada correctamente');
    }

    /**
     * Guarda las gestiones de revisión del Lider de producción, Gerencia y los cambios realizados por el
     * productor cuando existe un rechazo
     * @param $estado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revisionOC($estado) {
        $redirect_route = 'ordenes-compra-lid';

        if ($estado == 3) {
            // REVISIÓN DE GERENCIA APROBADA
            $this->validate([
                'observaciones_revision_gerencia' => 'required|string|max:1000'
            ]);

            $this->queriedOrden->observaciones_revision_gerencia = $this->observaciones_revision_gerencia;

            // Envía mensaje al tercero si tiene teléfono
            $tercero = Tercero::where('id', $this->tercero)->first();
//                if ($tercero->telefono){ $this->oc_natura_creada($tercero, $this->queriedOrden->id); }

            $redirect_route = 'ordenes-compra';
        }
        elseif ($estado == 8) {
            // GUARDAR AJUSTES REALIZADOS A LA OC POR PARTE DEL PRODUCTOR (CUANDO LA OC ES RECHAZADA POR EL LIDER DE PRODUCCIÓN O GERENCIA)

            // Elimina los items actuales y crea los nuevos
            foreach ($this->queriedOrden->ordenItems as $item){
                $item->delete();
            }

            $OcItem = new OcItem();
            foreach ($this->items as $item){
                $OcItem->create([
                    'oc_id' => $this->queriedOrden->id,
                    'item_id' => $item['item']['id'],
                    'desc_oc' => $item['desc'],
                    'cant_oc' => $item['cant'],
                    'dias_oc' => $item['dias'],
                    'otros_oc' => $item['otros'],
                    'vunit_oc' => $item['valor_unitario'],
                    'vtotal_oc' => $item['valor_total'],
                    'tipo_servicio' => $item['tipo_servicio'],
                    'tipo_contrato' => $item['tipo_contrato'],
                ]);
            }

            $redirect_route = 'ordenes-compra-prod';
        }
        elseif ($estado == 9) {
            // REVISIÓN LIDER APROBADA
            $this->validate([
                'observaciones_revision_lider' => 'required|string|max:1000'
            ]);

            // CALCULAMOS EL VALOR TOTAL DE LA OC
            $vtotal_oc = 0;
            foreach ($this->items as $item) {
                $vtotal_oc += $item['valor_total'];
            }

            // SI EL VALOR TOTAL DE LA OC ES MENOR A 5.000.000, SE CAMBIA A ESTADO EDITABLE ($estado_id = 3),
            // DE LO CONTRARIO, SE ENVIA A REVISIÓN DE GERENCIA (estado_id = 9)
            if ($vtotal_oc < 5000000) {
                $estado = 3;

                // Envía mensaje al tercero si tiene teléfono
                $tercero = Tercero::where('id', $this->tercero)->first();
//                if ($tercero->telefono){ $this->oc_natura_creada($tercero, $this->queriedOrden->id); }
            }

            $this->queriedOrden->observaciones_revision_lider = $this->observaciones_revision_lider;
        }
        elseif ($estado == 11) {
            // RECHAZO VALIDACIÓN LIDER DE PRODUCCIÓN
            $this->validate([
                'rechazo_revision_lider' => 'required|string|max:1000',
            ]);

            $this->queriedOrden->rechazo_revision_lider = $this->rechazo_revision_lider;
        }
        elseif ($estado == 12) {
            // RECHAZO REVISIÓN GERENCIA
            $this->validate([
                'rechazo_revision_gerencia' => 'required|string|max:1000',
            ]);

            $this->queriedOrden->rechazo_revision_gerencia = $this->rechazo_revision_gerencia;
        }

        $this->queriedOrden->estado_id = $estado;
        $this->queriedOrden->update();

        return redirect()->route($redirect_route)->with('success', 'Información guardada correctamente');
    }

    /* * --------------------- * */

    /**
     *  Queried Data
    **/
    // Carga los datos de una orden existente para edición
    public function getData(){
        // Productor
        $this->productor = $this->queriedOrden->naturalInfo->productor;
        // Tercero
        $queriedTercero = $this->queriedOrden->naturalInfo->tercero;
        $this->tercero = $queriedTercero->id;
        $this->nombre = $queriedTercero->nombre;
        $this->apellido = $queriedTercero->apellido;
        $this->correo = $queriedTercero->correo;
        $this->cedula = $queriedTercero->cedula;
        $this->telefono = $queriedTercero->telefono;
        $this->ciudad = $queriedTercero->ciudad;
        $this->banco = $queriedTercero->banco;
        // Items
        $queriedItems = $this->queriedOrden->ordenItems;
        foreach ($queriedItems as $item){
            $newItem = [
                'proyecto' => [
                        'id' => $item->itemPresupuesto->presto->id,
                        'nombre' => $item->itemPresupuesto->presto->gestion->nom_proyecto_cot,
                        'cod_cc' => $item->itemPresupuesto->presto->cod_cc
                    ],
                'item' => [
                    'id' => $item->itemPresupuesto->id,
                    'nombre' => $item->itemPresupuesto->descripcion,
                    'cod_cc' => $item->itemPresupuesto->presto->cod_cc,
                    'display_item' => $item->itemPresupuesto->displayItem()
                ],
                'desc' => $item->desc_oc,
                'cant' => $item->cant_oc,
                'dias' => $item->dias_oc,
                'otros' => $item->otros_oc,
                'valor_unitario' => $item->vunit_oc,
                'valor_total' => $item->vtotal_oc,
                'tipo_servicio' => $item->tipo_servicio,
                'tipo_contrato' => $item->tipo_contrato,
            ];

            $this->items->push($newItem);
        }

        if ($this->queriedOrden->estado_id == 2) {
            $this->cod_oc = "OC" . $this->queriedOrden->id;
        }
    }

    // Elimina una orden de compra y sus relaciones
    public function deleteOrden(){
        $this->queriedOrden->ordenItems()->delete();
        $this->queriedOrden->naturalInfo()->delete();
        $this->queriedOrden->evidencias()->delete();
        $this->queriedOrden->delete();

        return redirect()->route('ordenes-prod')->with('success', 'Orden de compra eliminada correctamente');
    }

    // Calcula los límites de cantidad y valores para el item seleccionado
    public function getItemLimite(){
        // Trae información del item seleccionado
        $item_info = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();

        /* LIMITES: LOS LIMITES OMITEN LOS ITEMS DE LA OC ACTUAL SI ES ACTUALIZACIÓN */
        if($this->queriedOrden){
            $this->limiteCantidad = (($item_info->cantidad * $item_info->dia * $item_info->otros) - $item_info->consumidos()->where('oc_id', '!=', $this->queriedOrden->id)->get()->sum('cant_oc'));
            $this->limiteValorTotal = ($item_info->v_total - $item_info->consumidos()->where('oc_id', '!=', $this->queriedOrden->id)->get()->sum('vtotal_oc'));
        }else {
            $this->limiteCantidad = (($item_info->cantidad * $item_info->dia * $item_info->otros) - $item_info->consumidos()->get()->sum('cant_oc'));
            $this->limiteValorTotal = ($item_info->v_total - $item_info->consumidos()->get()->sum('vtotal_oc'));
        }

        $this->limiteDias = $item_info->dia;
        $this->limiteOtros = $item_info->otros;
        $this->limiteValorUnitario = $item_info->v_unitario;
    }

    /* * --------------------- * */

    /*
        * EVIDENCIAS
    */
    // Valida y sube el archivo de evidencia para la orden de compra
    public function validateEvidencia($estado, PdfService $pdfService){
        if ($estado == 5) {
            $this->queriedOrden->cod_oc = "OC".$this->queriedOrden->id;

            // CREA EL PDF DE LA OC
            $crear_pdf_oc = $pdfService->generarPdfOC($this->queriedOrden, "public/ordenes_naturales");
            // CREA EL PDF DE LA CUENTA DE COBRO
            $pdf_cco = $pdfService->generarPdfCCO($this->queriedOrden, "public/cuentas_cobro");

            $this->queriedOrden->archivo_orden_helisa = $crear_pdf_oc;
            $this->queriedOrden->fecha_aprobacion = now();
//             $this->ocNaturalRevisionContabilidad($this->queriedOrden);
//            $this->ocNaturalEnvioCuentaCobro($this->queriedOrden, "public/cuentas_cobro/cuenta_cobro_" . $this->queriedOrden->id . ".pdf");
        } elseif ($estado == 7) {
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:255'
            ]);

            $this->queriedOrden->justificacion_rechazo = $this->justificacion_rechazo;
            $this->oc_evidencias_rechazadas($this->queriedOrden);
            if (Auth()->user()->rol == 1){
                $this->ocNaturalEvidenciasRechazadas($this->queriedOrden);
            }
        }

        $this->queriedOrden->estado_id = $estado;
        $this->queriedOrden->update();

        if (Auth()->user()->rol == 1) {
            return redirect()->route('ordenes-compra')->with('success', 'Validación exitosa');
        }elseif (Auth()->user()->rol == 7) {
            return redirect()->route('ordenes-prod')->with('success', 'Validación exitosa');
        }
    }

    /**
     *  UPDATES
    **/
    // Cuando se actualiza el tercero, carga sus datos en el formulario
    public function updatedTercero(){
        $this->validate([
            'tercero' => 'required'
        ]);

        if ($this->tercero){
            $tercero = $this->terceros->where('id', $this->tercero)->first();
            $this->nombre = $tercero->nombre;
            $this->apellido = $tercero->apellido;
            $this->correo = $tercero->correo;
            $this->cedula = $tercero->cedula;
            $this->telefono = $tercero->telefono;
            $this->ciudad = $tercero->ciudad;
            $this->banco = $tercero->banco;
        }

        $this->items = collect();
    }

    // Cuando se actualiza el presupuesto, carga los items disponibles
    public function updatedPresupuesto(){
        $this->validate([
            'presupuesto' => 'required'
        ]);

        if ($this->presupuesto){
            $items_presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first()->presupuestoItems;
            $this->items_presupuesto = $items_presupuesto->filter(function($item) {
                return Str::contains($item->proveedor, 's:1:"3"') && $item->disponible == 1;
            });
        }else {
            $this->items_presupuesto = [];
        }

        $this->resetFields(['item_presupuesto', 'cantidad', 'dias', 'otros', 'valor_unitario', 'valor_total']);
    }

    // Cuando se selecciona un item de presupuesto, valida duplicados y carga límites
    public function updatedItemPresupuesto(){
        $this->validate([
            'item_presupuesto' => 'required'
        ]);

        foreach ($this->items as $item){
            if ($item['item']['id'] == $this->item_presupuesto){
                $this->addError('items-error', 'Este item ya fue agregado en esta orden de compra.');
                return back();
            }
        }

        if ($this->item_presupuesto){
            $this->getItemLimite();

            $this->cantidad = $this->limiteCantidad;
            $this->dias = $this->limiteDias;
            $this->otros = $this->limiteOtros;
            $this->valor_unitario = $this->limiteValorUnitario;
            $this->valor_total = $this->limiteValorTotal;
        }else{
            $this->items_presupuesto = [];
        }
    }

    // Cuando se actualiza la cantidad, recalcula límites y el valor total
    public function updatedCantidad(){
        $this->cantidad = trim($this->cantidad);
        $this->cantidad = str_replace(",",'', $this->cantidad);

        $this->getItemLimite();

        $this->validate([
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad
        ]);

        $this->getValorTotal();
    }

    // Cuando se actualizan los días, recalcula el valor total
    public function updatedDias(){
        $this->dias = trim($this->dias);
        $this->dias = str_replace(",",'', $this->dias);
        $this->validate([
            'dias' => 'required|numeric|min: 1|max:'.$this->limiteDias
        ]);

        $this->getValorTotal();
    }

    // Cuando se actualizan los otros, recalcula el valor total
    public function updatedOtros(){
        $this->otros = trim($this->otros);
        $this->otros = str_replace(",",'', $this->otros);
        $this->validate([
            'otros' => 'required|numeric|min: 1|max:'.$this->limiteOtros
        ]);

        $this->getValorTotal();
    }

    // Cuando se actualiza el valor unitario, recalcula el valor total
    public function updatedValorUnitario(){
        $this->valor_unitario = trim($this->valor_unitario);
        $this->valor_unitario = str_replace(",",'', $this->valor_unitario);
        $this->validate([
            'valor_unitario' => 'required|numeric|min: 1|max:'.$this->limiteValorUnitario
        ]);

        $this->getValorTotal();
    }

    // Cuando se actualiza el valor total, lo valida y recalcula
    public function updatedValorTotal(){
        $this->validate([
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal
        ]);

        $this->getValorTotal();
    }

    // Calcula el valor total del item
    public function getValorTotal(){
        $this->valor_total = ($this->cantidad  * $this->valor_unitario);
    }

    /* * --------------------- * */

    /*
        * TOOLS
        * @params array $fields
    */
    // Limpia los campos especificados
    public function resetFields($fields){
        foreach ($fields as $field){
            $this->$field = '';
        }
    }

    public function toggleRechazo()
    {
        $this->toggleRechazo = !$this->toggleRechazo;
    }
}
