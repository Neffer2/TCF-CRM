<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use App\Models\Año;
use App\Models\EstadosPresupuesto;
use App\Models\PresupuestoProyecto;
use App\Models\User;
use App\Traits\Email;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ValidacionesPresto extends Component
{
    use WithPagination, Email;
    protected $paginationTheme = 'bootstrap';

    // Propiedades del modelo para filtros y ordenamiento
    public $fecha = 'desc'; // Orden de fecha (asc/desc)
    public $cod_cc; // Código de centro de costo para filtrar
    public $año; // Año seleccionado para filtrar

    // Variables útiles para almacenar datos
    public $estados = []; // Lista de estados de presupuesto disponibles
    public $años; // Lista de años disponibles (Collection)
    public $yearInfo; // Información detallada del año seleccionado

    // Variables que contienen los ids de los Lideres Comerciales y de los Gerentes
    public $gerentes = [8, 10];
    public $lideres_comerciales = [];

    public function render()
    {
        // Filtros base: solo presupuestos con código de centro de costo
        $filtros = [];

        // Filtro por código de centro de costo si se especifica
        if ($this->cod_cc){
            array_push($filtros, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }

        // Filtro por año si se especifica
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Validamos si el usuario es Lider Comercial
        if (Auth::user()->comerciales()->exists()) {
            array_push($filtros, ['estado_id', 4]);

            // Obtenemos los ids de los comerciales asignados al Lider
            $comerciales_id = Auth::user()->comerciales()->pluck('users.id');

            // Obtenemos el listado de presupuestos
            $presupuestos = PresupuestoProyecto::where($filtros)
                ->whereHas('gestion', function ($query) use ($comerciales_id) {
                    $query->whereIn('id_user', $comerciales_id);
                })
                ->orderBy('id', $this->fecha)
                ->paginate(15);
        }
        // Validamos si el usuario es Gerente
        elseif (in_array(Auth::user()->id, $this->gerentes)) {
            array_push($filtros, ['estado_id', 5]);

            // Obtenemos el listado de presupuestos
            $presupuestos = PresupuestoProyecto::where($filtros)
                ->orderBy('id', $this->fecha)
                ->paginate(15);
        }
        else {
            $presupuestos = [];
        }

        return view('livewire.admin.gestion-comercial.validaciones-presto', ['presupuestos' => $presupuestos]);
    }

    /**
     * Metodo de inicialización del componente
     * Se ejecuta cuando el componente es montado
     */
    public function mount(){
        $this->getEstados();
        $this->getAños();
    }

    /**
     * Obtiene la lista de estados de presupuesto disponibles
     */
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')
            ->whereNotIn('id', [1,4,5])
            ->get();
    }

    /**
     * Obtiene la lista de años disponibles
     * Establece el año actual como seleccionado por defecto
     */
    public function getAños(){
        $this->años = Año::all();
        // Selecciona el año más reciente como valor por defecto
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    /**
     * Se ejecuta cuando cambia el año seleccionado
     * Valida el año y obtiene su información detallada
     */
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        // Obtiene la información completa del año incluyendo sus meses
        $this->yearInfo = Año::find($this->año);
    }

    /**
     * Cambia el estado de un presupuesto
     * @param int|null $id ID del presupuesto
     * @param int|null $estado Nuevo estado del presupuesto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cambioEstado($id = null, $estado = null){
        $presupuesto = PresupuestoProyecto::find($id);

        // Si el estado es 2 (revisión)
        if ($estado == 2) {
            // Si el presupuesto esta en validación lider comercial y el margen del proyecto es menor al 35%,
            // se envia a validación de gerencia (estado_id = 5),
            // de lo contrario se envia a revisión por parte de Controller (estado_id = 2)
            if ($presupuesto->estado_id == 4 && $presupuesto->margen_proy < 35.00) {
                $estado = 5;
            }
            else {
                // Envía notificación de revisión
                $this->presupuestoAprobacion($presupuesto, Auth::user());
            }
        }
        // Si el estado es 3 (rechazado)
        elseif ($estado == 3) {
            // Envía notificación de rechazo
            $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, null);
        }

        $presupuesto->estado_id = $estado;
        $presupuesto->update();
        return redirect()->route('validaciones')->with('success', 'Cambios guardados exitosamente');
    }
}
