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
        // Filtra por estado si está seleccionado
        if ($this->estado){
            array_push($filtros, ['estado_id', $this->estado]);
        }else {
            array_push($filtros, ['estado_id', 5]);
        }

        // Filtra por año si está seleccionado (rango de fechas del año)
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtra por tipo de orden si está seleccionado
        if($this->tipo){
            array_push($filtros, ['tipo_oc', $this->tipo]);
        }

        // Si hay código de centro de costos, filtra por ese código en la relación presupuesto
        if ($this->cod_cc){
            $ordenes = OrdenCompra::with('presupuesto')
                ->whereHas('presupuesto', function ($presto) {
                    $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
                })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }else {
            // Si no hay código, filtra solo por los filtros generales
            $ordenes = OrdenCompra::where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }

        // Filtro por cedula: información natural
        if ($this->cedula) {
            $ordenes = OrdenCompra::where(function($query) {
                $query->WhereHas('naturalInfo', function ($natural) {
                    $natural->WhereHas('tercero', function ($tercero) {
                        $tercero->where('cedula', 'LIKE', "%$this->cedula%");
                    });
                });
            })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }

        // Si hay productor seleccionado, filtra por productor en presupuesto o naturalInfo
        if ($this->productor) {
            $ordenes = OrdenCompra::where(function($query) {
                $query->whereHas('presupuesto', function ($presupuesto) {
                    $presupuesto->where('productor', $this->productor);
                })
                ->orWhereHas('naturalInfo', function ($natural) {
                    $natural->where('productor_id', $this->productor);
                });
            })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }

        // Retorna la vista con las órdenes filtradas y paginadas
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
