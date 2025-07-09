<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\Models\Año;
use App\Models\User;
use App\Models\Presupuesto;
use App\Models\Mes;
use Illuminate\Validation\Rules;

/**
 * Componente Livewire para la asignación de presupuestos mensuales a comerciales
 * Permite gestionar presupuestos por año y mes para usuarios con rol comercial
 */
class AsignarPresupuesto extends Component
{
    // Propiedades para la selección de datos principales

    /**
     * ID del comercial seleccionado
     * @var int
     */
    public $comercialesModel;

    /**
     * ID del año seleccionado
     * @var int
     */
    public $añosModel;

    /**
     * Nombre del comercial seleccionado (para mostrar en la vista)
     * @var string
     */
    public $comercialName;

    /**
     * Descripción del año seleccionado (para mostrar en la vista)
     * @var string
     */
    public $añoDescription;

    // Propiedades para los valores de presupuesto de cada mes

    /**
     * Valor del presupuesto para enero
     * @var float
     */
    public $eneroModel;

    /**
     * Valor del presupuesto para febrero
     * @var float
     */
    public $febreroModel;

    /**
     * Valor del presupuesto para marzo
     * @var float
     */
    public $marzoModel;

    /**
     * Valor del presupuesto para abril
     * @var float
     */
    public $abrilModel;

    /**
     * Valor del presupuesto para mayo
     * @var float
     */
    public $mayoModel;

    /**
     * Valor del presupuesto para junio
     * @var float
     */
    public $junioModel;

    /**
     * Valor del presupuesto para julio
     * @var float
     */
    public $julioModel;

    /**
     * Valor del presupuesto para agosto
     * @var float
     */
    public $agostoModel;

    /**
     * Valor del presupuesto para septiembre
     * @var float
     */
    public $septiembreModel;

    /**
     * Valor del presupuesto para octubre
     * @var float
     */
    public $octubreModel;

    /**
     * Valor del presupuesto para noviembre
     * @var float
     */
    public $noviembreModel;

    /**
     * Valor del presupuesto para diciembre
     * @var float
     */
    public $diciembreModel;

    // Propiedades para almacenar datos de los modelos

