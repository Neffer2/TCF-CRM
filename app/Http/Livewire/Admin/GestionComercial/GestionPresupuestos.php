<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto;
use App\Models\GestionComercial;
use App\Traits\Email;
use Livewire\WithPagination;
use Auth;

/**
 * Componente Livewire para gestionar presupuestos de proyectos
 * Permite visualizar, filtrar y cambiar estados de presupuestos sin código de centro de costo
 */
class GestionPresupuestos extends Component
{
    use WithPagination, Email;
    protected $paginationTheme = 'bootstrap';

    // Propiedades del modelo para filtros y ordenamiento
    public $filter = 0; // Filtro por estado (0 = todos)
    // public $comercial = 0; // Filtro por comercial (comentado)
    public $fecha = 'asc'; // Orden por fecha (asc/desc)
    public $margen = 'asc'; // Filtro por margen (asc/</>)

    // Variables útiles para almacenar datos
    public $estados = []; // Lista de estados de presupuesto disponibles
    public $margenOperator; // Operador para filtro de margen
    public $estadoProyecto; // Estado específico del proyecto según rol
    // public $comerciales = []; // Lista de comerciales (comentado)

    public $rol; // Rol del usuario autenticado

    /**
     * Renderiza la vista del componente con presupuestos filtrados
     * Aplica filtros por estado, margen y excluye presupuestos con código de centro de costo
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $filtros = [];

        // Filtro por estado si se especifica (0 = todos los estados)
        if ($this->filter != 0){
            array_push($filtros, ['estado_id', $this->filter]);
        }

        // Filtro por margen de proyecto
        if ($this->margen == '<'){
            // Margen menor o igual a 35%
            array_push($filtros, ['margen_proy', '<=', 35]);
        }elseif ($this->margen == '>'){
            // Margen mayor o igual a 35%
            array_push($filtros, ['margen_proy', '>=', 35]);
        }

        // Solo presupuestos SIN código de centro de costo (diferencia con ActualizacionesPresto)
        array_push($filtros, ['cod_cc', null]);

        $query = PresupuestoProyecto::query();

        // Aplicar estado según la propiedad
        if (!empty($this->estadoProyecto)) {
            $query->where('estado_id', $this->estadoProyecto);
        }

        $query->where($filtros);

        // --- FILTRADO MEDIANTE TABLA PIVOTE ---
        $user = Auth::user();

        // Verificamos si el usuario actual está registrado como líder en la tabla pivote
        if ($user && $user->comercialesAsignados()->exists()) {
            // Extraer los IDs de sus comerciales asignados
            $comercialesIds = $user->comercialesAsignados()->pluck('users.id')->toArray();

            // Filtrar presupuestos cuya gestión pertenezca a sus comerciales
            $query->whereHas('gestion', function ($q) use ($comercialesIds) {
                $q->whereIn('comercial_id', $comercialesIds);
            });
        }

        $presupuestos = $query->orderBy('id', $this->fecha)->paginate(10);

        return view('livewire.admin.gestion-comercial.gestion-presupuestos', [
            'presupuestos' => $presupuestos
        ]);
    }

    /**
     * Método de inicialización del componente
     * Configura estados y establece estado específico según el rol del usuario
     */
    public function mount(){
        $this->getEstados();

        $user = Auth::user();

        // Si existe en la tabla pivote como líder, ajustamos el estado por defecto a validar (ejemplo: 4)
        if ($user && $user->comercialesAsignados()->exists()) {
            $this->estadoProyecto = 4; 
        } else {
            $this->estadoProyecto = 4; // Cambiar según el estado inicial requerido para admin general
        }
        // $this->getComerciales(); // Método comentado
    }

    /**
     * Obtiene la lista de estados de presupuesto disponibles
     * Excluye el estado con ID 3
     */
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')
            ->where('id', '<>', 3)
            ->get();
    }

    /**
     * Cambia el estado de un presupuesto y actualiza la gestión comercial relacionada
     * @param int|null $id ID del presupuesto
     * @param int|null $estado Nuevo estado del presupuesto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cambioEstado($id = null, $estado = null){
        $presupuesto = PresupuestoProyecto::find($id);
        $presupuesto->estado_id = $estado;

        if ($estado == 1) {
            $gestion = GestionComercial::find($presupuesto->id_gestion);
            $gestion->id_estado = 4;
            $gestion->update();

            $presupuesto->justificacion_compras = null;
            $presupuesto->justificacion_lider = null;
            $presupuesto->justificacion = null;
        }
        $presupuesto->update();

        if ($presupuesto->estado_id == 1) {
            $this->presupuestoAprobado($presupuesto->gestion->comercial, $presupuesto->gestion, null, $presupuesto->cod_cc);
        } elseif ($presupuesto->estado_id == 3) {
            $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, null);
        }

        return redirect()->route('presupuesto-proyecto')->with('success', 'Cambios guardados exitosamente');
    }
}
