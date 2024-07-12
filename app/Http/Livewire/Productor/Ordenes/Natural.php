<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\PresupuestoProyecto;
use App\Models\OrdenCompra; 
use App\Models\OcItem; 

class Natural extends Component
{
    // Models
    public $tercero, $nombre, $apellido, $correo, $cedula, $telefono, $ciudad, $banco,
            $search_nombre, $search_cedula, $search_telefono,
            $selected_item, $presupuesto, $item_presupuesto, $cantidad, $dias, $otros, $valor_unitario = 0, $valor_total = 0;

    // Useful vars 
    public $terceros, $ciudades, $items = [], $presupuestos = [], $items_presupuesto = [];

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
            'cantidad' => 'required',
            'dias' => 'required',
            'otros' => 'required',
            'valor_unitario' => 'required',
            'valor_total' => 'required',
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
    }

    public function updatedPresupuesto(){
        $this->validate([
            'presupuesto' => 'required'
        ]);

        if ($this->presupuesto){
            $this->items_presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first()->presupuestoItems;
        }else {
            $this->items_presupuesto = [];
        }
    }

    public function updatedItemPresupuesto(){
        $this->validate([
            'item_presupuesto' => 'required'
        ]);

        if ($this->item_presupuesto){
            $item_info = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();
            $this->cantidad = $item_info->cantidad;
            $this->dias = $item_info->dia;
            $this->otros = $item_info->otros;
            $this->valor_unitario = $item_info->v_unitario;
            $this->valor_total = $item_info->v_unitario;
        }else{
            $this->items_presupuesto = [];
        }
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
 