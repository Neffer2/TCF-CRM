<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;
 
use Livewire\Component;
use App\Models\EstadoCuenta;
use App\Models\Cuenta;
use App\Models\Base_comercial;
use App\Models\User; 
use App\Models\GestionComercial;
use Illuminate\Validation\Rules; 
use Illuminate\Support\Facades\Auth;

class NewProyecto extends Component
{   
    // str_replace(",",'', $this->debito);
    // MODELS 
    public $fecha = ""; 
    public $nom_cliente = "";  
    public $nom_proyecto = ""; 
    public $cod_cc; 
    public $valor_proyecto = ""; 
    public $com_2 = ""; 
    public $porcentaje;
    public $id_estado = "";
    public $id_cuenta = "";
    public $fecha_inicio = null; 
    public $dura_mes = null;

    // porcentajes
    public $comercial0;
    public $comercial1;
    public $comercial2;
    public $comercial3;

    public $porcentaje0 = 100;
    public $porcentaje1;
    public $porcentaje2;
    public $porcentaje3;

    public $valor0;
    public $valor1;
    public $valor2;
    public $valor3;

    //USEFUL VARS
    public $estados = []; 
    public $cuentas = [];  
    public $porcentajes = ['100', '50'];
    public $comerciales = [];
    public $participaciones = 1;
    public $testigoPorcentaje;

    public $valorEjemplo;

    // Useful vars
    // Se decide utilizar "lead" como referencia a los registros del la tabla Gestion Comercial
    public $lead_id = 0;

    public function render() 
    {   
        return view('livewire.com.gestion-comercial.forms.new-proyecto');
    }

