<?php

namespace App\Http\Livewire\Cont\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\EstadoOrdenesCompra;
use Livewire\WithPagination;
use App\Models\Año;
use App\Models\User;
use App\Models\TipoOrdenCompra;

class Anticipos extends Component
{
    // Variables para filtros y búsqueda
    public $cod_cc, $fecha = 'desc', $estado, $año, $tipo, $productor, $cedula; 

    // Listas para selects y filtros
    public $estados = [], $años = [], $tipos = [], $productores = [];

    // Habilita paginación y define el tema de Bootstrap
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Renderiza la vista principal y aplica los filtros de búsqueda
    public function render()
    {
        $filtros = [];        

        // Filtra por estado
        if ($this->estado) {
            array_push($filtros, ['estado_id', $this->estado]);
        } else {
            array_push($filtros, ['estado_id', 5]);
        }

        // Filtra por año
        if ($this->año) {
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtra por tipo de orden
        if ($this->tipo) {
            array_push($filtros, ['tipo_oc', $this->tipo]);
        }

        // Construcción dinámica de la consulta
        $query = OrdenCompra::where($filtros);

        // Centro de Costos
        $query->when($this->cod_cc, function ($q) {
            $q->with('presupuesto')
            ->whereHas('presupuesto', function ($presto) {
                $presto->where('cod_cc', 'LIKE', "%{$this->cod_cc}%")
                        ->orWhere('id', 'LIKE', "%{$this->id}%");
            });
        });

        // Productor
        $query->when($this->productor, function ($q) {
            $q->where(function ($sub) {
                $sub->whereHas('presupuesto', function ($presupuesto) {
                    $presupuesto->where('productor', $this->productor);
                })
                ->orWhereHas('naturalInfo', function ($natural) {
                    $natural->where('productor_id', $this->productor);
                });
            });
        });

        // Búsqueda por cédula / tercero / documento
        $query->when($this->cedula, function ($q) {
            $term = $this->cedula;
            $q->where(function ($sub) use ($term) {
                $sub->whereHas('proveedor', function ($prov) use ($term) {
                    $prov->where('documento', 'LIKE', "%{$term}%")
                        ->orWhere('tercero', 'LIKE', "%{$term}%");
                })
                ->orWhereHas('naturalInfo.tercero', function ($tercero) use ($term) {
                    $tercero->where('cedula', 'LIKE', "%{$term}%");
                });
            });
        });

        // Ejecuta la consulta combinando todos los filtros aplicados
        $ordenes = $query->orderBy('created_at', $this->fecha)->paginate(15);

        return view('livewire.cont.produccion.anticipos', ['ordenes' => $ordenes]);
    }

    // Al montar el componente, carga los datos para los filtros
    public function mount(){
        $this->getEstados();
        $this->getAños();
        $this->getTipos();
        $this->getProductores();
    }

    // Obtiene la lista de productores para el filtro
    public function getProductores(){
        $this->productores = User::select('id', 'name')->where('rol', 7)->get();
    }

    // Obtiene la lista de tipos de orden de compra
    public function getTipos(){
        $this->tipos = TipoOrdenCompra::all();
    }

    // Obtiene la lista de estados de las órdenes (excepto el estado 3)
    public function getEstados(){
        $this->estados = EstadoOrdenesCompra::where('id', '<>', 3)->get();
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
}
