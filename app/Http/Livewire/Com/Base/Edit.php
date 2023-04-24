<?php

namespace App\Http\Livewire\Com\Base;

use Livewire\Component;
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Cuenta;
use Illuminate\Support\Facades\Auth;
 
class Edit extends Component
{   
    // Models
    public $nom_cliente;
    public $nom_proyecto;
    public $CC;
    public $valor;
    public $estado;
    public $cuenta;

    // Useful vars
    public $estados;
    public $cuentas;

    public $proyecto_id;

 
    public function render()
    {
        return view('livewire.com.base.edit');
    }

    public function mount($proyecto_id){
        $stored = Base_comercial::select('nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_proyecto', 'id_estado', 'id_cuenta')->where('id', $proyecto_id)->first();
        
        $this->nom_cliente = $stored->nom_cliente;
        $this->nom_proyecto = $stored->nom_proyecto;
        $this->CC = $stored->cod_cc;
        $this->valor = $stored->valor_proyecto;
        $this->estado = $stored->id_estado;
        $this->cuenta = $stored->id_cuenta;
        $this->getEstados();
        $this->getCuentas();
    }

    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    public function update_proyecto(){
        $proyecto = Base_comercial::where('id', $this->proyecto_id)->first();

        $this->validate([
            'nom_cliente' => 'string|min:0',
            'nom_proyecto' => 'string|min:0',
            'CC' => 'string|min:0',
            'valor' => 'numeric', 
            'estado' => 'numeric',
            'cuenta' => 'numeric' 
        ]);

        if ($this->nom_cliente){
            $proyecto->nom_cliente = $this->nom_cliente;
        }

        if ($this->nom_proyecto){
            $proyecto->nom_proyecto = $this->nom_proyecto;
        }

        if ($this->CC){
            $proyecto->cod_cc = $this->CC;
        }

        if ($this->valor){
            $proyecto->valor_proyecto = $this->valor;
        }
        
        if ($this->estado){
            $proyecto->id_estado = $this->estado;
        }

        if ($this->cuenta){
            $proyecto->id_cuenta = $this->cuenta;
        }

        $proyecto->id_asistente = Auth::user()->id;
        $proyecto->update();

        return redirect()->route('dashboard-base')->with('success', 'Proyecto actualizado exitosamente.');
    }   
}
