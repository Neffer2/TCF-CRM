<?php

namespace App\Http\Livewire\Produccion;

use App\Models\Anticipo;
use App\Services\AnticipoJuridicoFlujo as F;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Bandeja de anticipos jurídicos. Para el Controller: los que le toca revisar (después del líder de
 * producción, o devueltos por Contabilidad). Para Gerencia (permiso revisar-anticipos-gerencia): los que
 * superan el umbral y esperan su aprobación. En los dos casos, el histórico completo para consultar.
 */
class AnticiposJuridicos extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $vista = 'pendientes'; // pendientes | todos
    public $buscar = '';
    /** 'controller' o 'gerencia': lo fija el servidor según quién abre la bandeja. */
    public $bandeja = 'controller';

    public static function bandejaDe($user): string
    {
        if ($user && !$user->esControl() && $user->can('revisar-anticipos-gerencia')) { return 'gerencia'; }
        return 'controller';
    }

    public function updating($name, $value)
    {
        abort_if($name === 'bandeja', 403); // la fija el servidor
    }

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

        $this->bandeja = self::bandejaDe(auth()->user());
        $gerencia = $this->bandeja === 'gerencia';
        $estadosPendientes = $gerencia ? [F::REV_GERENCIA] : [F::REV_CONTROLLER, F::RECH_CONTABILIDAD];
        $pendientes = (clone $base)->whereIn('estado_id', $estadosPendientes);
        $conteo = (clone $pendientes)->count();
        $anticipos = ($this->vista === 'todos' ? $base->orderByDesc('id') : $pendientes->orderBy('fecha_solicitud'))->paginate(15);

        return view('livewire.produccion.anticipos-juridicos', [
            'anticipos' => $anticipos, 'conteo' => $conteo, 'gerencia' => $gerencia,
            'ruta' => $gerencia ? 'anticipo-admin' : 'anticipo-controller', 'estadosPendientes' => $estadosPendientes,
        ]);
    }
}
