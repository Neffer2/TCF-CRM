<?php

namespace App\Http\Livewire\Teso\Produccion; 

use Livewire\Component;
use App\Models\EstadoOrdenesCompra;   
use App\Models\OrdenCompra;
use Livewire\WithPagination;  
 
class Anticipos extends Component 
{    
    use WithPagination;  
    protected $paginationTheme = 'bootstrap';  
    
    public function render()  
    {   
        // WhereHas busca dentro de la colleccion el campo 'proveedor'
        $ordenes = OrdenCompra::with('proveedor')
        ->whereHas('proveedor', function ($proveedor){ $proveedor->where('anticipo', '>', 0); })
        ->where('estado_id', 1)->Where('archivo_comprobante_pago', 'NULL')->paginate(15); 
        return view('livewire.teso.produccion.anticipos', ['ordenes' => $ordenes]);
    }
}
 