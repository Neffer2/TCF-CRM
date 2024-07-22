<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\OrdenCompra;

class ConsultaTerceros extends Component
{
    // Models
    public $numOrden;

    public function render()
    {
        $filtro = [];
        $orden = null;

        if ($this->numOrden) {
            array_push($filtro, ['id', $this->numOrden]);
            $orden = OrdenCompra::where($filtro)->first();
        }

        return view('livewire.productor.terceros.consulta-terceros', ['orden' => $orden]);
    } 

    
}
