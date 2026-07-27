<?php

namespace App\Http\Livewire\Admin\Produccion\Consumidos;

use App\Models\ItemPresupuesto;
use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\Proveedor;

class Consumido extends Component
{

    //public temporales
    public $cod;
    public $concepto;
    public $cantidad;
    public $dia;
    public $otros;
    public $descripcion;
    public $valor_unitario = 0;
    public $valor_total = 0;
    public $valor_total_cliente = 0;
    public $proveedor = [];
    public $utilidad;
    public $tiempoFactura;
    public $notas;

    public $mes;
    public $dias;
    public $ciudad;


    // Useful vars
    public $presupuesto, $proveedores = [];

    // filled
    public $presupuesto_id;

    public function render()
    {
        //temporal
        $this->items = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->orderBy('orden')
            ->get();


        return view('livewire.admin.produccion.consumidos.consumido');
    }

    public function mount(){
        $this->presupuesto = PresupuestoProyecto::find($this->presupuesto_id);
        $this->proveedores = Proveedor::select('id', 'tercero')->get();
    }

    public function getDataEdit($id){
        $this->selected_item = [];
        foreach ($this->items as $item) {
            if ($item->id == $id) {
                $this->selected_item = $item;
                break;
            }
        }
        $this->cod = $this->selected_item->cod;
        $this->presupuesto_id = $this->selected_item->presupuesto_id;
        $this->cantidad = $this->selected_item->cantidad;
        $this->dia = $this->selected_item->dia;
        $this->otros = $this->selected_item->otros;
        $this->descripcion = $this->selected_item->descripcion;
        $this->valor_unitario = $this->selected_item->v_unitario;
        $this->valor_total = $this->selected_item->v_total;
        $this->valor_total_cliente = $this->selected_item->v_total_cliente;
        $this->proveedor = (@unserialize($this->selected_item->proveedor)) ? @unserialize($this->selected_item->proveedor) : [$this->selected_item->proveedor];
        $this->utilidad = $this->selected_item->margen_utilidad;
        $this->mes = $this->selected_item->mes;
        $this->dias = $this->selected_item->dias;
        $this->ciudad = $this->selected_item->ciudad;
    }
}
