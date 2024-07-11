<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\PresupuestoProyecto;
use App\Models\OrdenCompra;

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

    /*
        * CRUD ITEMS OC
    */
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

        
        array_push($this->items, $newItem);
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
    

    public function deleteItem($itemId){
        unset($this->items[$itemId]);
    }
    /*
        * ---------------------
    */

    // UPDATES
    public function updatedTercero(){
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
        if ($this->presupuesto){
            $this->items_presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first()->presupuestoItems;
        }else {
            $this->items_presupuesto = [];
        }
    }

    public function updatedItemPresupuesto(){
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
 