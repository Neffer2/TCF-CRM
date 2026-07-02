<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use App\Models\EstadoCuenta;
use App\Models\Cuenta;
use App\Models\Base_comercial;
use App\Models\User;
use App\Models\Asistente;
use App\Rules\CentroCostos;
use App\Models\GestionComercial;
use App\Models\PresupuestoProyecto;
use Illuminate\Support\Facades\Auth;

class NewProyecto extends Component
{
    // MODELS
    public $fecha = ""; // Fecha del proyecto
    public $nom_cliente = ""; // Nombre del cliente
    public $nom_proyecto = ""; // Nombre del proyecto
    public $cod_cc; // Código de centro de costos
    public $valor_proyecto = ""; // Valor total del proyecto
    public $com_2 = ""; // Comercial secundario
    public $porcentaje; // Porcentaje principal
    public $id_estado = ""; // Estado del proyecto
    public $id_cuenta = ""; // Cuenta asociada
    public $fecha_inicio = null; // Fecha de inicio
    public $dura_mes = null; // Duración en meses
    public $fecha_facturacion = null; // Fecha de facturacion

    // Variables para comerciales y porcentajes
    public $comercial0; // Comercial principal
    public $comercial1; // Comercial secundario 1
    public $comercial2; // Comercial secundario 2
    public $comercial3; // Comercial secundario 3

    public $porcentaje0 = 100; // Porcentaje del comercial principal
    public $porcentaje1; // Porcentaje del comercial 1
    public $porcentaje2; // Porcentaje del comercial 2
    public $porcentaje3; // Porcentaje del comercial 3

    public $valor0; // Valor correspondiente al comercial principal
    public $valor1; // Valor correspondiente al comercial 1
    public $valor2; // Valor correspondiente al comercial 2
    public $valor3; // Valor correspondiente al comercial 3

    // Variables útiles
    public $estados = []; // Lista de estados posibles
    public $cuentas = []; // Lista de cuentas
    public $porcentajes = ['100', '50']; // Porcentajes por defecto
    public $comerciales = []; // Lista de comerciales
    public $participaciones = 1; // Número de participantes
    public $testigoPorcentaje; // Suma de porcentajes (debe ser 100)

    public $valorEjemplo; // Variable de ejemplo para valores

    // Se decide utilizar "lead" como referencia a los registros de la tabla Gestion Comercial
    public $lead_id = 0; // ID del registro en Gestión Comercial

    // Renderiza la vista principal del formulario de nuevo proyecto
    public function render()
    {
        return view('livewire.com.gestion-comercial.forms.new-proyecto');
    }

    // Trae datos que ya están registrados en la gestión comercial (nombre proyecto, valor, etc.)
    public function mount(){
        $this->getEstados();
        $this->getCuentas();
        $informacionGeneral = GestionComercial::where('id', $this->lead_id)->first();
        $this->nom_cliente = $informacionGeneral->contacto->nombre." ".$informacionGeneral->contacto->apellido." ".$informacionGeneral->contacto->empresa;
        $this->nom_proyecto = $informacionGeneral->nom_proyecto_cot;

        // Valor proyecto actualizado, else para los antiguos (sin presupuesto)
        if ($informacionGeneral->presupuesto){
            $this->valor_proyecto = $informacionGeneral->presupuesto->venta_proy;
        }else{
            $this->valor_proyecto = $informacionGeneral->presto_cot;
        }

        $this->com_2 = $informacionGeneral->comercial_2;
        $this->porcentaje = $informacionGeneral->porcentaje;
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();

        $this->participaciones = $informacionGeneral->participaciones;

        // Asigna el comercial principal según el rol
        if (Auth::user()->rol == 2){
            $this->comercial0 = Auth::id();
        }else if(Auth::user()->rol == 5){
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $this->comercial0 = $asistente->comercial_id;
        }

        // Asigna comerciales secundarios y porcentajes
        $this->comercial1 = $informacionGeneral->comercial_2;
        $this->comercial2 = $informacionGeneral->comercial_3;
        $this->comercial3 = $informacionGeneral->comercial_4;

        $this->porcentaje0 = $informacionGeneral->porcentaje;
        $this->porcentaje1 = $informacionGeneral->porcentaje_2;
        $this->porcentaje2 = $informacionGeneral->porcentaje_3;
        $this->porcentaje3 = $informacionGeneral->porcentaje_4;

        // Trae código de centro de costos y fecha si existe
        $prestoInfo = PresupuestoProyecto::select('cod_cc', 'fecha_cc')->where('id_gestion', $this->lead_id)->first();
        if ($prestoInfo){
            $this->cod_cc = $prestoInfo->cod_cc;
            $this->fecha = $prestoInfo->fecha_cc;
        }

        $this->getValor();
        $this->getTotalPorcentaje();
    }

