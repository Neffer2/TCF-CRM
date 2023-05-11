<?php

namespace App\Http\Livewire\Com\Presupuesto; 

use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemPresupuesto;
use App\Models\PresupuestoProyecto;

class Presupuesto extends Component
{

    // Models
    public $cod;
    public $revisar;
    public $concepto;

    public $cantidad;
    public $dia;
    public $otros;
    public $descripcion;
    public $valor_unitario;
    public $valor_total;
    public $proveedor;
    public $utilidad;

    public $mes;
    public $dias;
    public $ciudad; 

    // Useful vars
    public $items = [];
    public $presupuesto_id;

    // globals
    public $id_gestion; 

    public function render()
    {
        return view('livewire.com.presupuesto.presupuesto');
    }

    public function mount(){        
        $validator = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        if (is_null($validator)){
            $presupuesto = new PresupuestoProyecto;
            $presupuesto->id_gestion = $this->id_gestion;
            $presupuesto->save();
            $this->presupuesto_id = $presupuesto->id;
        }else {
            $this->presupuesto_id = $validator->id;
        }

        $this->getItems();
    }

    public function new_item(){      
        $this->validate([
            'cod' => ['required'],
            'revisar' => ['required'],
            'concepto' => ['required'],
            'cantidad' => ['required'],
            'dia' => ['required'],
            'otros' => ['required'],
            'descripcion' => ['required'],
            'valor_unitario' => ['required'],
            'valor_total' => ['required'],
            'proveedor' => ['required'],
            'utilidad' => ['required'],
            'mes' => ['required'],
            'dias' => ['required'],
            'ciudad' => ['required'] 
        ]);
         
        $item = new ItemPresupuesto;
        $item->cod = $this->cod;
        $item->presupuesto_id = $this->presupuesto_id;
        $item->revisar = $this->revisar;
        $item->concepto = $this->concepto;
        $item->cantidad = $this->cantidad;
        $item->dia = $this->dia;
        $item->otros = $this->otros;
        $item->descripcion = $this->descripcion;
        $item->v_unitario = $this->valor_unitario;
        $item->v_total = $this->valor_total;
        $item->proveedor = $this->proveedor;
        $item->margen_utilidad = $this->utilidad;
        $item->mes = $this->mes;
        $item->dias = $this->dias;
        $item->ciudad = $this->ciudad;
        $item->save();

        $this->getItems();
    }

    public function getItems(){
        $this->items = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->get();
    }

    // VALIDATIONS
    public function updatedCod(){
        $this->validate([
            'cod' => ['required']
        ]);
    }

    public function updatedRevisar(){
        $this->validate([
            'revisar' => ['required']
        ]);
    }

    public function updatedConcepto(){
        $this->validate([
            'concepto' => ['required']
        ]);
    }

    public function updatedCantidad(){
        $this->validate([
            'cantidad' => ['required']
        ]);
    }

    public function updatedDia(){
        $this->validate([
            'dia' => ['required']
        ]);
    }

    public function updatedOtros(){
        $this->validate([
            'otros' => ['required']
        ]);
    }

    public function updatedDescripcion(){
        $this->validate([
            'descripcion' => ['required']
        ]);
    }

    public function updatedValorUnitario(){
        $this->validate([
            'valor_unitario' => ['required', 'numeric']
        ]);
    }

    public function updatedValorTotal(){
        $this->validate([
            'valor_total' => ['required', 'numeric']
        ]);
    }

    public function updatedProveedor(){
        $this->validate([
            'proveedor' => ['required']
        ]);
    }

    public function updatedUtilidad(){
        $this->validate([
            'utilidad' => ['required', 'numeric']
        ]);
    }

    public function updatedMes(){
        $this->validate([
            'mes' => ['required']
        ]);
    }

    public function updatedDias(){
        $this->validate([
            'dias' => ['required']
        ]);
    }

    public function updatedCiudad(){
        $this->validate([
            'ciudad' => ['required']
        ]);
    }
    // ----------- 
}
