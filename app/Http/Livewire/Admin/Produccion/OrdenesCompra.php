<?php

namespace App\Http\Livewire\Admin\Produccion;

use Livewire\Component; 
use App\Models\OrdenCompra;
use App\Models\EstadoOrdenesCompra;
use Livewire\WithPagination;

class OrdenesCompra extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Models
    public $cod_cc, $fecha = 'desc', $estado;
     
    // Useful vars
    public $estados = []; 

    public function render(){    
        $filtros = []; 
        if ($this->estado){
            array_push($filtros, ['estado_id', $this->estado]);
        }
        
        if ($this->cod_cc){
            $ordenes = OrdenCompra::with('presupuesto')
                ->whereHas('presupuesto', function ($presto) { 
                    $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
                })->where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }else {
            $ordenes = OrdenCompra::where($filtros)->orderBy('created_at', $this->fecha)->paginate(15);
        }
        
        return view('livewire.admin.produccion.ordenes-compra', ['ordenes' => $ordenes]);
    }

    public function mount(){
        $this->getEstados();
    }

    public function getEstados(){
        $this->estados = EstadoOrdenesCompra::where('id', '<>', 3)->get();
    }
}
 