<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use App\Models\User;
use App\Models\Asistente;
use Livewire\WithFileUploads;

class CotizacionForm extends Component
{
    use WithFileUploads;  // Habilita la subida de archivos con Livewire

    // Modelos para los campos del formulario
    public $presupuesto;
    public $nom_proyecto;
    public $fecha;
    public $cotizacionFile;
    public $com_2;
    public $cotizacionUrl;
    public $claro;

    // Variables para comerciales y porcentajes
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

    // Variables útiles
    public $lead_id = 0; // ID del registro en Gestión Comercial
    public $porcentajes = ['100', '50']; // Porcentajes por defecto
    public $comerciales = []; // Lista de comerciales
    public $participaciones = 1; // Número de participantes
    public $testigoPorcentaje; // Suma de porcentajes (debe ser 100)

    // Renderiza la vista principal del formulario de cotización
    public function render()
    {
        return view('livewire.com.gestion-comercial.forms.cotizacion-form');
    }

    // Inicializa el componente y asigna el comercial principal según el rol
    public function mount(){
        $this->getComerciales();
        if (Auth::user()->rol == 2){
            $this->comercial0 = Auth::id();
        }else if(Auth::user()->rol == 5){
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $this->comercial0 = $asistente->comercial_id;
        }
    }

    // Obtiene la lista de comerciales (usuarios con rol 2)
    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    // Valida y actualiza el presupuesto
    public function updatedPresupuesto (){
        $this->presupuesto = str_replace(",",'', $this->presupuesto);
        $this->validate([
            'presupuesto' => 'required|numeric'
        ]);

        $this->getValor();
        $this->getTotalPorcentaje();
    }

    // Valida y actualiza el nombre del proyecto
    public function updatedNomProyecto (){
        $this->validate([
            'nom_proyecto' => 'required|string'
        ]);
    }

    // Valida y actualiza el segundo comercial
    public function updatedCom2 (){
        $this->validate([
            'com_2' => 'required|numeric'
        ]);
    }

    // Valida y actualiza la fecha
    public function updatedFecha (){
        $this->validate([
            'fecha' => 'required|date'
        ]);
    }

    // Valida y actualiza la URL de la cotización
    public function updatedCotizacionUrl (){
        $this->validate([
            'cotizacionUrl' => 'nullable|string',
        ]);
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
        $this->valor0 = str_replace(",",'', $this->valor0);
        $this->validate([
            'valor0' => 'required|numeric'
        ]);
    }
    public function updatedValor1(){
        $this->valor1 = str_replace(",",'', $this->valor1);
        $this->validate([
            'valor1' => 'required|numeric'
        ]);
    }
    public function updatedValor2(){
        $this->valor2 = str_replace(",",'', $this->valor2);
        $this->validate([
            'valor2' => 'required|numeric'
        ]);
    }
    public function updatedValor3(){
        $this->valor3 = str_replace(",",'', $this->valor3);
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
            $this->{'valor'.$i} = $this->presupuesto * ($this->{'porcentaje'.$i} / 100);
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
    // Guarda la cotización y actualiza el registro en Gestión Comercial
    public function store (){
        $this->validate([
            'presupuesto' => 'required|numeric',
            'nom_proyecto' => 'required|string',
            // 'cotizacionFile' => 'required|max:1024',
            'fecha' => 'required|date',
            'cotizacionUrl' => 'nullable|string',

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
        ]);

        if ($this->claro){
            $this->validate([
                'claro' => 'boolean'
            ]);
        }

        // Busca el registro de gestión comercial (lead) y actualiza los campos
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->presto_cot = $this->presupuesto;
        $lead->participaciones = $this->participaciones;
        $lead->comercial_2 = $this->comercial1;
        $lead->comercial_3 = $this->comercial2;
        $lead->comercial_4 = $this->comercial3;
        $lead->porcentaje = $this->porcentaje0;
        $lead->porcentaje_2 = $this->porcentaje1;
        $lead->porcentaje_3 = $this->porcentaje2;
        $lead->porcentaje_4 = $this->porcentaje3;
        $lead->nom_proyecto_cot = $this->nom_proyecto;
        $lead->fecha_estimada_cot = $this->fecha;
        $lead->cotizacion_file = null;
        $lead->propuesta_url = $this->cotizacionUrl;
        // $lead->id_estado = 3;
        if ($this->claro){
            $lead->claro = $this->claro;
        }
        $lead->id_estado = 7;
        $lead->update();

        // Redirige según el rol del usuario
        if (Auth::user()->rol == 2){
            return redirect()->route('gestion-comercial')->with('success', '¡Propuesta registrada exitosamente!');
        }else if(Auth::user()->rol == 5){
            return redirect()->route('asis-gestion-comercial')->with('success', '¡Propuesta registrada exitosamente!');
        }
    }
}
