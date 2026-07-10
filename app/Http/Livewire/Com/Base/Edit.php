<?php

namespace App\Http\Livewire\Com\Base;

use Livewire\Component;
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Cuenta;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads; // Habilita la subida de archivos con Livewire

    // Models
    public $nom_cliente; // Nombre del clientes
    public $nom_proyecto; // Nombre del proyecto
    public $CC; // Código de centro de costos
    public $valor; // Valor original del proyecto
    public $estado; // Estado del proyecto
    public $cuenta; // Cuenta asociada al proyecto
    public $cotizacion_file_actualizacion; // Archivo de cotización actualizado

    // Porcentajes y comerciales
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
    public $estados; // Lista de estados posibles
    public $cuentas; // Lista de cuentas
    public $comerciales = []; // Lista de comerciales
    public $participaciones = 1; // Número de participantes
    public $stored; // Registro almacenado
    public $id_gestion; // ID de la gestión comercial
    public $valor_guardado = 0; // Valor original guardado

    public $proyecto_id; // ID del proyecto

    // Renderiza la vista principal del formulario de edición
    public function render()
    {
        return view('livewire.com.base.edit');
    }

    // Inicializa el formulario con los datos del proyecto seleccionado
    public function mount($proyecto_id){
        // Obtiene el registro principal y todos los participantes del proyecto
        $stored = Base_comercial::select('nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_original', 'valor_proyecto', 'id_estado', 'id_cuenta', 'id_gestion', 'cotizacion_file_actualizacion')->where('id', $proyecto_id)->first();
        $Proyectos = Base_comercial::select('nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_original', 'valor_proyecto', 'id_estado', 'id_cuenta', 'id_gestion', 'porcentaje', 'id_user')->where('id_gestion', $stored->id_gestion)->get();

        $this->id_gestion = $stored->id_gestion;
        $this->nom_cliente = $stored->nom_cliente;
        $this->nom_proyecto = $stored->nom_proyecto;
        $this->CC = $stored->cod_cc;
        $this->valor = $stored->valor_original;
        $this->valor_guardado = $stored->valor_original;
        $this->estado = $stored->id_estado;
        $this->cuenta = $stored->id_cuenta;

        // Si existe gestión, asigna el número de participaciones
        if ($stored->gestion){
            $this->participaciones = $stored->gestion->participaciones;
        }

        // Asigna comerciales y porcentajes a cada participante
        foreach ($Proyectos as $key => $proyecto) {
            $this->{'comercial'.$key} = $proyecto->id_user;
        }
        foreach ($Proyectos as $key => $proyecto) {
            $this->{'porcentaje'.$key} = $proyecto->porcentaje;
        }

        $this->getValor();
        $this->getTotalPorcentaje();

        $this->getEstados();
        $this->getCuentas();
        $this->getComerciales();
    }

    // Valida y actualiza el valor del proyecto
    public function updatedValor(){
        $this->valor = str_replace(",",'', $this->valor);
        $this->validate([
            'valor' => 'numeric'
        ]);
        $this->getValor();
        $this->getTotalPorcentaje();
    }

    // Valida y actualiza el nombre del clientes
    public function updatedNomCliente(){
        $this->validate([
            'nom_cliente' => 'string|min:0'
        ]);
    }

    // Valida y actualiza el código de centro de costos
    public function updatedCC(){
        $this->validate([
            'nom_cliente' => 'string|min:0'
        ]);
    }

    // Valida y actualiza el nombre del proyecto
    public function updatedNomProyecto(){
        $this->validate([
            'nom_proyecto' => 'string|min:0'
        ]);
    }

    // Valida y actualiza el estado del proyecto
    public function updatedEstado(){
        $this->validate([
            'estado' => 'numeric',
        ]);
    }

    // Valida y actualiza la cuenta asociada
    public function updatedCuenta(){
        $this->validate([
            'cuenta' => 'numeric'
        ]);
    }

    // Valida el archivo de cotización actualizado
    public function CotizacionFileActualizacion(){
        $this->validate([
            'cotizacion_file_actualizacion' => 'nullable|max:1024'
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
    }

    // Valida y actualiza el comercial 1
    public function updatedComercial1(){
        $this->validate([
            'comercial1' => 'required|numeric'
        ]);
    }

    // Valida y actualiza el comercial 2
    public function updatedComercial2(){
        $this->validate([
            'comercial2' => 'required|numeric'
        ]);
    }

    // Valida y actualiza el comercial 3
    public function updatedComercial3(){
        $this->validate([
            'comercial3' => 'required|numeric'
        ]);
    }

    // Valida y actualiza el porcentaje del comercial 0
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

    // Valida y actualiza el porcentaje del comercial 1
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

    // Valida y actualiza el porcentaje del comercial 2
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

    // Valida y actualiza el porcentaje del comercial 3
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

    // Valida y actualiza el valor del comercial 0
    public function updatedValor0(){
        $this->validate([
            'valor0' => 'required|numeric'
        ]);
    }

    // Valida y actualiza el valor del comercial 1
    public function updatedValor1(){
        $this->validate([
            'valor1' => 'required|numeric'
        ]);
    }

    // Valida y actualiza el valor del comercial 2
    public function updatedValor2(){
        $this->validate([
            'valor2' => 'required|numeric'
        ]);
    }

    // Valida y actualiza el valor del comercial 3
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
            $this->{'valor'.$i} = $this->valor * ($this->{'porcentaje'.$i} / 100);
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

    // Carga los estados posibles
    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->get();
    }

    // Carga las cuentas disponibles
    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    // Carga los comerciales disponibles
    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    // Actualiza el proyecto con los datos del formulario
    public function update_proyecto(){
        // Valida todos los campos antes de actualizar
        $this->validate([
            'nom_cliente' => 'string|min:0',
            'nom_proyecto' => 'string|min:0',
            'CC' => 'string|min:0',
            'valor' => 'numeric',
            'estado' => 'numeric',
            'cuenta' => 'numeric',
            'cotizacion_file_actualizacion' => 'nullable|max:1024',

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

        // Actualiza cada participante del proyecto
        $i  = 0;
        while($i < $this->participaciones){
            $proyecto = Base_comercial::where('id_gestion', $this->id_gestion)->where('id_user', $this->{'comercial'.$i})->first();

            if ($this->nom_cliente){
                $proyecto->nom_cliente = $this->nom_cliente;
            }

            if ($this->nom_proyecto){
                $proyecto->nom_proyecto = $this->nom_proyecto;
            }

            // if ($this->CC){
            //     $proyecto->cod_cc = $this->CC;
            // }

            if ($this->valor){
                $proyecto->valor_original = $this->valor;
            }

            if ($this->estado){
                $proyecto->id_estado = $this->estado;
            }

            if ($this->cuenta){
                $proyecto->id_cuenta = $this->cuenta;
            }

            if ($this->cuenta){
                $proyecto->id_cuenta = $this->cuenta;
            }

            // Si se subió un archivo de cotización, lo guarda
            if ($this->cotizacion_file_actualizacion){
                $proyecto->cotizacion_file_actualizacion = $this->cotizacion_file_actualizacion->store('cotizaciones');
            }

            $proyecto->valor_proyecto = $this->{'valor'.$i};
            $proyecto->porcentaje = $this->{'porcentaje'.$i};

            $proyecto->id_asistente = Auth::user()->id;
            $proyecto->update();
            $i++;
        }

        // Redirige según el rol del usuario con mensaje de éxito
        if (Auth::user()->rol == 2){
            return redirect()->route('dashboard-base')->with('success', 'Proyecto actualizado exitosamente.');
        }elseif (Auth::user()->rol == 5){
            return redirect()->route('asis-dashboard-base')->with('success', 'Proyecto actualizado exitosamente.');
        }
    }
}