    /**
     * Colección de comerciales disponibles
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $comercialesStored;

    /**
     * Colección de años disponibles
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $añosStored;

    // Configuración de listeners para Livewire

    /**
     * Array de listeners que escuchan eventos específicos
     * @var array
     */
    protected $listeners = ['refresh' => 'mount'];

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.presupuestos.asignar-presupuesto');
    }

    /**
     * Método que se ejecuta al inicializar el componente
     * Carga los datos necesarios para el funcionamiento del componente
     *
     * @return void
     */
    public function mount(){
        $this->getModels();
    }

    /**
     * Obtiene los modelos necesarios para los selectores
     * Carga comerciales (usuarios con rol 2) y años disponibles
     *
     * @return void
     */
    public function getModels (){
        // Obtiene usuarios con rol comercial (rol = 2)
        $this->comercialesStored = User::select('id', 'name', 'rol')->where('rol', 2)->get();
        // Obtiene todos los años disponibles
        $this->añosStored = Año::select('id', 'description')->get();
    }

    /**
     * Obtiene los presupuestos almacenados para el comercial y año seleccionados
     * Valida que se hayan seleccionado comercial y año antes de proceder
     * Si existen presupuestos, los carga en las propiedades mensuales
     * Si no existen, limpia los campos (excepto selecciones principales)
     *
     * @return void
     */
    public function getPresupuestoStored (){
        // Valida que se hayan seleccionado comercial y año
        $this->validate([
            'comercialesModel' => 'required',
            'añosModel' => 'required'
        ]);

        // Busca presupuestos existentes para el comercial y año seleccionados
        $presupuestoStored = Presupuesto::select('id', 'valor', 'ano_id', 'id_user')
                                    ->where('ano_id', $this->añosModel)
                                    ->where('id_user', $this->comercialesModel)
                                    ->get();

        // Obtiene el nombre del comercial y descripción del año para mostrar
        $this->comercialName = User::select('name')->where('id', $this->comercialesModel)->first()->name;
        $this->añoDescription = Año::select('description')->where('id', $this->añosModel)->first()->description;

        // Si existen presupuestos, los carga en las propiedades correspondientes
        if (!$presupuestoStored->isEmpty()){
            // Asigna cada valor del presupuesto al mes correspondiente (orden: enero-diciembre)
            $this->eneroModel = $presupuestoStored[0]->valor;
            $this->febreroModel = $presupuestoStored[1]->valor;
            $this->marzoModel = $presupuestoStored[2]->valor;
            $this->abrilModel = $presupuestoStored[3]->valor;
            $this->mayoModel = $presupuestoStored[4]->valor;
            $this->junioModel = $presupuestoStored[5]->valor;
            $this->julioModel = $presupuestoStored[6]->valor;
            $this->agostoModel = $presupuestoStored[7]->valor;
            $this->septiembreModel = $presupuestoStored[8]->valor;
            $this->octubreModel = $presupuestoStored[9]->valor;
            $this->noviembreModel = $presupuestoStored[10]->valor;
            $this->diciembreModel = $presupuestoStored[11]->valor;
        }else {
            // Si no existen presupuestos, limpia los campos mensuales
            $this->limpiar(true);
        }
    }

    /**
     * Actualiza o crea presupuestos para el comercial y año seleccionados
     * Valida todos los campos mensuales y las selecciones principales
     * Si existen presupuestos, los actualiza; si no, los crea
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePresupuestos() {
        // Valida que todos los campos mensuales sean numéricos y estén presentes
        $this->validate([
            'eneroModel' => ['required', 'numeric'],
            'febreroModel' => ['required', 'numeric'],
            'marzoModel' => ['required', 'numeric'],
            'abrilModel' => ['required', 'numeric'],
            'mayoModel' => ['required', 'numeric'],
            'junioModel' => ['required', 'numeric'],
            'julioModel' => ['required', 'numeric'],
            'agostoModel' => ['required', 'numeric'],
            'septiembreModel' => ['required', 'numeric'],
            'octubreModel' => ['required', 'numeric'],
            'noviembreModel' => ['required', 'numeric'],
            'diciembreModel' => ['required', 'numeric'],
            'añosModel' => ['required'],
            'comercialesModel' => ['required']
        ]);

        // Busca presupuestos existentes para el comercial y año seleccionados
        $presupuestosXmes = Presupuesto::where('ano_id', $this->añosModel)->where('id_user', $this->comercialesModel)->get();

        // Si existen presupuestos, los actualiza
        if (!$presupuestosXmes->isEmpty()){
            foreach ($presupuestosXmes as $presMes){
                // Actualiza el valor según el identificador del mes
                switch ($presMes->presupuesto_mes->identifier){
                    case 1:
                        $presMes->valor = $this->eneroModel;
                    break;
                    case 2:
                        $presMes->valor = $this->febreroModel;
                    break;
                    case 3:
                        $presMes->valor = $this->marzoModel;
                    break;
                    case 4:
                        $presMes->valor = $this->abrilModel;
                    break;
                    case 5:
                        $presMes->valor = $this->mayoModel;
                    break;
                    case 6:
                        $presMes->valor = $this->junioModel;
                    break;
                    case 7:
                        $presMes->valor = $this->julioModel;
                    break;
                    case 8:
                        $presMes->valor = $this->agostoModel;
                    break;
                    case 9:
                        $presMes->valor = $this->septiembreModel;
                    break;
                    case 10:
                        $presMes->valor = $this->octubreModel;
                    break;
                    case 11:
                        $presMes->valor = $this->noviembreModel;
                    break;
                    case 12:
                        $presMes->valor = $this->diciembreModel;
                    break;
                }
                // Guarda los cambios en la base de datos
                $presMes->update();
            }
        }else {
            // Si no existen presupuestos, los crea para cada mes del año
            $meses = Mes::select('id', 'identifier')->where('ano_id', $this->añosModel)->get();
            foreach ($meses as $mes){
                $newPresupuesto = new Presupuesto;
                $newPresupuesto->ano_id = $this->añosModel;
                $newPresupuesto->mes_id = $mes->id;

                // Asigna el valor según el identificador del mes
                switch ($mes->identifier){
                    case 1:
                        $newPresupuesto->valor = $this->eneroModel;
                    break;
                    case 2:
                        $newPresupuesto->valor = $this->febreroModel;
                    break;
                    case 3:
                        $newPresupuesto->valor = $this->marzoModel;
                    break;
                    case 4:
                        $newPresupuesto->valor = $this->abrilModel;
                    break;
                    case 5:
                        $newPresupuesto->valor = $this->mayoModel;
                    break;
                    case 6:
                        $newPresupuesto->valor = $this->junioModel;
                    break;
                    case 7:
                        $newPresupuesto->valor = $this->julioModel;
                    break;
                    case 8:
                        $newPresupuesto->valor = $this->agostoModel;
                    break;
                    case 9:
                        $newPresupuesto->valor = $this->septiembreModel;
                    break;
                    case 10:
                        $newPresupuesto->valor = $this->octubreModel;
                    break;
                    case 11:
                        $newPresupuesto->valor = $this->noviembreModel;
                    break;
                    case 12:
                        $newPresupuesto->valor = $this->diciembreModel;
                    break;
                }

                // Asigna el comercial y guarda el nuevo presupuesto
                $newPresupuesto->id_user = $this->comercialesModel;
                $newPresupuesto->save();
            }
        }

        // Limpia todos los campos después de guardar
        $this->limpiar();

        // Redirige con mensaje de éxito
        return redirect()->back()->with('success', 'Presupuestos asignados exitosamente!');
    }

    /**
     * Limpia los campos del formulario
     * Resetea todos los valores mensuales a cadenas vacías
     * Opcionalmente puede preservar las selecciones de comercial y año
     *
     * @param bool $except Si es true, no limpia comercial y año seleccionados
     * @return void
     */
    public function limpiar($except = false){
        // Limpia todos los valores mensuales
        $this->eneroModel = "";
        $this->febreroModel = "";
        $this->marzoModel = "";
        $this->abrilModel = "";
        $this->mayoModel = "";
        $this->junioModel = "";
        $this->julioModel = "";
        $this->agostoModel = "";
        $this->septiembreModel = "";
        $this->octubreModel = "";
        $this->noviembreModel = "";
        $this->diciembreModel = "";

        // Si $except es false, también limpia las selecciones principales
        if (!$except){
            $this->comercialesModel = "";
            $this->añosModel = "";
            $this->comercialName = "";
            $this->añoDescription = "";
        }
    }
}
