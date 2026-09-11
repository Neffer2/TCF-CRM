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
    public $cod_cc, $fecha = 'desc', $estado, $año, $tipo, $productor, $documento;

    // Listas para selects y filtros
    public $estados = [], $años = [], $tipos = [], $productores = [];
 
    // Habilita paginación y define el tema de Bootstrap
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Renderiza la vista principal y aplica los filtros de búsqueda.
    // Los filtros son ACUMULATIVOS sobre una sola query (antes cada filtro
    // reconstruía la consulta descartando los anteriores) y la información
    // del año se resuelve aquí mismo ($yearInfo no era una propiedad
    // persistida por Livewire y crasheaba al paginar o filtrar).
    public function render()
    {
        $query = OrdenCompra::query();

        // Filtra por estado
        if ($this->estado){
            $query->where('estado_id', $this->estado);
        }

        // Filtra por año (rango de fechas del año)
        $yearInfo = $this->año ? Año::find($this->año) : null;
        if ($yearInfo && $yearInfo->meses->isNotEmpty()){
            $query->whereBetween('created_at', [
                $yearInfo->meses->first()->f_inicio,
                $yearInfo->meses->last()->f_fin
            ]);
        }

        // Filtra por tipo de orden
        if($this->tipo){
            $query->where('tipo_oc', $this->tipo);
        }

        // Filtra por código de centro de costos
        if ($this->cod_cc){
            $query->whereHas('presupuesto', function ($presto) {
                $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
            });
        }

        // Filtro por documento (cédula del tercero o NIT del proveedor)
        if ($this->documento) {
            $query->where(function($q) {
                $q->whereHas('naturalInfo.tercero', function ($tercero) {
                    $tercero->where('cedula', 'LIKE', "%$this->documento%");
                })->orWhereHas('proveedor', function ($proveedor) {
                    $proveedor->where('documento', 'LIKE', "%$this->documento%");
                });
            });
        }

        // Filtra por productor (en presupuesto o naturalInfo)
        if ($this->productor) {
            $query->where(function($q) {
                $q->whereHas('presupuesto', function ($presupuesto) {
                    $presupuesto->where('productor', $this->productor);
                })
                ->orWhereHas('naturalInfo', function ($natural) {
                    $natural->where('productor_id', $this->productor);
                });
            });
        }

        $ordenes = $query->with('presupuesto')
            ->orderBy('created_at', $this->fecha)
            ->paginate(15);

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
    }

    // Cuando se actualiza el año, solo valida: la información del año se
    // resuelve en render() a partir de $this->año en cada request.
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);
    }
}
