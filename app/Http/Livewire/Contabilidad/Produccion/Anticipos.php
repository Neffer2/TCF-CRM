<?php

namespace App\Http\Livewire\Contabilidad\Produccion;

use Livewire\Component;
use App\Models\Anticipo;

class Anticipos extends Component
{
    public function render()
    {
        $anticipos = Anticipo::where('estado_id', 1)->paginate(15);
        return view('livewire.contabilidad.produccion.anticipos', compact('anticipos'));
    }
}
  