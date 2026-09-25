<?php

namespace App\Http\Livewire\LiderProduccion;

use App\Models\Anticipo;
use Livewire\Component;

class Anticipos extends Component
{
    public function render()
    {
        $anticipos = Anticipo::whereIn('estado_id', [8, 10])->with(['estado', 'proveedor', 'ordenCompra.presupuesto.gestion', 'presupuesto.gestion', 'productor_info'])->paginate(15);
        return view('livewire.lider-produccion.anticipos', compact('anticipos'));
    }
}
