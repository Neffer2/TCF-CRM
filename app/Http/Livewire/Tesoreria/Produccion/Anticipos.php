<?php

namespace App\Http\Livewire\Tesoreria\Produccion;

use Livewire\Component;
use App\Models\Anticipo;

class Anticipos extends Component
{
    public function render()
    {
        $anticipos = Anticipo::where([
            ['comprobante_pago', null],
            ['estado_id', 5]
        ])->paginate(15);
        return view('livewire.tesoreria.produccion.anticipos', compact('anticipos'));
    }
}
 