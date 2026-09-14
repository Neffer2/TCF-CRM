<?php

namespace App\Http\Livewire\Tesoreria\Produccion;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Anticipo;

/** Lista de anticipos para tesorería: causados por pagar (estado 5) y ya pagados (estado 14). */
class Anticipos extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $vista = 'pendientes'; // pendientes | pagados

    public function updatedVista() { $this->resetPage(); }

    public function render()
    {
        $anticipos = $this->vista === 'pagados'
            ? Anticipo::where('estado_id', 14)->orderByDesc('fecha_comprobante_pago')->paginate(15)
            : Anticipo::where('estado_id', 5)->whereNull('comprobante_pago')->orderByDesc('id')->paginate(15);

        return view('livewire.tesoreria.produccion.anticipos', compact('anticipos'));
    }
}
