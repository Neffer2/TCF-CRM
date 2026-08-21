<?php

namespace App\Http\Livewire\LiderProduccion;

use App\Models\Anticipo;
use Livewire\Component;

class Anticipos extends Component
{
    public function render()
    {
        $anticipos = Anticipo::where('estado_id', 8)->paginate(15);
        return view('livewire.lider-produccion.anticipos', compact('anticipos'));
    }
}
