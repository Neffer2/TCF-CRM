<?php

namespace App\Http\Livewire\Com;

use Livewire\Component;
use App\Models\EstadoCuenta;
use Illuminate\Validation\Rules;

class NewProyecto extends Component
{      
    // MODELS
    public $fecha = ""; 
    public $nom_cliente = ""; 
    public $nom_proyecto = ""; 
    public $cod_cc = ""; 
    public $valor_proyecto = ""; 
    public $com_1 = ""; 
    public $com_2 = ""; 
    public $id_estado = "";
    public $fecha_inicio = "";
    public $dura_mes = "";

    //USEFUL VARS
    public $estados = []; 

    public function render()
    {   
        $this->getEstados();
        return view('livewire.com.new-proyecto');
    }

    public function updatedFecha(){ 
        $this->validate(['fecha' => ['required', 'date']]);
    }

    public function updatedNomCliente(){ 
        $this->validate(['nom_cliente' => ['required', 'string']]);
    }

    public function updatedNomProyecto(){ 
        $this->validate(['nom_proyecto' => ['required', 'string']]);
    }

    public function updatedCod_cc(){ 
        $this->validate(['cod_cc' => ['required', 'string']]);
    }

    public function updatedValorProyecto(){ 
        $this->validate(['valor_proyecto' => ['required', 'numeric']]);
    }

    public function updatedCom1(){ 
        $this->validate(['com_1' => ['string']]); 
    }

    public function updatedIdEstado(){ 
        $this->validate(['id_estado' => ['required', 'numeric']]);
    }

    public function updateFechaInicio(){
        $this->validate(['fecha_inicio' => ['date']]);
    }

    public function updateDuraMes(){
        $this->validate(['dura_mes' => ['date']]);
    }

    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->get();
    }
}