    // Trae datos que ya éstan registrados en la gestión comericial (Nombre proyecto, valor, etc).
    public function mount(){
        $this->getEstados();
        $this->getCuentas();
        $informacionGeneral = GestionComercial::where('id', $this->lead_id)->first();
        $this->nom_cliente = $informacionGeneral->contacto->nombre." ".$informacionGeneral->contacto->apellido." ".$informacionGeneral->contacto->empresa;
        $this->nom_proyecto = $informacionGeneral->nom_proyecto_cot;
        $this->valor_proyecto = $informacionGeneral->presto_cot;
        $this->com_2 = $informacionGeneral->comercial_2;
        $this->porcentaje = $informacionGeneral->porcentaje;
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
        
        $this->participaciones = $informacionGeneral->participaciones;
        $this->comercial0 = Auth::id();
        $this->comercial1 = $informacionGeneral->comercial_2;
        $this->comercial2 = $informacionGeneral->comercial_3;
        $this->comercial3 = $informacionGeneral->comercial_4;

        $this->porcentaje0 = $informacionGeneral->porcentaje;
        $this->porcentaje1 = $informacionGeneral->porcentaje_2;
        $this->porcentaje2 = $informacionGeneral->porcentaje_3;
        $this->porcentaje3 = $informacionGeneral->porcentaje_4;    

        $this->getValor();
        $this->getTotalPorcentaje();
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

    public function updatedCodCc(){ 
        $this->validate(['cod_cc' => ['required', 'string']]);
    }

    public function updatedValorProyecto(){ 
        $this->valor_proyecto = str_replace(",",'', $this->valor_proyecto);
        $this->validate(['valor_proyecto' => ['required', 'numeric']]); 
        $this->getValor();
        $this->getTotalPorcentaje();
    }

    public function updatedPorcentaje(){ 
        $this->validate(['porcentaje' => ['required', 'numeric']]);
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

    /****** PARTICIPACIONES ******/
    public function updatedParticipaciones(){
        if ($this->participaciones >= 4){$this->participaciones = 4;}
        if ($this->participaciones <= 0){$this->participaciones = 1;}

        $this->validate([
            'participaciones' => 'required|numeric|min:1|max:4'
        ]);

        $this->getPorcentaje(); 
        $this->getValor();
        $this->getTotalPorcentaje();
        $this->updatedTestigoPorcentaje();
    }

    public function updatedComercial0(){
        $this->validate([
            'comercial0' => 'required|numeric'
        ]);
        $this->comercial0 = Auth::id();
    }

    public function updatedComercial1(){
        $this->validate([
            'comercial1' => 'required|numeric'
        ]);
    }

    public function updatedComercial2(){
        $this->validate([
            'comercial2' => 'required|numeric'
        ]);
    }

    public function updatedComercial3(){
        $this->validate([
            'comercial3' => 'required|numeric'
        ]);
    }

    public function updatedPorcentaje0(){
        if ($this->porcentaje0 >= 100){$this->porcentaje0 = 100;}
        if ($this->porcentaje0 <= 0){$this->porcentaje0 = 1;}

        $this->validate([
            'porcentaje0' => 'required|numeric|min: 1|max: 100'
        ]);

        $this->getValor();
        $this->getTotalPorcentaje();
        $this->updatedTestigoPorcentaje();
    }

    public function updatedPorcentaje1(){
        if ($this->porcentaje1 >= 100){$this->porcentaje1 = 100;}
        if ($this->porcentaje1 <= 0){$this->porcentaje1 = 1;}

        $this->validate([
            'porcentaje1' => 'required|numeric|min: 1|max: 100'
        ]);

        $this->getValor();
        $this->getTotalPorcentaje();
        $this->updatedTestigoPorcentaje();
    }

    public function updatedPorcentaje2(){
        if ($this->porcentaje2 >= 100){$this->porcentaje2 = 100;}
        if ($this->porcentaje2 <= 0){$this->porcentaje2 = 1;}

        $this->validate([
            'porcentaje2' => 'required|numeric|min: 1|max: 100'
        ]);

        $this->getValor();
        $this->getTotalPorcentaje();
        $this->updatedTestigoPorcentaje();
    }

    public function updatedPorcentaje3(){
        if ($this->porcentaje3 >= 100){$this->porcentaje3 = 100;}
        if ($this->porcentaje3 <= 0){$this->porcentaje3 = 1;}

        $this->validate([
            'porcentaje3' => 'required|numeric|min: 1|max: 100'
        ]);

        $this->getValor();
        $this->getTotalPorcentaje();
        $this->updatedTestigoPorcentaje();
    }

    public function updatedValor0(){
        $this->validate([
            'valor0' => 'required|numeric'
        ]);
    }

    public function updatedValor1(){
        $this->validate([
            'valor1' => 'required|numeric'
        ]);
    }

    public function updatedValor2(){
        $this->validate([
            'valor2' => 'required|numeric'
        ]); 
    }

    public function updatedValor3(){
        $this->validate([
            'valor3' => 'required|numeric'
        ]);
    }

    public function updatedTestigoPorcentaje(){
        $this->validate([
            'testigoPorcentaje' => 'required|numeric|min:100|max:100'
        ]);
    } 

    public function getTotalPorcentaje(){
        $i = 0;
        $this->testigoPorcentaje = 0;
        while($i < $this->participaciones){            
            $this->testigoPorcentaje += $this->{'porcentaje'.$i}; 
            $i++;
        }
    }

    public function getValor(){
        $i = 0;
        while($i < $this->participaciones){     
            $this->{'valor'.$i} = $this->valor_proyecto * ($this->{'porcentaje'.$i} / 100);
            $i++;
        }
    }

    public function getPorcentaje(){
        $i = 0;
        while($i < $this->participaciones){     
            $this->{'porcentaje'.$i} = 100/$this->participaciones;
            $i++;
        }
    }
    /************/
  
    public function store (){
        $this->validate([
            'fecha' => ['required', 'date'],
            'nom_cliente' => ['required', 'string'],
            'nom_proyecto' => ['required', 'string'],
            'cod_cc' => ['required', 'string'],
            'valor_proyecto' => ['required', 'numeric'],
            'id_estado' => ['required', 'numeric'],
            'id_cuenta' => ['required','numeric'], 
            'fecha_inicio' => ['present'],
            'dura_mes' => ['present'],

            // PARTICIPACIONES 
            'testigoPorcentaje' => 'required|numeric|min:100|max:100',
            'participaciones' => 'required|numeric|min:1|max:4',
            'porcentaje0' => 'required|numeric|min: 1|max: 100',
            'porcentaje1' => 'nullable|numeric|min: 1|max: 100',
            'porcentaje2' => 'nullable|numeric|min: 1|max: 100',
            'porcentaje3' => 'nullable|numeric|min: 1|max: 100',

            'comercial0' => 'required|numeric',
            'comercial1' => 'nullable|numeric',
            'comercial2' => 'nullable|numeric',
            'comercial3' => 'nullable|numeric',

            'valor0' => 'required|numeric',
            'valor1' => 'nullable|numeric',
            'valor2' => 'nullable|numeric', 
            'valor3' => 'nullable|numeric',
        ]);

        $i  = 0;
        while($i < $this->participaciones){
            $base_comercial = new Base_comercial;
            $base_comercial->fecha = $this->fecha; 
            $base_comercial->nom_cliente = $this->nom_cliente;
            $base_comercial->nom_proyecto = $this->nom_proyecto;
            $base_comercial->cod_cc = $this->cod_cc; 
            $base_comercial->valor_original = $this->valor_proyecto;
            $base_comercial->porcentaje = $this->{'porcentaje'.$i};
            $base_comercial->valor_proyecto = $this->{'valor'.$i};
            $base_comercial->id_gestion = $this->lead_id;
            $base_comercial->id_cuenta = $this->id_cuenta;
            $base_comercial->id_estado = $this->id_estado;
            $base_comercial->fecha_inicio = $this->fecha_inicio;
            $base_comercial->dura_mes = $this->dura_mes;
            $base_comercial->id_user = $this->{'comercial'.$i};
            
            $base_comercial->save();
            $i++;
        }
        
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
