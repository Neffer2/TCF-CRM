<?php

namespace App\Http\Livewire\Produccion;

use App\Models\Anticipo;
use App\Services\AnticipoJuridicoFlujo as F;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Bandeja del Controller: anticipos jurídicos que le toca revisar (después del líder de
 * producción, o devueltos por Contabilidad) y el histórico completo para consultar.
 */
class AnticiposJuridicos extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $vista = 'pendientes'; // pendientes | todos
    public $buscar = '';

    public function updatedVista() { $this->resetPage(); }
    public function updatedBuscar() { $this->resetPage(); }

    public function render()
    {
        $t = trim($this->buscar);
        $base = Anticipo::with(['estado', 'proveedor', 'ordenCompra', 'presupuesto', 'productor_info'])
            ->where('tipo', Anticipo::TIPO_JURIDICO)
            ->when($t !== '', function ($q) use ($t) {
                $q->where(function ($w) use ($t) {
                    $w->whereHas('proveedor', fn ($p) => $p->where('tercero', 'like', "%{$t}%")->orWhere('documento', 'like', "{$t}%"))
                      ->orWhereHas('presupuesto', fn ($p) => $p->where('cod_cc', 'like', "%{$t}%"))
                      ->orWhere('cod_orden_anticipo', 'like', "%{$t}%");
                    if (ctype_digit($t)) { $w->orWhere('id', (int) $t); }
                });
            });

        $pendientes = (clone $base)->whereIn('estado_id', [F::REV_CONTROLLER, F::RECH_CONTABILIDAD]);
        $conteo = (clone $pendientes)->count();
        $anticipos = ($this->vista === 'todos' ? $base->orderByDesc('id') : $pendientes->orderBy('fecha_solicitud'))->paginate(15);

        return view('livewire.produccion.anticipos-juridicos', compact('anticipos', 'conteo'));
    }
}
