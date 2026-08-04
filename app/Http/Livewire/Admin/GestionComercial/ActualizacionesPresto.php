<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto;
use App\Models\Año;
use App\Models\Asistente;
use App\Models\ItemPresupuesto;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Traits\Email;
use App\Models\User;

/**
 * Componente Livewire para gestionar actualizaciones de presupuestos
 * Permite visualizar, filtrar y cambiar estados de presupuestos según el rol del usuario
 */
class ActualizacionesPresto extends Component
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

    public $rol; // Rol del usuario autenticado

    /**
     * Renderiza la vista del componente con presupuestos filtrados
     * Aplica diferentes filtros según el rol del usuario:
     * - Rol 1: Solo presupuestos con estado 2 (pendientes de aprobación)
     * - Rol 2: Solo presupuestos propios del comercial
     * - Rol 5: Presupuestos del comercial asignado al asistente
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Filtros base: solo presupuestos con código de centro de costo
        $filtros = [['cod_cc', '<>', null]];

        // Filtro por código de centro de costo si se especifica
        if ($this->cod_cc){
            array_push($filtros, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }

        // Filtro por año si se especifica
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        //Admin Gerencia
        $admin = Auth::user()->id == 10;
        // Para rol 1 (administrador): solo presupuestos con estado 4 (revision líder comercial)
        if ($this->rol == 1 && !$admin){
            $filtros[] = ['estado_id', 4];
        } else if($this->rol == 1 && $admin){
            $filtros[] = ['estado_id', 5];
        }

        //if ($this->rol == 1){ array_push($filtros, ['estado_id', 5]); }

        // Para rol 2 (comercial): solo presupuestos propios
        if ($this->rol == 2){
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->
                whereHas('gestion', function (Builder $query){
                    $query->where('id_user', Auth::id());
                })->paginate(15);
        }elseif ($this->rol == 5){
            // Para rol 5 (asistente): presupuestos del comercial asignado
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->
                whereHas('gestion', function (Builder $query){
                    $query->where('id_user', Asistente::select('comercial_id')->where('asistente_id', Auth::id())->first()->comercial_id);
                })->paginate(15);
        }

        // Para rol 1 (administrador): todos los presupuestos con actualizaciones
        if ($this->rol == 1){
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->paginate(15);
        }

        return view('livewire.admin.gestion-comercial.actualizaciones-presto', ['presupuestos' => $presupuestos]);
    }

    /**
     * Método de inicialización del componente
     * Se ejecuta cuando el componente es montado
     */
    public function mount(){
        $this->getEstados();
        $this->getAños();
    }

    /**
     * Obtiene la lista de estados de presupuesto disponibles
     * Excluye el estado con ID 3
     */
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }

    /**
     * Cambia el estado de un presupuesto
     * @param int|null $id ID del presupuesto
     * @param int|null $estado Nuevo estado del presupuesto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cambioEstado($id = null, $estado = null){
        $presupuesto = PresupuestoProyecto::find($id);
        $presupuesto->estado_id = $estado;

        // Si el estado es 1 (aprobado)
        if ($presupuesto->estado_id == 1){
            // Limpia las justificaciones
            $presupuesto->justificacion_compras = null;
            $presupuesto->justificacion = null;

            // Re-calcula los valores de la base y gestión comercial
            $this->reCalculate($presupuesto);

            // Envía notificación de aprobación
            $this->presupuestoAprobado($presupuesto->gestion->comercial, $presupuesto->gestion, null, $presupuesto->cod_cc);

            // Marca todos los ítems como no actualizados por defecto
            ItemPresupuesto::where('presupuesto_id', $id)->get()->map(function ($item){
                $item->actualizado = false;
                $item->update();
            });
        }elseif ($presupuesto->estado_id == 3){
            // Si el estado es 3 (rechazado), envía notificación de rechazo
            $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, null);
        }

        $presupuesto->update();
        return redirect()->route('actualizaciones')->with('success', 'Cambios guardados exitosamente');
    }

    /**
     * Recalcula los valores del presupuesto en la gestión comercial y base comercial
     * Actualiza los montos distribuidos entre los comerciales participantes
     * @param PresupuestoProyecto $presupuesto El presupuesto a recalcular
     */
    public function reCalculate($presupuesto){
        $prestosCom = [];

        // Actualiza el valor de cotización en la gestión
        $presupuesto->gestion->presto_cot = $presupuesto->venta_proy;
        $presupuesto->gestion->update();

        // Calcula el presupuesto del comercial principal (creador de la gestión)
        array_push($prestosCom, [
            'comercial_id' => $presupuesto->gestion->id_user,
            'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->porcentaje)/100
        ]);

        // Calcula el presupuesto de los comerciales participantes (2, 3 y 4)
        $i = 2;
        while($i < 5){
            array_push($prestosCom, [
                'comercial_id' => $presupuesto->gestion->{'comercial_'.$i},
                'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->{'porcentaje_'.$i})/100,
            ]);
            $i++;
        }

        // Actualiza los valores en la base comercial
        foreach ($presupuesto->gestion->baseComercial as $key => $base){
            if ($base->id_user == $prestosCom[$key]['comercial_id']){
                $base->valor_original = $presupuesto->venta_proy;
                $base->valor_proyecto = $prestosCom[$key]['presupuesto'];
                $base->update();
            }
        }
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
}
