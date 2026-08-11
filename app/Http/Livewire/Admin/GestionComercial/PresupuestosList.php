<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\Año;
use App\Models\User;
use Livewire\WithPagination;

/**
 * Componente Livewire para mostrar y filtrar la lista de presupuestos
 * Permite filtrar por código de centro de costos, año, nombre de proyecto y comercial
 */
class PresupuestosList extends Component
{
    // Trait para manejar la paginación de los resultados
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'Actualizacion' => 'notificarActualizacion', 
        'marcarVisto' => 'marcarComoVisto',
    ];

    // Propiedades públicas para los filtros del formulario
    public $cod_cc;        // Código de centro de costos para filtrar
    public $año;           // ID del año seleccionado
    public $yearInfo;      // Objeto completo del año con sus meses
    public $orderBy = 'DESC';  // Orden de los resultados (DESC por defecto)
    public $nom_proyecto;  // Nombre del proyecto para filtrar
    public $comercial;     // ID del comercial/usuario para filtrar

    // Arrays para almacenar las opciones de los filtros
    public $estados = [];     // Estados de los presupuestos (no utilizado actualmente)
    public $años = [];        // Lista de años disponibles
    public $comerciales = []; // Lista de comerciales/usuarios

    public $presupuesto;
    public $id_gestion;

    /**
     * Método principal que renderiza el componente con los presupuestos filtrados
     * Aplica filtros de búsqueda y retorna la vista con los datos paginados
     */
    public function render()
    {
        // Array para almacenar filtros de la tabla presupuestos
        $filtros = [];
        // Array para almacenar filtros de la tabla gestión comercial (relación)
        $filtrosGestion = [];

        // Filtro por código de centro de costos (búsqueda parcial)
        if ($this->cod_cc){
            array_push($filtros, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }

        // Filtro por año: busca registros entre el primer día del año y el último
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtro por nombre de proyecto en la tabla gestión comercial
        if ($this->nom_proyecto){
            array_push($filtrosGestion, ['nom_proyecto_cot', 'like', "%$this->nom_proyecto%"]);
        }

        // Filtro por comercial/usuario en la tabla gestión comercial
        if ($this->comercial){
            array_push($filtrosGestion, ['id_user', $this->comercial]);
        }

        // Consulta principal: obtiene presupuestos con su gestión comercial aplicando todos los filtros
        $presupuestos = PresupuestoProyecto::with('gestion')
                        ->whereHas('gestion', function ($gestion) use ($filtrosGestion) {
                            $gestion->where($filtrosGestion);
                        })
                        ->where($filtros)
                        ->orderBy('created_at', $this->orderBy)
                        ->paginate(15);

        return view('livewire.admin.gestion-comercial.presupuestos-list', ['presupuestos' => $presupuestos]);
    }

    /**
     * Método que se ejecuta al inicializar el componente
     * Carga los datos iniciales necesarios para los filtros
     */
    public function mount(){
        $this->getAños();
        $this->getComerciales();
    }

    /**
     * Carga la lista de comerciales (usuarios con rol = 2)
     * Los comerciales son usuarios que pueden crear gestiones comerciales
     */
    public function getComerciales(){
        $this->comerciales = User::where('rol', 2)->get();
    }

    /**
     * Carga la lista de años disponibles y establece el año actual por defecto
     * Automáticamente selecciona el año más reciente
     */
    public function getAños(){
        $this->años = Año::all();

        // Establece el año más reciente como selección por defecto
        $añoMasReciente = $this->años->sortByDesc('description')->first();
        if ($añoMasReciente) {
            $this->año = $añoMasReciente->id;
            $this->updatedAño();
        }
    }

    /**
     * Método que se ejecuta cuando se actualiza la propiedad 'año'
     * Valida el año seleccionado y carga la información completa del año
     */
    public function updatedAño(){
        // Valida que se haya seleccionado un año
        $this->validate([
            'año' => 'required'
        ]);

        // Carga la información completa del año incluyendo sus meses
        $this->yearInfo = Año::find($this->año);
    }

    public function notificarActualizacion()
    {
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        if ($presto) {
            $presto->update(['notificacion_actualizacion' => true]);
            $this->presupuesto = $presto;
        }
    }
}