<?php

namespace App\Http\Livewire\Admin\Dashboard;

use App\Models\Año;
use App\Models\Helisa;
use App\Models\Mes;
use App\Models\Presupuesto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Ranking del periodo: venta facturada (Helisa) frente al presupuesto y % de
 * cumplimiento. Tres vistas: comerciales por cumplimiento, comerciales por
 * venta, y líderes comerciales (la venta y la meta de todo su equipo, según
 * lider_comercial_user). Escucha la señal 'Ranking' de Filters.
 */
class Ranking extends Component
{
    public $año_id;
    public $mes_id;
    public $comercial;          // comercial elegido en filtros (se resalta)
    public $lider;              // líder elegido en filtros (se resalta / acota)
    public $comerciales = null; // ids con los que filtrar (null = todos)
    public $alcance = '';
    public $orden = 'cumplimiento';   // 'cumplimiento' | 'venta' | 'lider'
    public $filas = [];
    public $periodo = '';

    protected $listeners = ['Ranking' => 'actualizar'];

    public function mount()
    {
        $año = Año::orderBy('created_at', 'desc')->first();
        $this->año_id = $año ? $año->id : null;
        $this->calcular();
    }

    public function actualizar($filtros = null)
    {
        if ($filtros) {
            $this->año_id = $filtros['año_id'] ?? $this->año_id;
            $this->mes_id = $filtros['mes'] ?: null;
            $this->comercial = $filtros['comercial'] ?: null;
            $this->lider = $filtros['lider'] ?: null;
            $this->comerciales = $filtros['comerciales'] ?? null;
            $this->alcance = $filtros['alcance'] ?? '';
        } else {
            $año = Año::orderBy('created_at', 'desc')->first();
            $this->año_id = $año ? $año->id : null;
            $this->mes_id = null;
            $this->comercial = null;
            $this->lider = null;
            $this->comerciales = null;
            $this->alcance = '';
        }
        $this->calcular();
    }

    public function ordenar($modo)
    {
        $this->orden = in_array($modo, ['venta', 'lider'], true) ? $modo : 'cumplimiento';
        $this->calcular();
    }

    public function calcular()
    {
        $this->filas = [];
        $año = $this->año_id ? Año::find($this->año_id) : null;
        if (!$año) { return; }

        $meses = Mes::where('ano_id', $año->id)->orderByRaw('CAST(identifier AS UNSIGNED)')->get();
        if ($meses->isEmpty()) { return; }

        // Periodo: el mes elegido, o de enero al último mes del año
        $mes = $this->mes_id ? $meses->firstWhere('id', $this->mes_id) : null;
        $desde = $mes ? $mes->f_inicio : $meses->first()->f_inicio;
        $hasta = $mes ? $mes->f_fin : $meses->last()->f_fin;
        $mesIds = $mes ? [$mes->id] : $meses->pluck('id')->all();
        $this->periodo = $mes ? $mes->description.' '.$año->description : 'Año '.$año->description;

        // Venta facturada por comercial (misma definición que el KPI de venta facturada)
        $ventas = Helisa::select('comercial', DB::raw('SUM(base_factura) AS venta'))
            ->where('año', $año->description)
            ->whereBetween('fecha', [$desde, $hasta])
            ->whereNotNull('comercial')
            ->groupBy('comercial')
            ->pluck('venta', 'comercial');

        // Presupuesto por comercial en el mismo periodo
        $metas = Presupuesto::select('id_user', DB::raw('SUM(valor) AS meta'))
            ->where('ano_id', $año->id)
            ->whereIn('mes_id', $mesIds)
            ->groupBy('id_user')
            ->pluck('meta', 'id_user');

        $this->filas = $this->orden === 'lider'
            ? $this->filasLideres($ventas, $metas)
            : $this->filasComerciales($ventas, $metas);
    }

    /** Filas de comerciales (acotadas a la lista de filtros si la hay). */
    private function filasComerciales($ventas, $metas)
    {
        $ids = $ventas->keys()->merge($metas->keys())->unique()->filter()->values();
        if (is_array($this->comerciales)) {
            $ids = $ids->intersect($this->comerciales)->values();
        }
        if ($ids->isEmpty()) { return []; }
        $usuarios = User::whereIn('id', $ids)->where('rol', '!=', 4)->get()->keyBy('id');

        $filas = collect();
        foreach ($ids as $id) {
            $u = $usuarios->get($id);
            if (!$u) { continue; }  // suspendidos o cuentas eliminadas no compiten
            $venta = (float) ($ventas[$id] ?? 0);
            $meta = (float) ($metas[$id] ?? 0);
            if ($venta <= 0 && $meta <= 0) { continue; }
            $filas->push([
                'id' => (int) $id,
                'nombre' => $u->name,
                'avatar' => $u->avatar ?: null,
                'equipo' => null,
                'venta' => $venta,
                'presupuesto' => $meta,
                'cumplimiento' => $meta > 0 ? round($venta / $meta * 100, 1) : null,
                'seleccionada' => $this->comercial == $id,
            ]);
        }

        $filas = $this->orden === 'venta'
            ? $filas->sortByDesc('venta')
            : $filas->sortByDesc(function ($f) { return $f['cumplimiento'] ?? -1; });

        return $this->pesar($filas->take(8)->values());
    }

    /** Filas de líderes: suma de la venta y la meta de los comerciales a su cargo. */
    private function filasLideres($ventas, $metas)
    {
        $equipos = DB::table('lider_comercial_user')->select('lider_id', 'comercial_id')->get()->groupBy('lider_id');
        if ($equipos->isEmpty()) { return []; }
        $lideres = User::whereIn('id', $equipos->keys())->get()->keyBy('id');

        $filas = collect();
        foreach ($equipos as $liderId => $rel) {
            $l = $lideres->get($liderId);
            if (!$l) { continue; }
            $equipo = $rel->pluck('comercial_id')->unique();
            $venta = (float) $equipo->sum(function ($id) use ($ventas) { return (float) ($ventas[$id] ?? 0); });
            $meta = (float) $equipo->sum(function ($id) use ($metas) { return (float) ($metas[$id] ?? 0); });
            $filas->push([
                'id' => (int) $liderId,
                'nombre' => $l->name,
                'avatar' => $l->avatar ?: null,
                'equipo' => $equipo->count(),
                'venta' => $venta,
                'presupuesto' => $meta,
                'cumplimiento' => $meta > 0 ? round($venta / $meta * 100, 1) : null,
                'seleccionada' => $this->lider == $liderId,
            ]);
        }

        return $this->pesar($filas->sortByDesc(function ($f) { return $f['cumplimiento'] ?? -1; })->values());
    }

    /** Ancho de la barra de cada fila, relativo al máximo de la lista. */
    private function pesar($filas)
    {
        $porVenta = $this->orden === 'venta';
        $max = $porVenta
            ? (float) ($filas->max('venta') ?: 1)
            : (float) max(100, $filas->max('cumplimiento') ?: 1);

        return $filas->map(function ($f) use ($max, $porVenta) {
            $base = $porVenta ? $f['venta'] : ($f['cumplimiento'] ?? 0);
            $f['peso'] = round(max(0, $base) / $max * 100, 1);
            return $f;
        })->all();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.ranking');
    }
}
