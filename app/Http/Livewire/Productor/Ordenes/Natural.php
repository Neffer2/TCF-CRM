<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\PresupuestoProyecto;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use App\Models\NaturalInfo;
use App\Traits\SMS;
use App\Traits\Email;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Natural extends Component
{
    // Incluye traits para subir archivos, paginación y SMS personalizados
    use WithFileUploads, WithPagination, SMS, Email, \App\Traits\GestionaEvidenciasNatural;

    // Variables públicas para modelos y datos del formulario
    public $tercero, $nombre, $apellido, $correo, $cedula, $telefono, $ciudad, $banco,
            $search_nombre, $search_cedula, $search_telefono,
            $selected_item, $presupuesto, $item_presupuesto, $cantidad, $dias, $otros, $valor_unitario = 0, $valor_total = 0,
            $tipo_servicio, $tipo_contrato, $cod_oc, $gr, $oc_helisa, $justificacion_rechazo, $justificacion_productor, $toggleRechazo = false,
            $porcentaje;

    // Variables útiles para almacenar colecciones y límites
    public $terceros = [], $ciudades, $items = [], $presupuestos = [], $items_presupuesto = [], $servicios = [], $bancos = [],
            $limiteCantidad, $limiteDias, $limiteOtros, $limiteValorUnitario, $limiteValorTotal,
            $queriedOrden;

    // Variables para identificar el productor y la orden actual
    public $productor, $orden_id;

    public $es_orden_trabajo = false;

    public function exportarAnticipoPdf(int $anticipoId)
    {
        abort_unless((int) auth()->user()?->rol === 1, 403);
        $orden = OrdenCompra::findOrFail($this->orden_id);
        abort_unless((int) $orden->tipo_oc === 4, 403);
        $anticipo = $orden->anticipos()->findOrFail($anticipoId);

        return \App\Exports\AnticipoOrdenes::downloadPdf($anticipo->id);
    }

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

        if ($this->orden_id) {
            $this->queriedOrden = OrdenCompra::find($this->orden_id);
            $this->getData();
            $this->cod_oc = $this->queriedOrden->cod_oc ?: $this->queriedOrden->codigoOC();
            $this->gr     = $this->queriedOrden->gr ?: $this->queriedOrden->codigoGR();
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
                'display_item' => $item->num_item
            ],
            'tipo_servicio' => $this->tipo_servicio,
            'tipo_contrato' => $this->tipo_contrato,
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
                'display_item' => $item->num_item
            ],
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

    public function enviarAprobacion()
    {
        $queriedOrden = OrdenCompra::where('id', $this->orden_id)->first();

        if ($queriedOrden) {
            $queriedOrden->estado_id = 8;
            $queriedOrden->save();

            // Alerta usando SweetAlert y pasando el ID de la orden
            $this->dispatchBrowserEvent('swal', [
                'title' => '¡Éxito!',
                'text' => 'Envia a líder de producción para aprobación con el número de orden ' . $queriedOrden->id,
                'icon' => 'success',
            ]);
        }
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
    public function uploadOC()
    {
        if ($this->items->count() <= 0) {
            $this->addError('items-error', 'No se han agregado items a la orden de compra');
            return back();
        }

        $this->validate([
            'tercero' => [Rule::requiredIf(fn () => ! $this->es_orden_trabajo)],
            'justificacion_productor' => 'required|string|max:255',
            'porcentaje' => $this->es_orden_trabajo ? ['nullable'] : ['required', 'numeric', 'between:0,100', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        $tercero = null;

        if (!$this->es_orden_trabajo && $this->tercero) {
            $tercero = Tercero::where('id', $this->tercero)->first();

            if ($tercero) {
                $tercero->update([
                    'nombre'   => $this->nombre,
                    'apellido' => $this->apellido,
                    'cedula'   => $this->cedula,
                    'correo'   => $this->correo,
                    'telefono' => $this->telefono,
                    'ciudad'   => $this->ciudad,
                    'banco'    => $this->banco,
                ]);
            }
        }

        // Si está marcado: tipo_oc = 4 y se guarda el ID de quien lo solicita
        $orden = OrdenCompra::create([
            'tipo_oc'               => $this->es_orden_trabajo ? 4 : 2,
            'estado_id'             => $this->es_orden_trabajo ? 3 : 8,
            'justificacion_productor' => $this->justificacion_productor,
            'proveedor_id'          => 3,
            'porcentaje'            => $this->es_orden_trabajo ? null : $this->porcentaje,
        ]);

        NaturalInfo::create([
            'oc_id'        => $orden->id,
            'tercero_id'   => $tercero?->id,
            'productor_id' => auth()->id(),
        ]);

        $OcItem = new OcItem();
        foreach ($this->items as $item) {
            $OcItem->create([
                'oc_id'         => $orden->id,
                'item_id'       => $item['item']['id'],
                'desc_oc'       => $item['item']['nombre'],
                'cant_oc'       => $item['cant'],
                'dias_oc'       => $item['dias'],
                'otros_oc'      => $item['otros'],
                'vunit_oc'      => $item['valor_unitario'],
                'vtotal_oc'     => $item['valor_total'],
                'tipo_servicio' => $item['tipo_servicio'],
                'tipo_contrato' => $item['tipo_contrato'],
            ]);
        }

        if ($tercero && $tercero->telefono) {
            $this->oc_natura_creada($tercero, $orden->id);
        }

        $this->resetFields([
            'presupuesto',
            'item_presupuesto',
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
            'es_orden_trabajo',
            'porcentaje',
        ]);

        unset($this->tercero);
        unset($this->selected_item);
        $this->items = collect();

        return redirect()->route('orden-natural-prod', ['orden_id' => $orden->id])
            ->with('success', (int) $orden->tipo_oc === 4 ? 'Orden de empleado creada. Puedes editarla y solicitar un anticipo.' : 'Orden enviada a líder de producción para aprobación');
    }
    /* * --------------------- * */

    /**
     *  Queried Data
    **/
    // Actualiza una orden de compra existente con los nuevos items y estado
    protected function guardarOrdenEmpleado()
    {
        $this->validate([
            'items' => 'required|min:1',
            'justificacion_productor' => 'required|string|max:1000',
        ]);
        \Illuminate\Support\Facades\DB::transaction(function () {
            $orden = OrdenCompra::lockForUpdate()->findOrFail($this->queriedOrden->id);
            abort_unless((int) $orden->tipo_oc === 4 && (int) $orden->naturalInfo->productor_id === (int) auth()->id(), 403);
            if ($orden->anticipos()->where('estado_id', '!=', 6)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['items-error' => 'La orden tiene un anticipo en trámite.']);
            }
            $orden->update(['justificacion_productor' => $this->justificacion_productor]);
            $orden->ordenItems()->delete();
            foreach ($this->items as $item) {
                $orden->ordenItems()->create([
                    'item_id' => $item['item']['id'],
                    'desc_oc' => $item['item']['nombre'],
                    'cant_oc' => $item['cant'], 'dias_oc' => $item['dias'], 'otros_oc' => $item['otros'],
                    'vunit_oc' => $item['valor_unitario'], 'vtotal_oc' => $item['valor_total'],
                    'tipo_servicio' => $item['tipo_servicio'], 'tipo_contrato' => $item['tipo_contrato'],
                ]);
            }
        });
        return redirect()->route('orden-natural-prod', ['orden_id' => $this->queriedOrden->id])->with('success', 'Orden de empleado guardada.');
    }

    public function updateOC(){
        if ((int) $this->queriedOrden->tipo_oc === 4) {
            return $this->guardarOrdenEmpleado();
        }
        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items a la orden de compra');
            return back();
        }

        $tieneContrato = $this->queriedOrden->naturalInfo->contrato;

        $this->validate([
            'tercero' => 'required',
            'justificacion_productor' => ($tieneContrato ? 'nullable' : 'required') . '|string|max:1000',
            'porcentaje' => ['required', 'numeric', 'between:0,100', 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        $this->queriedOrden->update([
            'justificacion_productor' => $this->justificacion_productor,
            'porcentaje' => $this->porcentaje,
        ]);

        // Si la orden tiene contrato, cambia el estado y envía mensaje
        if ($this->queriedOrden->naturalInfo->contrato && $this->queriedOrden->evidencias->isEmpty()){
            $this->queriedOrden->update([
                'estado_id' => 7,
            ]);

            if ($this->queriedOrden->naturalInfo->tercero->telefono){
                $this->oc_evidencias($this->queriedOrden->naturalInfo->tercero, $this->queriedOrden->id);
            }
        }else {
            // Si la orden de compra ya tiene evidencias, actualiza el estado a 8:  Lider de produccion
            $this->queriedOrden->update([
                'estado_id' => 8,
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
                'desc_oc' => $item['item']['nombre'],
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

    /* * --------------------- * */

    /**
     *  Queried Data
    **/
    // Carga los datos de una orden existente para edición
    public function getData(){
        $this->es_orden_trabajo = (int) $this->queriedOrden->tipo_oc === 4;
        // Productor
        $this->productor = $this->queriedOrden->naturalInfo->productor;
        // Tercero
        $queriedTercero = $this->es_orden_trabajo
            ? $this->productor
            : $this->queriedOrden->naturalInfo->tercero;
        $this->tercero = $this->es_orden_trabajo ? null : $queriedTercero?->id;
        $this->nombre = $this->es_orden_trabajo ? $queriedTercero?->name : $queriedTercero?->nombre;
        $this->apellido = $this->es_orden_trabajo ? '' : $queriedTercero?->apellido;
        $this->correo = $this->es_orden_trabajo ? $queriedTercero?->email : $queriedTercero?->correo;
        $this->cedula = $queriedTercero?->cedula;
        $this->telefono = $queriedTercero?->telefono;
        $this->ciudad = $queriedTercero?->ciudad;
        $this->banco = $queriedTercero?->banco;
        $this->justificacion_productor = $this->queriedOrden->justificacion_productor;
        $this->porcentaje = $this->queriedOrden->porcentaje;
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
                    'display_item' => $item->itemPresupuesto->num_item
                ],
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
}
