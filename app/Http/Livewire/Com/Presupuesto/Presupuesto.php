<?php

namespace App\Http\Livewire\Com\Presupuesto; 

use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
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
    public $ciudades = [];

    // metricas
    public $margenGeneral = 0;
    public $ventaProyecto = 0;

    // Contacto
    public $nombre;
    public $cliente;
    public $nomProyecto;
    public $ciudadContacto;

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

        $this->refresh();
        $this->getContacto();
        $this->getCiudades(); 
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

        $this->refresh();
        $this->limpiar();
    }

    public function getItems(){
        $this->items = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->get();
    }

    public function getMetricas(){
        (!$this->margenGeneral = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->avg('margen_utilidad')) && $this->margenGeneral = 0;
        $this->ventaProyecto = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->sum('v_total');
    }

    public function getCiudades(){
        $this->ciudades = app('ciudades');
    }

    public function deleteItem($id){
        ItemPresupuesto::destroy($id);        
        $this->refresh();
    }

    public function refresh(){
        $this->getMetricas();
        $this->getItems();
    }

    public function getContacto(){
        $contacto = GestionComercial::where('id', $this->id_gestion)->first();
        $this->nombre = $contacto->contacto->nombre." ".$contacto->apellido;
        $this->cliente = $contacto->contacto->empresa;
        $this->nomProyecto = $contacto->nom_proyecto_cot;
        $this->ciudadContacto = $contacto->contacto->ciudad;
    }

    // VALIDATIONS
    public function updatedCod(){
        $this->cod = trim($this->cod);
        $this->validate([
            'cod' => ['required']
        ]);
    }

    public function updatedRevisar(){
        $this->revisar = trim($this->revisar);
        $this->validate([
            'revisar' => ['required']
        ]);
    }

    public function updatedConcepto(){
        $this->concepto = trim($this->concepto);
        $this->validate([
            'concepto' => ['required']
        ]);
    }

    public function updatedCantidad(){
        $this->cantidad = trim($this->cantidad);
        $this->validate([
            'cantidad' => ['required']
        ]);

        $this->getValorTotal();
    }

    public function updatedDia(){
        $this->dia = trim($this->dia);
        $this->validate([
            'dia' => ['required']
        ]);

        $this->getValorTotal();
    }

    public function updatedOtros(){
        $this->otros = trim($this->otros);
        $this->validate([
            'otros' => ['required']
        ]);

        $this->getValorTotal();
    }

    public function updatedDescripcion(){
        $this->descripcion = trim($this->descripcion);
        $this->validate([
            'descripcion' => ['required']
        ]);
    }

    public function updatedValorUnitario(){
        $this->valor_unitario = trim($this->valor_unitario);
        $this->valor_unitario = str_replace(",",'', $this->valor_unitario);
        $this->validate([
            'valor_unitario' => ['required', 'numeric']
        ]);

        $this->getValorTotal();
    }

    public function updatedValorTotal(){
        $this->valor_total = trim($this->valor_total);
        $this->valor_total = str_replace(",",'', $this->valor_total);
        $this->validate([
            'valor_total' => ['required', 'numeric']
        ]);
    }

    public function updatedProveedor(){
        $this->proveedor = trim($this->proveedor);
        $this->validate([
            'proveedor' => ['required']
        ]);
    }

    public function updatedUtilidad(){
        $this->utilidad = trim($this->utilidad);
        $this->utilidad = str_replace(",",'', $this->utilidad);
        $this->validate([
            'utilidad' => ['required', 'numeric']
        ]);
    }

    public function updatedMes(){
        $this->mes = trim($this->mes);
        $this->validate([
            'mes' => ['required']
        ]);
    }

    public function updatedDias(){
        $this->dias = trim($this->dias);
        $this->validate([
            'dias' => ['required']
        ]);
    }

    public function updatedCiudad(){
        $this->validate([
            'ciudad' => ['required']
        ]);
    }

    public function getValorTotal(){
        if (!is_null($this->cantidad) && !is_null($this->dia) && !is_null($this->otros) && !is_null($this->valor_unitario)){
            $this->valor_total = $this->cantidad * $this->dia * $this->otros * $this->valor_unitario;
        }
    }

    public function limpiar(){
        $this->cod = "";
        $this->revisar = "";
        $this->concepto = "";

        $this->cantidad = "";
        $this->dia = "";
        $this->otros = "";
        $this->descripcion = "";
        $this->valor_unitario = "";
        $this->valor_total = "";
        $this->proveedor = "";
        $this->utilidad = "";

        $this->mes = "";
        $this->dias = "";
        $this->ciudad = "";
    }
    // ----------- 
}
