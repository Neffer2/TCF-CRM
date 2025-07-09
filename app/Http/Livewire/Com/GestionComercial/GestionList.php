<?php

namespace App\Http\Livewire\Com\GestionComercial;

use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\EstadoGestionComercial;
use App\Models\PresupuestoProyecto;
use App\Models\Contacto;
use App\Models\Asistente;
use App\Models\Año;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class GestionList extends Component
{
    // Habilita la paginación y usa el tema de Bootstrap
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Variables de filtro y orden
    public $nomProyecto, $contacto, $estado, $año, $order = 'desc';

    // Variables auxiliares para selects y lógica
    public $estados = [], $contactos = [], $años = [], $yearInfo;
    // Listener para refrescar la lista cuando se emite el evento 'list'
    protected $listeners = ['list' => 'getData'];

    // Renderiza la vista principal con los filtros aplicados
    public function render()
    {
        $this->getData(); // Carga datos auxiliares para los selects
        $filtros = [];

        // Filtro por nombre de proyecto
        if ($this->nomProyecto){
            array_push($filtros, ['nom_proyecto_cot', 'like', "%$this->nomProyecto%"]);
        }

        // Filtro por contacto
        if ($this->contacto){
            array_push($filtros, ['id_contacto', $this->contacto]);
        }

        // Filtro por año: filtra por rango de fechas del año seleccionado
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtro por estado
        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }

        // Consulta los registros según el rol del usuario
        if (Auth::user()->rol == 2){
            // Comerciales ven sus propias gestiones y donde participan
            $datos = GestionComercial::select('id', 'id_contacto', 'id_estado', 'nom_proyecto_cot', 'presto_cot')->where(function ($query) {
                $query->where('id_user', Auth::user()->id)
                    ->orWhere('comercial_2', Auth::user()->id)
                    ->orWhere('comercial_3', Auth::user()->id)
                    ->orWhere('comercial_4', Auth::user()->id);
            })->where($filtros)->orderBy('created_at', $this->order)->paginate(15);

        }else if(Auth::user()->rol == 5){
            // Asistentes ven las gestiones del comercial asignado
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $datos = GestionComercial::select('id','id_contacto','id_estado', 'nom_proyecto_cot', 'presto_cot')->where(function ($query) use ($asistente) {
                $query->where('id_user', $asistente->comercial_id)
                    ->orWhere('comercial_2', $asistente->comercial_id)
                    ->orWhere('comercial_3', $asistente->comercial_id)
                    ->orWhere('comercial_4', $asistente->comercial_id);
            })->where($filtros)->orderBy('created_at', $this->order)->paginate(15);
        }

        // Retorna la vista con los datos filtrados y paginados
        return view('livewire.com.gestion-comercial.gestion-list', ['datos' => $datos]);
    }

    // Carga los estados y contactos según el rol del usuario
    public function getData(){
        $this->estados = EstadoGestionComercial::select('id', 'description')->get();

        if(Auth::user()->rol == 2){
            // Comerciales ven solo sus propios contactos
            $this->contactos = Contacto::where('id_user', Auth::id())->get();
        }else if(Auth::user()->rol == 5){
            // Asistentes ven los contactos del comercial asignado
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $this->contactos = Contacto::where('id_user', $asistente->comercial_id)->get();
        }
    }

    // Inicializa el componente y carga los años disponibles
    public function mount (){
        $this->getAños();
    }

    // Obtiene la lista de años y selecciona el año actual por defecto
    public function getAños(){
        $this->años = Año::all();
        /* CURRENT YEAR */
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    // Cuando se cambia el año, valida y actualiza la información del año seleccionado
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        $this->yearInfo = Año::find($this->año);
    }
}
