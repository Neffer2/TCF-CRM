<?php

namespace App\Http\Livewire\Admin\Produccion;

use Livewire\Component; 
use App\Models\OrdenCompra;
use Livewire\WithPagination;

class OrdenesCompra extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render() 
    {
        $ordenes = OrdenCompra::paginate(15);
        return view('livewire.admin.produccion.ordenes-compra', ['ordenes' => $ordenes]);
    }

    public function mount(){
        
    }
}
 