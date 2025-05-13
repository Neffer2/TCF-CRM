<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\PresupuestoProyecto;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use App\Models\NaturalInfo;
use PhpParser\Node\Stmt\Return_;
use Livewire\WithFileUploads;

class Natural extends Component
{
    use WithFileUploads;

    // Models
    public $tercero, $nombre, $apellido, $correo, $cedula, $telefono, $ciudad, $banco,
            $search_nombre, $search_cedula, $search_telefono,
            $selected_item, $presupuesto, $item_presupuesto, $cantidad, $dias, $otros, $valor_unitario = 0, $valor_total = 0,
            $tipo_servicio, $tipo_contrato, $cod_oc, $oc_helisa;

    // Useful vars
    public $terceros, $ciudades, $items = [], $presupuestos = [], $items_presupuesto = [], $servicios = [], $bancos = [],
            $limiteCantidad, $limiteDias, $limiteOtros, $limiteValorUnitario, $limiteValorTotal,
            $queriedOrden;

    // Filled
    public $productor, $orden_id;

    /*
        * EVIDENCIAS
    */

    public function render()
    {
        $this->getTerceros();
        $this->getPresupuestos();
        return view('livewire.productor.ordenes.natural');
    }

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

        $this->terceros = Tercero::select('id', 'nombre', 'apellido', 'cedula', 'cert_bancaria', 'rut')->where($filtros)->get();
    }

    public function getPresupuestos(){
        // Trase solo presupuestos que tengan como proveedor: Cuenta de cobro
        $this->presupuestos = PresupuestoProyecto::with('presupuestoItems')->select('id', 'cod_cc')
                            ->where([['productor', $this->productor->id], ['estado_id', 1]])
                            ->whereHas('presupuestoItems', function ($item){
                                $item->select('id', 'cantidad', 'dia', 'otros', 'v_unitario', 'v_total', 'proveedor')
                                    ->where([['proveedor', 'a:1:{i:0;s:1:"3";}'], ['disponible', 1]]);
                            })
                            ->get();
    }

    /**
     * CRUD ITEMS OC *
    **/
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

        $newItem = [
            'proyecto' => [
                    'id' => $presupuesto->id,
                    'nombre' => $presupuesto->gestion->nom_proyecto_cot,
                    'cod_cc' => $presupuesto->cod_cc
                ],
            'item' => [
                'id' => $item->id,
                'nombre' => $item->descripcion,
                'cod_cc' => $presupuesto->cod_cc
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
                'cod_cc' => $presupuesto->cod_cc
            ],
            'cant' => $this->cantidad,
            'dias' => $this->dias,
            'otros' => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total' => $this->valor_total,
            'tipo_servicio' => $this->tipo_servicio,
            'tipo_contrato' => $this->tipo_contrato,
        ];

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

    public function deleteItem($itemId){
        unset($this->items[$itemId]);
    }
    /* * --------------------- * */

    /**
     *  UPLOAD OC
    **/
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

        // Presupuesto_id null, porque una orden de compra natural tiene varios presupuestos
        $orden = OrdenCompra::create([
            'tipo_oc' => 2,
            'estado_id' => 3,
            'presupuesto_id' => null,
            'proveedor_id' => 3,
        ]);

        $natural = NaturalInfo::create([
            'oc_id' => $orden->id,
            'tercero_id' => $tercero->id,
            'productor_id' => $this->productor->id
        ]);

        $OcItem = new OcItem();
        foreach ($this->items as $item){
            $OcItem->create([
                'oc_id' => $orden->id,
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
        return back()->with('success', 'Orden de compra creada correctamente');
    }
    /* * --------------------- * */

    /**
     *  Queried Data
    **/
    public function updateOC(){
        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items a la orden de compra');
            return back();
        }

        $this->validate([
            'tercero' => 'required'
        ]);

        // Presupuesto_id null, porque una orden de compra natural tiene varios presupuestos
        // TODO: Condición editar con y sin contrato. Con contrato = estado 2

        if ($this->queriedOrden->naturalInfo->contrato){
            $this->queriedOrden->update([
                'estado_id' => 7,
            ]);
        }

        // Elimina los items de la OC actual y crea los nuevos
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
                    'cod_cc' => $item->itemPresupuesto->presto->cod_cc
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

    public function deleteOrden(){
        $this->queriedOrden->ordenItems()->delete();
        $this->queriedOrden->naturalInfo()->delete();
        $this->queriedOrden->delete();

        return redirect()->route('ordenes-prod')->with('success', 'Orden de compra eliminada correctamente');
    }

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
    public function validateEvidencia($estado){
        if ($estado == 5) {
            $this->validate([
                'cod_oc' => 'required|string',
                'oc_helisa' => 'required|file|mimes:pdf|max:2048'
            ]);
            
            $this->queriedOrden->cod_oc = $this->cod_oc;
            $this->queriedOrden->archivo_orden_helisa = $this->oc_helisa->store('public/ordenes_naturales');
        }
        
        $this->queriedOrden->estado_id = $estado;
        $this->queriedOrden->update();

        return redirect()->route('ordenes-compra')->with('success', 'Validación exitosa');
    }

    /**
     *  UPDATES
    **/
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

    public function updatedPresupuesto(){
        $this->validate([
            'presupuesto' => 'required'
        ]);

        if ($this->presupuesto){
            $items_presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first()->presupuestoItems;
            $this->items_presupuesto = $items_presupuesto->where('proveedor', 'a:1:{i:0;s:1:"3";}')->where('disponible', 1);
        }else {
            $this->items_presupuesto = [];
        }

        $this->resetFields(['item_presupuesto', 'cantidad', 'dias', 'otros', 'valor_unitario', 'valor_total']);
    }

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

    public function updatedCantidad(){
        $this->cantidad = trim($this->cantidad);
        $this->cantidad = str_replace(",",'', $this->cantidad);

        $this->getItemLimite();

        $this->validate([
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad
        ]);

        $this->getValorTotal();
    }

    public function updatedDias(){
        $this->dias = trim($this->dias);
        $this->dias = str_replace(",",'', $this->dias);
        $this->validate([
            'dias' => 'required|numeric|min: 1|max:'.$this->limiteDias
        ]);

        $this->getValorTotal();
    }

    public function updatedOtros(){
        $this->otros = trim($this->otros);
        $this->otros = str_replace(",",'', $this->otros);
        $this->validate([
            'otros' => 'required|numeric|min: 1|max:'.$this->limiteOtros
        ]);

        $this->getValorTotal();
    }

    public function updatedValorUnitario(){
        $this->valor_unitario = trim($this->valor_unitario);
        $this->valor_unitario = str_replace(",",'', $this->valor_unitario);
        $this->validate([
            'valor_unitario' => 'required|numeric|min: 1|max:'.$this->limiteValorUnitario
        ]);

        $this->getValorTotal();
    }

    public function updatedValorTotal(){
        $this->validate([
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal
        ]);

        $this->getValorTotal();
    }

    public function getValorTotal(){
        $this->valor_total = ($this->cantidad  * $this->valor_unitario);
    }

    /* * --------------------- * */

    /*
        * RESET FIELDS
        * @params array $fields
    */
    public function resetFields($fields){
        foreach ($fields as $field){
            $this->$field = '';
        }
    }
}
