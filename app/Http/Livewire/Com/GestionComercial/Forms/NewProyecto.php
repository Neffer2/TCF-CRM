<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use App\Models\EstadoCuenta;
use App\Models\Cuenta;
use App\Models\Base_comercial;
use App\Models\GestionComercial;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

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
    public $id_cuenta = "";
    public $fecha_inicio = null;
    public $dura_mes = null;

    //USEFUL VARS
    public $estados = []; 
    public $cuentas = [];  

    // Useful vars
    // Se decide utilizar "lead" como referencia a los registros del la tabla Gestion Comercial
    public $lead_id = 0;

    public function render() 
    {   
        $this->getEstados();
        $this->getCuentas();
        return view('livewire.com.gestion-comercial.forms.new-proyecto');
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

    public function updatedIdCuenta(){ 
        $this->validate(['id_cuenta' => ['required', 'numeric']]);
    }

    public function updateFechaInicio(){
        $this->validate(['fecha_inicio' => ['present']]);
    }

    public function updateDuraMes(){
        $this->validate(['dura_mes' => ['present']]);
    }

    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->where('id', 6)->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    // Actualiza el estado en gestion comercial
    public function storeVenta(){
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->id_estado = 5;
        $lead->update();
    }

    public function store (){
        $this->validate([
            'fecha' => ['required', 'date'],
            'nom_cliente' => ['required', 'string'],
            'nom_proyecto' => ['required', 'string'],
            'cod_cc' => ['required', 'string'],
            'valor_proyecto' => ['required', 'numeric'],
            'com_1' => 'string',
            'id_estado' => ['required', 'numeric'],
            'id_cuenta' => ['numeric'],
            'fecha_inicio' => ['present'],
            'dura_mes' => ['present']
        ]);
 
        $base_comercial = new Base_comercial;
        $base_comercial->fecha = $this->fecha; 
        $base_comercial->nom_cliente = $this->nom_cliente;
        $base_comercial->nom_proyecto = $this->nom_proyecto;
        $base_comercial->cod_cc = $this->cod_cc;
        $base_comercial->valor_proyecto = $this->valor_proyecto;
        $base_comercial->com_1 = $this->com_1;
        $base_comercial->id_gestion = $this->lead_id;

        if ($this->id_cuenta){ 
            $base_comercial->id_cuenta = $this->id_cuenta;
        }

        $base_comercial->id_estado = $this->id_estado;

        if ($this->fecha_inicio){
            $base_comercial->fecha_inicio = $this->fecha_inicio;
        }

        if ($this->dura_mes){ 
            $base_comercial->dura_mes = $this->dura_mes;
        }

        $base_comercial->id_user = Auth::id();
        $base_comercial->id_asistente = Auth::id(); 
        $base_comercial->save();

        $this->storeVenta();
  
        return redirect()->route('gestion-comercial')->with('success', '¡Proyecto creado exitosamente!');
    }
 
    public function limpiar(){
        $this->fecha = ""; 
        $this->nom_cliente = ""; 
        $this->nom_proyecto = ""; 
        $this->cod_cc = ""; 
        $this->valor_proyecto = ""; 
        $this->com_1 = ""; 
        $this->com_2 = ""; 
        $this->id_estado = "";
        $this->fecha_inicio = null;
        $this->dura_mes = null;
    }
}
