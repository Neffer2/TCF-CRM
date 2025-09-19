<?php

namespace App\Http\Livewire\Admin\Produccion;

use Livewire\Component;
use App\Models\Anticipo;

class Anticipos extends Component
{
    public function render()
    {
        $anticipos = Anticipo::where('estado_id', 2)->paginate(15);
        return view('livewire.admin.produccion.anticipos', compact('anticipos'));
    }
}
 