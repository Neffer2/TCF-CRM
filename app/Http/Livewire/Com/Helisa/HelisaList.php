<?php

namespace App\Http\Livewire\Com\Helisa;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Helisa;
use App\Models\Año;
use App\Models\Mes;
use App\Models\Cuenta;
use Illuminate\Support\Facades\Auth;

class HelisaList extends Component
{
    // Habilita la paginación y usa el tema de Bootstrap
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Variables de filtro y orden
    public $año, $centro, $orderBy = 'DESC', $mes;

    // Variables auxiliares para selects y lógica
    public $cuentas = [], $años = [], $meses = [], $yearInfo;

    // Renderiza la vista principal con los filtros aplicados
    public function render()
    {
        // Filtro base: solo registros del usuario autenticado
        $filtros = [['comercial', Auth::user()->id]];

        // Filtro por centro de costos si se selecciona
        if ($this->centro){
            array_push($filtros, ['centro', 'LIKE', "%$this->centro%"]);
        }

        // Filtro por mes si se selecciona
        if ($this->mes){
            array_push($filtros, ['mes', 'LIKE', "%$this->mes%"]);
        }

        // Filtro por año: filtra por rango de fechas del año seleccionado
        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Consulta los registros Helisa con los filtros y paginación
        $registrosHelisa = Helisa::where($filtros)->orderBy('created_at', $this->orderBy)->paginate(15);
        return view('livewire.com.helisa.helisa-list', ['registrosHelisa' => $registrosHelisa]);
    }

    // Elimina un registro Helisa por ID
    public function delete_registro($registro_id){
        Helisa::find($registro_id)->delete();
        return redirect()->back()->with('success', 'Registro eliminado exitosamente.');
    }

    // Inicializa el componente y carga datos para los selects
    public function mount(){
        $this->getAños();
        $this->getMeses();
        $this->getCuentas();
    }

    // Obtiene la lista de meses del año seleccionado
    public function getMeses(){
        $this->meses = Mes::select('id','description')->where('ano_id', '<', $this->yearInfo->id)->get();
    }

    // Obtiene la lista de cuentas contables
    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    // Obtiene la lista de años y selecciona el año actual por defecto
    public function getAños(){
        $this->años = Año::all();
        /* Año actual */
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
