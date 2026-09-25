<?php

namespace App\Http\Livewire\Contabilidad\Produccion;

use Livewire\Component;
use App\Models\Anticipo;

class Anticipos extends Component
{
    public function render()
    {
        $anticipos = Anticipo::where('estado_id', 1)->with(['estado', 'proveedor', 'ordenCompra.presupuesto.gestion', 'presupuesto.gestion', 'productor_info'])->paginate(15);
        return view('livewire.contabilidad.produccion.anticipos', compact('anticipos'));
    }
}
  