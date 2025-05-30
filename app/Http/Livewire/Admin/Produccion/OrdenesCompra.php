<?php

namespace App\Http\Livewire\Admin\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\EstadoOrdenesCompra;
use App\Models\NaturalInfo;
use Livewire\WithPagination;
use App\Models\Año;
use App\Models\TipoOrdenCompra;

class OrdenesCompra extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Models
    public $cod_cc, $fecha = 'desc', $estado, $año, $tipo;

    // Useful vars
    public $estados = [], $años = [], $tipos = [];

    // Filled
    public $productor_id;

    public function render(){
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

        if ($this->cod_cc){
            $ordenes = OrdenCompra::with('presupuesto')
                ->whereHas('presupuesto', function ($presto) {
                    $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
                })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }else {
            $ordenes = OrdenCompra::where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }

        if ($this->productor_id){
            $ordenes = OrdenCompra::whereHas('naturalInfo', function ($natural) {
                        $natural->where('productor_id', $this->productor_id);
                    })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);

            return view('livewire.admin.produccion.ordenes-compra', ['ordenes' => $ordenes]);
        }

        return view('livewire.admin.produccion.ordenes-compra', ['ordenes' => $ordenes]);
    }

    public function mount(){
        $this->getEstados();
        $this->getAños();
        $this->getTipos();
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
