<?php

namespace App\Http\Livewire\Teso\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\EstadoOrdenesCompra;
use Livewire\WithPagination;
use App\Models\Año;
use App\Models\User;
use App\Models\TipoOrdenCompra;

class Anticipos extends Component
{
    // Modelos para los filtros y campos del formulario
    public $cod_cc, $fecha = 'desc', $estado, $año, $tipo, $productor, $cedula;

    // Variables útiles para los selectores y catálogos
    public $estados = [], $años = [], $tipos = [], $productores = [];

    use WithPagination; // Habilita la paginación de Livewire
    protected $paginationTheme = 'bootstrap'; // Usa el tema bootstrap para la paginación

    // Renderiza la vista principal del componente y filtra las órdenes según los filtros seleccionados
    public function render()
    {
        $query = OrdenCompra::query();

        // Filtra por estado
        if ($this->estado) {
            $query->where('estado_id', $this->estado);
        }

        // Filtra por año (rango de fechas del año)
        if ($this->año && $this->yearInfo) {
            $query->whereBetween('created_at', [
                $this->yearInfo->meses->first()->f_inicio,
                $this->yearInfo->meses->last()->f_fin
            ]);
        }

        // Filtra por tipo de orden
        if ($this->tipo) {
            $query->where('tipo_oc', $this->tipo);
        }

        // Filtro obligatorio: Solo órdenes con causal (anticipo) y sin comprobante de pago
        $query->whereNotNull('cod_causal')
            ->where('cod_causal', '<>', 'NULL')
            ->whereNull('archivo_comprobante_pago');

        // Filtra por código de centro de costos
        if ($this->cod_cc) {
            $query->whereHas('presupuesto', function ($presto) {
                $presto->where('cod_cc', 'LIKE', '%' . $this->cod_cc . '%');
            });
        }

        // NUEVO: Filtra por tercero / cédula / nit
        if ($this->cedula) {
            $term = trim($this->cedula);

            $query->where(function ($q) use ($term) {
                // Proveedor (nit o nombre de tercero)
                $q->whereHas('proveedor', function ($prov) use ($term) {
                    $prov->where('documento', 'LIKE', "%{$term}%")
                        ->orWhere('tercero', 'LIKE', "%{$term}%");
                })
                // Persona Natural (vía naturalInfo -> relación tercero)
                ->orWhereHas('naturalInfo.tercero', function ($tercero) use ($term) {
                    $tercero->where('cedula', 'LIKE', "%{$term}%");
                });
            });
        }

        // Filtra por productor
        if ($this->productor) {
            $query->where(function ($q) {
                $q->whereHas('presupuesto', function ($presupuesto) {
                    $presupuesto->where('productor', $this->productor);
                })
                ->orWhereHas('naturalInfo', function ($natural) {
                    $natural->where('productor_id', $this->productor);
                });
            });
        }

        // Ejecuta la consulta acumulada con ordenamiento y paginación
        $ordenes = $query->with('presupuesto')
            ->orderBy('created_at', $this->fecha)
            ->paginate(15);

        return view('livewire.teso.produccion.anticipos', ['ordenes' => $ordenes]);
    }

    // Método que se ejecuta al montar el componente, carga los catálogos y valores iniciales
    public function mount(){
        $this->getEstados();
        $this->getAños();
        $this->getTipos();
        $this->getProductores();
    }

    // Obtiene la lista de productores con rol 7
    public function getProductores(){
        $this->productores = User::select('id', 'name')->where('rol', 7)->get();
    }

    // Obtiene todos los tipos de orden de compra
    public function getTipos(){
        $this->tipos = TipoOrdenCompra::all();
    }

    // Obtiene todos los estados de orden de compra excepto el id 3
    public function getEstados(){
        $this->estados = EstadoOrdenesCompra::where('id', '<>', 3)->get();
    }

    // Obtiene todos los años y selecciona el año actual por defecto
    public function getAños(){
        $this->años = Año::all();
        /* CURRENT YEAR */
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