    // Validaciones para los campos del formulario
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
        $this->validate(['cod_cc' => ['required', 'string', new CentroCostos]]);
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

    public function updateFechaFacturacion() {
        $this->validate(['fecha_facturacion' => ['present']]);
    }

    // Carga los estados posibles
    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->where('id', 6)->get();
    }

    // Carga las cuentas disponibles
    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    // Actualiza el estado en gestión comercial a "Venta"
    public function storeVenta(){
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->id_estado = 5;
        $lead->update();
    }

    /****** PARTICIPACIONES ******/
    // Valida y actualiza el número de participaciones
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

    // Valida y actualiza el comercial principal
    public function updatedComercial0(){
        $this->validate([
            'comercial0' => 'required|numeric'
        ]);
        if (Auth::user()->rol == 2){
            $this->comercial0 = Auth::id();
        }else if(Auth::user()->rol == 5){
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $this->comercial0 = $asistente->comercial_id;
        }
    }

    // Valida y actualiza los comerciales secundarios
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

    // Valida y actualiza los porcentajes de participación
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

    // Valida y actualiza los valores de participación
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

    // Valida que la suma de porcentajes sea 100
    public function updatedTestigoPorcentaje(){
        $this->validate([
            'testigoPorcentaje' => 'required|numeric|min:100|max:100'
        ]);
    }

    // Calcula la suma total de los porcentajes de participación
    public function getTotalPorcentaje(){
        $i = 0;
        $this->testigoPorcentaje = 0;
        while($i < $this->participaciones){
            $this->testigoPorcentaje += $this->{'porcentaje'.$i};
            $i++;
        }
    }

    // Calcula el valor correspondiente a cada porcentaje de participación
    public function getValor(){
        $i = 0;
        while($i < $this->participaciones){
            $this->{'valor'.$i} = $this->valor_proyecto * ($this->{'porcentaje'.$i} / 100);
            $i++;
        }
    }

    // Calcula el porcentaje para cada participante (distribución equitativa)
    public function getPorcentaje(){
        $i = 0;
        while($i < $this->participaciones){
            $this->{'porcentaje'.$i} = 100/$this->participaciones;
            $i++;
        }
    }
    /************/

    // Guarda el proyecto y crea los registros en base_comercial
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
            'fecha_facturacion' => ['present'],

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

        // Crea un registro en base_comercial por cada participante
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
            $base_comercial->fecha_facturacion = $this->fecha_facturacion;
            $base_comercial->id_user = $this->{'comercial'.$i};

            $base_comercial->save();
            $i++;
        }

        $this->storeVenta(); // Actualiza el estado de la gestión comercial

        // Redirige según el rol del usuario
        if (Auth::user()->rol == 2){
            return redirect()->route('gestion-comercial')->with('success', '¡Proyecto creado exitosamente!');
        }else if(Auth::user()->rol == 5){
            return redirect()->route('asis-gestion-comercial')->with('success', '¡Proyecto creado exitosamente!');
        }
    }

    // Limpia los campos del formulario
    public function limpiar(){
        $this->fecha = "";
        $this->nom_cliente = "";
        $this->nom_proyecto = "";
        $this->cod_cc = "";
        $this->valor_proyecto = "";
        $this->com_2 = "";
        $this->id_estado = "";
        $this->fecha_inicio = null;
        $this->dura_mes = null;
        $this->fecha_facturacion = null;
    }
}
