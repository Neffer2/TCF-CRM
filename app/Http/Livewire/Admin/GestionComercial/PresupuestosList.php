<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\Año;
use App\Models\User;
use Livewire\WithPagination;
use Auth;

/**
 * Componente Livewire para mostrar y filtrar la lista de presupuestos
 * Permite filtrar por código de centro de costos, año, nombre de proyecto y comercial
 */
class PresupuestosList extends Component
{
    // Trait para manejar la paginación de los resultados
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

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
    public $notificacion = '';

    /**
     * Método principal que renderiza el componente con los presupuestos filtrados
     * Aplica filtros de búsqueda y retorna la vista con los datos paginados
     */
    public function render()
    {
        $user = Auth::user();
        $query = PresupuestoProyecto::query()->with('gestion');

        // Filtro por código de centro de costos
        if ($this->cod_cc) {
            $query->where('cod_cc', 'LIKE', "%{$this->cod_cc}%");
        }

        // Filtro por año
        if ($this->año && $this->yearInfo) {
            $query->whereBetween('created_at', [
                $this->yearInfo->meses->first()->f_inicio,
                $this->yearInfo->meses->last()->f_fin
            ]);
        }

        // NUEVO: Filtro por notificaciones
        if ($this->notificacion !== '' && $this->notificacion !== null) {
            if ($this->notificacion == '1') {
                // Opción A: Si el campo guarda un booleano/flag (1/0 o true/false)
                $query->where('notificacion_actualizacion', 1);

                // Opción B (Si el campo es fecha/texto y buscas que NO esté nulo, usa esta línea en su lugar):
                // $query->whereNotNull('notificacion_actualizacion');
            } elseif ($this->notificacion == '0') {
                $query->where(function ($q) {
                    $q->where('notificacion_actualizacion', 0)
                      ->orWhereNull('notificacion_actualizacion');
                });
            }
        }

        // Filtros en la relación 'gestion'
        if ($this->nom_proyecto || $this->comercial || ($user && $user->comercialesAsignados()->exists())) {
            $query->whereHas('gestion', function ($gestion) use ($user) {
                
                if ($this->nom_proyecto) {
                    $gestion->where('nom_proyecto_cot', 'LIKE', "%{$this->nom_proyecto}%");
                }

                if ($this->comercial) {
                    $gestion->where('id_user', $this->comercial);
                }

                // Restricción para líder comercial
                if ($user && $user->comercialesAsignados()->exists()) {
                    $comercialesIds = $user->comercialesAsignados()->pluck('users.id')->toArray();
                    $gestion->whereIn('id_user', $comercialesIds);
                }
            });
        }

        $presupuestos = $query->orderBy('created_at', $this->orderBy)->paginate(15);

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
        $user = Auth::user();

        if ($user && $user->comercialesAsignados()->exists()) {
            // Cargar únicamente los comerciales asociados al líder en la pivote
            $this->comerciales = $user->comercialesAsignados()->get();
        } else {
            // Si es un Admin global, carga todos los comerciales
            $this->comerciales = User::where('rol', 2)->get();
        }
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

}
