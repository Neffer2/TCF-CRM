<?php

namespace App\Http\Livewire\Admin\Dashboard;

use App\Models\Año;
use App\Models\Base_comercial;
use App\Models\Mes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Panel a pantalla completa con el detalle de un estado de la venta
 * (ejecución por facturar, venta en ejecución o venta): los proyectos de la
 * base comercial en ese estado, agrupados por líder comercial y comercial,
 * con los mismos filtros del dashboard (año, mes, líder/comercial).
 * Se abre con la señal 'abrirEstado' que emite el bloque "Ventas por estado".
 */
class DetalleEstado extends Component
{
    const NOMBRES = [3 => 'Ejecución por facturar', 7 => 'Venta en ejecución', 6 => 'Venta'];
    const TONOS   = [3 => 'tone-warn', 7 => 'tone-neutral', 6 => 'tone-ok'];

    public $abierto = false;
    public $estadoId = null;
    public $titulo = '';
    public $periodo = '';
    public $total = 0;
    public $nProyectos = 0;
    public $nComerciales = 0;
    public $lideres = [];   // [{id, nombre, total, n, peso, comerciales: [{id, nombre, total, n, peso, proyectos: [...]}]}]

    protected $listeners = ['abrirEstado' => 'abrir'];

    public function abrir($estadoId, $filtros = [])
    {
        $this->estadoId = (int) $estadoId;
        $this->titulo = self::NOMBRES[$this->estadoId] ?? 'Estado';
        $this->calcular($filtros ?: []);
        $this->abierto = true;
        $this->dispatchBrowserEvent('crm-modal', ['abierto' => true]);
    }

    public function cerrar()
    {
        $this->abierto = false;
        $this->dispatchBrowserEvent('crm-modal', ['abierto' => false]);
    }

    private function calcular(array $f)
    {
        $this->lideres = []; $this->total = 0; $this->nProyectos = 0; $this->nComerciales = 0;

        // Periodo: el año (por descripción o id) y, si hay, el mes
        $año = null;
        if (!empty($f['año'])) {
            $año = Año::where('description', $f['año'])->first() ?: Año::find($f['año']);
        }
        if (!$año) { $año = Año::orderBy('created_at', 'desc')->first(); }
        $meses = $año ? Mes::where('ano_id', $año->id)->orderByRaw('CAST(identifier AS UNSIGNED)')->get() : collect();
        $mes = (!empty($f['mes']) && $meses->count()) ? $meses->firstWhere('id', (int) $f['mes']) : null;
        $desde = $mes ? $mes->f_inicio : optional($meses->first())->f_inicio;
        $hasta = $mes ? $mes->f_fin : optional($meses->last())->f_fin;
        $this->periodo = $mes ? $mes->description.' '.$año->description : ($año ? 'Año '.$año->description : 'Todo el histórico');

        $q = Base_comercial::query()->where('id_estado', $this->estadoId);
        if ($desde && $hasta) { $q->whereBetween('fecha', [$desde, $hasta]); }
        if (!empty($f['comerciales']) && is_array($f['comerciales'])) { $q->whereIn('id_user', $f['comerciales']); }
        elseif (!empty($f['comercial'])) { $q->where('id_user', (int) $f['comercial']); }

        $proyectos = $q->orderByDesc('valor_proyecto')->get(['id', 'fecha', 'nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_proyecto', 'id_user']);
        if ($proyectos->isEmpty()) { return; }

        $usuarios = User::whereIn('id', $proyectos->pluck('id_user')->unique())->get(['id', 'name'])->keyBy('id');
        // Líder de cada comercial (el primero si tuviera varios)
        $liderDe = DB::table('lider_comercial_user')->whereIn('comercial_id', $proyectos->pluck('id_user')->unique())
            ->orderBy('id')->get()->groupBy('comercial_id')->map(function ($r) { return (int) $r->first()->lider_id; });
        $nombresLideres = User::whereIn('id', $liderDe->values()->unique())->get(['id', 'name'])->keyBy('id');

        $grupos = [];
        foreach ($proyectos->groupBy('id_user') as $comercialId => $lista) {
            $liderId = $liderDe[$comercialId] ?? 0;
            $grupos[$liderId]['id'] = $liderId;
            $grupos[$liderId]['nombre'] = $liderId ? (optional($nombresLideres->get($liderId))->name ?: 'Líder') : 'Sin líder comercial asignado';
            $grupos[$liderId]['comerciales'][] = [
                'id' => (int) $comercialId,
                'nombre' => optional($usuarios->get($comercialId))->name ?: 'Comercial '.$comercialId,
                'total' => (float) $lista->sum('valor_proyecto'),
                'n' => $lista->count(),
                'proyectos' => $lista->map(function ($p) {
                    return ['fecha' => $p->fecha, 'cliente' => $p->nom_cliente, 'proyecto' => $p->nom_proyecto, 'cc' => $p->cod_cc ? trim(explode(' - ', $p->cod_cc, 2)[0]) : null, 'valor' => (float) $p->valor_proyecto];
                })->values()->all(),
            ];
        }

        $this->total = (float) $proyectos->sum('valor_proyecto');
        $this->nProyectos = $proyectos->count();
        $this->nComerciales = $proyectos->pluck('id_user')->unique()->count();

        $lideres = collect($grupos)->map(function ($g) {
            $coms = collect($g['comerciales'])->sortByDesc('total')->values();
            $maxCom = (float) ($coms->max('total') ?: 1);
            $g['comerciales'] = $coms->map(function ($c) use ($maxCom) { $c['peso'] = round($c['total'] / $maxCom * 100, 1); return $c; })->all();
            $g['total'] = (float) $coms->sum('total');
            $g['n'] = (int) $coms->sum('n');
            return $g;
        })->sortByDesc('total')->values();
        $this->lideres = $lideres->map(function ($g) {
            $g['peso'] = $this->total > 0 ? round($g['total'] / $this->total * 100, 1) : 0;
            return $g;
        })->all();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.detalle-estado');
    }
}
