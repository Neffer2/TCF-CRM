<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\PresupuestoProyecto;
use App\Models\OrdenCompra; 
use App\Models\OcItem;
use App\Models\NaturalInfo;
use PhpParser\Node\Stmt\Return_;

class Natural extends Component
{
    // Models
    public $tercero, $nombre, $apellido, $correo, $cedula, $telefono, $ciudad, $banco,
            $search_nombre, $search_cedula, $search_telefono,
            $selected_item, $presupuesto, $item_presupuesto, $cantidad, $dias, $otros, $valor_unitario = 0, $valor_total = 0;

    // Useful vars 
    public $terceros, $ciudades, $items = [], $presupuestos = [], $items_presupuesto = [],
            $limiteCantidad, $limiteDias, $limiteOtros, $limiteValorUnitario, $limiteValorTotal;

    // Filled
    public $productor;

    public function render()
    {        
        $this->getTerceros();
        $this->getPresupuestos();
        return view('livewire.productor.ordenes.natural');
    }

    public function mount(){
        $this->items = collect();
        $this->ciudades = app('ciudades');
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

        $this->terceros = Tercero::select('id', 'nombre', 'apellido', 'cedula')->where($filtros)->get();
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
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal
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
            'valor_total'
        ]);
    }

    public function getItem($itemId){
        $this->selected_item = $itemId;

        $item = $this->items[$itemId];
        $this->presupuesto = $item['proyecto']['id'];
        $this->item_presupuesto = $item['item']['id'];
        $this->cantidad = $item['cant'];
        $this->dias = $item['dias'];
        $this->otros = $item['otros'];
        $this->valor_unitario = $item['valor_unitario'];
        $this->valor_total = $item['valor_total'];
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
        ];

        $this->resetFields([
            'presupuesto',
            'item_presupuesto',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total'
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

        $orden = OrdenCompra::create([
            'tipo_oc' => 2, 
            'estado_id' => 2,
            'presupuesto_id' => null, 
            'proveedor_id' => 3,  
        ]);
        
        $orden = NaturalInfo::create([
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
                'vtotal_oc' => $item['valor_total'] 
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
            'tercero',
            'nombre',
            'apellido',
            'correo',
            'cedula',
            'telefono',
            'ciudad',
            'banco'
        ]);

        unset($this->tercero);
        unset($this->selected_item);
        $this->items = collect(); 
        return back()->with('success', 'Orden de compra creada correctamente');
    }

    /* * --------------------- * */

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
            $item_info = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();
            // Limites
            $this->limiteCantidad = ($item_info->cantidad - $item_info->consumidos()->get()->sum('cant_oc'));
            $this->limiteDias = ($item_info->dia - $item_info->consumidos()->get()->sum('dias_oc'));
            $this->limiteOtros = ($item_info->otros - $item_info->consumidos()->get()->sum('otros_oc'));
            $this->limiteValorUnitario = ($item_info->v_unitario - $item_info->consumidos()->get()->sum('vunit_oc'));
            $this->limiteValorTotal = ($item_info->v_total - $item_info->consumidos()->get()->sum('vtotal_oc'));

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
        $this->validate([
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad
        ]);

        $this->getValorTotal();
    }

    public function updatedDias(){
        $this->validate([
            'dias' => 'required|numeric|min: 1|max:'.$this->limiteDias
        ]);

        $this->getValorTotal();
    }

    public function updatedOtros(){
        $this->validate([
            'otros' => 'required|numeric|min: 1|max:'.$this->limiteOtros
        ]);

        $this->getValorTotal();
    }

    public function updatedValorUnitario(){
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
        $this->valor_total = ($this->cantidad * $this->dias * $this->otros) * $this->valor_unitario;
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
 