<?php

namespace App\Http\Livewire\Teso\Produccion;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdenesTesoreria;
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
    public $cod_oc, $cod_cc, $fecha = 'desc', $estado, $año, $tipo, $productor, $documento;

    // Variables útiles para los selectores y catálogos
    public $estados = [], $años = [], $tipos = [], $productores = [];

    use WithPagination; // Habilita la paginación de Livewire
    protected $paginationTheme = 'bootstrap'; // Usa el tema bootstrap para la paginación

    // Renderiza la vista principal del componente y filtra las órdenes según los filtros seleccionados
    public function render()
    {
        $filtros = []; // Arreglo de filtros para la consulta

        // Filtra por estado si está seleccionado
        if ($this->estado){
            array_push($filtros, ['estado_id', $this->estado]);
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

        // Solo muestra órdenes con causal (anticipo)
        array_push($filtros, ['cod_causal', '<>', 'NULL']);

        // Si se ingresa código de centro de costos, filtra por ese código
        if ($this->cod_cc){
            $ordenes = OrdenCompra::with('presupuesto')
                ->whereHas('presupuesto', function ($presto) {
                    $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
                })->where($filtros)->whereNull('archivo_comprobante_pago')->orderBy('created_at', $this->fecha)->paginate(15);
        }else {
            // Si no hay filtro de centro de costos, consulta normal
            $ordenes = OrdenCompra::where($filtros)->whereNull('archivo_comprobante_pago')->orderBy('created_at', $this->fecha)->paginate(15);
        }

        // Filtro por código de orden de compra
        if ($this->cod_oc){
            $ordenes = OrdenCompra::where(function($query){
                $query->where('cod_oc', 'LIKE', "%$this->cod_oc%")
                    ->orWhere('id', 'LIKE', "%$this->cod_oc%");
            })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }

        // Filtro por documento
        if ($this->documento) {
            $ordenes = OrdenCompra::where(function($query) {
                $query->WhereHas('naturalInfo', function ($natural) {
                    $natural->WhereHas('tercero', function ($tercero) {
                        $tercero->where('cedula', 'LIKE', "%$this->documento%");
                    });
                })->orWhereHas('proveedor', function ($proveedor) {
                    $proveedor->where('documento', 'LIKE', "%$this->documento%");
                });
            })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }

        // Si se selecciona un productor, filtra por productor (en presupuesto o naturalInfo)
        if ($this->productor) {
            $ordenes = OrdenCompra::where(function($query) {
                $query->whereHas('presupuesto', function ($presupuesto) {
                    $presupuesto->where('productor', $this->productor);
                })
                ->orWhereHas('naturalInfo', function ($natural) {
                    $natural->where('productor_id', $this->productor);
                });
            })->where($filtros)->whereNull('archivo_comprobante_pago')->orderBy('created_at', $this->fecha)->paginate(15);
        }

        // Retorna la vista con las órdenes filtradas y paginadas
        return view('livewire.teso.produccion.anticipos', ['ordenes' => $ordenes]);
    }

    public function reporteExcel(){
        return Excel::download(new OrdenesTesoreria($this->yearInfo), "reporte_ordenes_tesoreria.xlsx");
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
