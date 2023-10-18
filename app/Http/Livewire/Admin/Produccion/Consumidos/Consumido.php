<?php

namespace App\Http\Livewire\Admin\Produccion\Consumidos;

use Livewire\Component;
use App\Models\PresupuestoProyecto;

class Consumido extends Component
{

    // Useful vars
    public $presupuesto;

    // filled
    public $presupuesto_id;

    public function render()
    { 
        return view('livewire.admin.produccion.consumidos.consumido');
    }
    
    public function mount(){
        $this->presupuesto = PresupuestoProyecto::find($this->presupuesto_id);
        // dd($this->presupuesto);
    }
}
