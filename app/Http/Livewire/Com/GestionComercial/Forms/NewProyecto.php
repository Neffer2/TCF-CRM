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

    //USEFUL VARS
    public $estados = []; 
    public $cuentas = [];  
    public $porcentajes = ['100', '50'];
    public $comerciales = [];

    public $valorEjemplo;

    // Useful vars
    // Se decide utilizar "lead" como referencia a los registros del la tabla Gestion Comercial
    public $lead_id = 0;

    public function render() 
    {   
        $this->getEstados();
        $this->getCuentas();
        return view('livewire.com.gestion-comercial.forms.new-proyecto');
    }

    // Trae datos que ya éstan registrados en la gestión comericial (Nombre proyecto, valor, etc).
    public function mount(){
        $informacionGeneral = GestionComercial::where('id', $this->lead_id)->first();
        $this->nom_cliente = $informacionGeneral->contacto->nombre." ".$informacionGeneral->contacto->apellido." ".$informacionGeneral->contacto->empresa;
        $this->nom_proyecto = $informacionGeneral->nom_proyecto_prop;
        $this->valor_proyecto = $informacionGeneral->presto_prop;
        $this->com_2 = $informacionGeneral->comercial_2;
        $this->porcentaje = $informacionGeneral->porcentaje;
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->where('id', '<>', Auth::id())->get();
        $this->setExample();
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
        $this->validate(['valor_proyecto' => ['required', 'numeric']]);
        $this->setExample();
    }

    public function updatedPorcentaje(){ 
        $this->validate(['porcentaje' => ['required', 'numeric']]);
        $this->setExample();
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

    public function setExample (){
        $this->valorEjemplo = ($this->valor_proyecto * $this->porcentaje)/100;
    }

    public function store (){
        if ($this->porcentaje == 50){
            $this->validate([
                'fecha' => ['required', 'date'],
                'nom_cliente' => ['required', 'string'],
                'nom_proyecto' => ['required', 'string'],
                'cod_cc' => ['required', 'string'],
                'valor_proyecto' => ['required', 'numeric'],
                'com_2' => ['required', 'numeric'],
                'id_estado' => ['required', 'numeric'],
                'id_cuenta' => ['numeric'],
                'fecha_inicio' => ['present'],
                'dura_mes' => ['present'],
                'valorEjemplo' => ['required', 'numeric']
            ]);

            $base_comercial = new Base_comercial;
            $base_comercial->fecha = $this->fecha; 
            $base_comercial->nom_cliente = $this->nom_cliente;
            $base_comercial->nom_proyecto = $this->nom_proyecto;
            $base_comercial->cod_cc = $this->cod_cc;
            $base_comercial->valor_proyecto = $this->valorEjemplo;
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

            // Lo mismo para el otro 50%
            $base_comercial = new Base_comercial;
            $base_comercial->fecha = $this->fecha; 
            $base_comercial->nom_cliente = $this->nom_cliente;
            $base_comercial->nom_proyecto = $this->nom_proyecto;
            $base_comercial->cod_cc = $this->cod_cc;
            $base_comercial->valor_proyecto = $this->valorEjemplo;
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

            $base_comercial->id_user = $this->com_2;
            $base_comercial->id_asistente = Auth::id(); 
            $base_comercial->save();
        }else {
            $this->validate([
                'fecha' => ['required', 'date'],
                'nom_cliente' => ['required', 'string'],
                'nom_proyecto' => ['required', 'string'],
                'cod_cc' => ['required', 'string'],
                'valor_proyecto' => ['required', 'numeric'],
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
