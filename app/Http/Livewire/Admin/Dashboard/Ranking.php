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
 * Ranking de comerciales del periodo: venta facturada (Helisa) frente a su
 * presupuesto y % de cumplimiento. Ordenable por venta o por cumplimiento.
 * Escucha la señal 'Ranking' de Filters.
 */
class Ranking extends Component
{
    public $año_id;
    public $mes_id;
    public $comercial;
    public $orden = 'cumplimiento';   // 'cumplimiento' | 'venta'
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
        } else {
            $año = Año::orderBy('created_at', 'desc')->first();
            $this->año_id = $año ? $año->id : null;
            $this->mes_id = null;
            $this->comercial = null;
        }
        $this->calcular();
    }

    public function ordenar($modo)
    {
        $this->orden = $modo === 'venta' ? 'venta' : 'cumplimiento';
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

        $ids = $ventas->keys()->merge($metas->keys())->unique()->filter()->values();
        if ($ids->isEmpty()) { return; }
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
                'venta' => $venta,
                'presupuesto' => $meta,
                'cumplimiento' => $meta > 0 ? round($venta / $meta * 100, 1) : null,
            ]);
        }

        $filas = $this->orden === 'venta'
            ? $filas->sortByDesc('venta')
            : $filas->sortByDesc(function ($f) { return $f['cumplimiento'] ?? -1; });

        $filas = $filas->take(8)->values();
        $max = $this->orden === 'venta'
            ? (float) ($filas->max('venta') ?: 1)
            : (float) max(100, $filas->max('cumplimiento') ?: 1);

        $this->filas = $filas->map(function ($f) use ($max) {
            $base = $this->orden === 'venta' ? $f['venta'] : ($f['cumplimiento'] ?? 0);
            $f['peso'] = round(max(0, $base) / $max * 100, 1);
            return $f;
        })->all();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.ranking');
    }
}
