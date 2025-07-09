<?php

namespace App\Http\Livewire\Com;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Año;
use App\Models\Cuenta;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;

class BaseList extends Component
{
    // Habilita la paginación y define el tema de Bootstrap
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Variables para filtros y búsqueda
    public $comercial, $centro, $nomProyecto, $estado, $año, $orderBy = 'DESC';
    // Variables útiles para selects y lógica
    public $cuentas = [], $user_id, $comerciales = [], $estados = [], $años = [], $yearInfo;

    // Listener para recargar el componente cuando se agrega un proyecto
    protected $listeners = ['proyectoAdded' => 'mount'];

    // Renderiza la vista principal y aplica los filtros de búsqueda
    public function render()
    {
        $filtros = [['id_user', $this->user_id]];

        // Filtra por centro de costos si está seleccionado
        if($this->centro){
            array_push($filtros, ['cod_cc', 'LIKE', "%$this->centro%"]);
        }

        // Filtra por nombre de proyecto si está seleccionado
        if ($this->nomProyecto){
            array_push($filtros, ['nom_proyecto', 'LIKE', "%$this->nomProyecto%"]);
        }

        // Filtra por año (rango de fechas del año)
        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtra por estado si está seleccionado
        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }

        // Filtra por comercial si está seleccionado
        if($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }

        // Consulta los proyectos con los filtros aplicados y los pagina
        $proyectos = Base_comercial::where($filtros)->orderBy('created_at', $this->orderBy)->paginate(15);
        return view('livewire.com.base-list', ['proyectos' => $proyectos]);
    }

    // Al montar el componente, inicializa los datos y filtros
    public function mount ($user_id){
        $this->user_id = $user_id;
        $this->getEstados();
        $this->getCuentas();
        $this->getAños();
    }

    // Obtiene la lista de estados de cuenta para el filtro
    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->get();
    }

    // Obtiene la lista de cuentas para el filtro
    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    // Obtiene la lista de años y selecciona el año actual por defecto
    public function getAños(){
        $this->años = Año::all();
        /* Año actual por defecto */
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    // Cuando se actualiza el año, valida y carga la información del año seleccionado
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        $this->yearInfo = Año::find($this->año);
    }

    // Exporta los proyectos filtrados a un archivo Excel
    public function exportar(){
        $filtros = [['id_user', $this->user_id]];

        // Filtra por comercial si está seleccionado
        if($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }

        // Filtra por año (rango de fechas del año)
        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtra por estado si está seleccionado
        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }

        // Descarga el archivo Excel con los filtros aplicados
        return Excel::download(new BaseExport(['filtros' => $filtros]), 'Reporte Base Comercial.xlsx');
    }
}
