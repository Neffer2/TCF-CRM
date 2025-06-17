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
    // Models
    public $cod_cc, $fecha = 'desc', $estado, $año, $tipo, $productor;

    // Useful vars
    public $estados = [], $años = [], $tipos = [], $productores = [];

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $filtros = [];

        if ($this->estado){
            array_push($filtros, ['estado_id', $this->estado]);
        }

        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        if($this->tipo){
            array_push($filtros, ['tipo_oc', $this->tipo]);
        }

        array_push($filtros, ['cod_causal', '<>', 'NULL']);

        if ($this->cod_cc){
            $ordenes = OrdenCompra::with('presupuesto')
                ->whereHas('presupuesto', function ($presto) {
                    $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
                })->where($filtros)->whereNull('archivo_comprobante_pago')->orderBy('created_at', $this->fecha)->paginate(15);
        }else {
            $ordenes = OrdenCompra::where($filtros)->whereNull('archivo_comprobante_pago')->orderBy('created_at', $this->fecha)->paginate(15);
        }

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

        return view('livewire.teso.produccion.anticipos', ['ordenes' => $ordenes]);
    }

    public function mount(){
        $this->getEstados();
        $this->getAños();
        $this->getTipos();
        $this->getProductores();
    }

    public function getProductores(){
        $this->productores = User::select('id', 'name')->where('rol', 7)->get();
    }

    public function getTipos(){
        $this->tipos = TipoOrdenCompra::all();
    }

    public function getEstados(){
        $this->estados = EstadoOrdenesCompra::where('id', '<>', 3)->get();
    }

    public function getAños(){
        $this->años = Año::all();
        /* CURRENT YEAR */
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        $this->yearInfo = Año::find($this->año);
    }
}
