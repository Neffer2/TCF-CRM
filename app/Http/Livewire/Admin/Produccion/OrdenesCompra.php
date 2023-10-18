<?php

namespace App\Http\Livewire\Admin\Produccion;

use Livewire\Component; 
use App\Models\OrdenCompra;
use Livewire\WithPagination;

class OrdenesCompra extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Models
    public $cod_cc, $fecha = 'desc';

    public function render(){    
        $filtros = [];
        array_push($filtros, ['estado_id', '2']);
        
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
}
 