<?php

namespace App\Http\Livewire\Admin\Produccion;

use Livewire\Component;
use App\Models\Anticipo;

class Anticipos extends Component
{
    public function render()
    {
        $anticipos = Anticipo::whereIn('estado_id', [2, 9])->with(['estado', 'proveedor', 'ordenCompra.presupuesto.gestion', 'presupuesto.gestion', 'productor_info'])->paginate(15);
        return view('livewire.admin.produccion.anticipos', compact('anticipos'));
    }
}
