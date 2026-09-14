<?php

namespace App\Http\Livewire\Admin\Dashboard;

use App\Models\Año;
use App\Models\Helisa;
use App\Models\Mes;
use App\Models\Presupuesto;
use Livewire\Component;

/**
 * Tendencia mensual del dashboard de gerencia: venta facturada (Helisa)
 * frente al presupuesto, mes a mes del año seleccionado, opcionalmente
 * para un solo comercial. Escucha la señal 'Tendencia' de Filters.
 */
class Tendencia extends Component
{
    public $año_id;
    public $comercial;
    public $comerciales = null; // ids con los que filtrar (un comercial o el equipo de un líder); null = todos
    public $comercialesFijos = null; // dashboard del comercial: se monta con [su id] y nunca se amplía
    public $alcance = '';
    public $labels = [];
    public $venta = [];
    public $presupuesto = [];
    public $añoDescripcion = '';
    public $pronostico = [];   // por mes: null en meses cerrados, valor estimado en el mes en curso y los que faltan
    public $cierre = null;     // ['total','presupuesto','pct','anterior','variacion','base','mesesReales']

    protected $listeners = ['Tendencia' => 'actualizar'];

    public function mount($comercialesFijos = null)
    {
        $this->comercialesFijos = $comercialesFijos;
        $this->comerciales = $comercialesFijos;
        $año = Año::orderBy('created_at', 'desc')->first();
        $this->año_id = $año ? $año->id : null;
        $this->calcular();
    }

    public function actualizar($filtros = null)
    {
        if ($filtros) {
            $this->año_id = $filtros['año_id'] ?? $this->año_id;
            $this->comercial = $filtros['comercial'] ?? null;
            $this->comerciales = $filtros['comerciales'] ?? ($this->comercial ? [(int) $this->comercial] : null);
            if (is_array($this->comercialesFijos)) { $this->comerciales = $this->comercialesFijos; }
            $this->alcance = $filtros['alcance'] ?? '';
        } else {
            $año = Año::orderBy('created_at', 'desc')->first();
            $this->año_id = $año ? $año->id : null;
            $this->comercial = null;
            $this->comerciales = $this->comercialesFijos;
            $this->alcance = '';
        }
        $this->calcular();
    }

    public function calcular()
    {
        $this->labels = [];
        $this->venta = [];
        $this->presupuesto = [];

        $año = $this->año_id ? Año::find($this->año_id) : null;
        if (!$año) {
            return;
        }
        $this->añoDescripcion = $año->description;

        // identifier es texto: ordenar numéricamente (si no, Ene, Oct, Nov, Dic, Feb...)
        $meses = Mes::where('ano_id', $año->id)->orderByRaw('CAST(identifier AS UNSIGNED)')->get();
        foreach ($meses as $mes) {
            // Misma definición que Block1::getVentaFacturada (Helisa.base_factura por fecha)
            $venta = Helisa::where('año', $año->description)
                ->when(is_array($this->comerciales), function ($q) { $q->whereIn('comercial', $this->comerciales); })
                ->whereBetween('fecha', [$mes->f_inicio, $mes->f_fin])
                ->sum('base_factura');

            // Misma definición que Block1::getPresupuesto (presupuestos.valor por mes)
            $presupuesto = Presupuesto::where('ano_id', $año->id)
                ->where('mes_id', $mes->id)
                ->when(is_array($this->comerciales), function ($q) { $q->whereIn('id_user', $this->comerciales); })
                ->sum('valor');

            $this->labels[] = mb_substr($mes->description, 0, 3);
            $this->venta[] = round((float) $venta);
            $this->presupuesto[] = round((float) $presupuesto);
        }

        $this->pronosticar($año, $meses);
    }

    /**
     * Pronóstico de cierre del año en curso por estacionalidad: cómo se
     * repartió la venta mes a mes en los años anteriores (promedio de sus
     * participaciones) aplicado al ritmo real de este año.
     *
     *   cierre = venta real de los meses cerrados ÷ participación histórica de esos meses
     *   mes futuro = cierre × participación histórica del mes
     *
     * Solo aplica al año calendario actual; el mes en curso cuenta como incompleto.
     */
    private function pronosticar($año, $meses)
    {
        $this->pronostico = array_fill(0, count($this->venta), null);
        $this->cierre = null;
        if ((int) $año->description !== (int) now()->year || $meses->count() < 12) { return; }

        $mesActual = (int) now()->month;
        $ids = $meses->map(function ($m) { return (int) $m->identifier; })->all();

        // Participación histórica por mes (promedio de los años anteriores con datos)
        $ventaPorAño = function ($a, $conFiltro) {
            $ms = Mes::where('ano_id', $a->id)->orderByRaw('CAST(identifier AS UNSIGNED)')->get();
            if ($ms->count() < 12) { return null; }
            $porMes = [];
            foreach ($ms as $m) {
                $porMes[(int) $m->identifier] = (float) Helisa::where('año', $a->description)
                    ->when($conFiltro && is_array($this->comerciales), function ($q) { $q->whereIn('comercial', $this->comerciales); })
                    ->whereBetween('fecha', [$m->f_inicio, $m->f_fin])->sum('base_factura');
            }
            return array_sum($porMes) > 0 ? $porMes : null;
        };
        $anteriores = Año::where('description', '<', $año->description)->orderBy('description', 'desc')->get();
        $participacion = array_fill(1, 12, 0.0); $base = 0; $ventaAnterior = null;
        foreach ([true, false] as $conFiltro) {   // primero con el filtro de comerciales; si no hay historia, la global
            foreach ($anteriores as $a) {
                $porMes = $ventaPorAño($a, $conFiltro);
                if (!$porMes) { continue; }
                $tot = array_sum($porMes);
                foreach ($porMes as $i => $v) { $participacion[$i] += $v / $tot; }
                $base++;
                if ($ventaAnterior === null && $conFiltro && (int) $a->description === (int) $año->description - 1) { $ventaAnterior = $tot; }
            }
            if ($base > 0) { break; }
        }
        if ($base === 0) { $participacion = array_fill(1, 12, 1 / 12); $base = 0; }
        else { $suma = array_sum($participacion); foreach ($participacion as $i => $v) { $participacion[$i] = $v / $suma; } }

        // Ritmo real: meses cerrados (antes del mes en curso)
        $real = 0; $shareReal = 0; $mesesReales = 0; $ultimoCerrado = null;
        foreach ($ids as $k => $id) {
            if ($id < $mesActual) { $real += $this->venta[$k]; $shareReal += $participacion[$id]; $mesesReales++; $ultimoCerrado = $k; }
        }
        if ($mesesReales === 0 || $shareReal <= 0) { return; }

        $total = $real / $shareReal;
        foreach ($ids as $k => $id) {
            if ($id >= $mesActual) { $this->pronostico[$k] = round($total * $participacion[$id]); }
        }
        if ($ultimoCerrado !== null) { $this->pronostico[$ultimoCerrado] = $this->venta[$ultimoCerrado]; } // une la línea real con la punteada

        $presupuestoAnual = array_sum($this->presupuesto);
        $this->cierre = [
            'total' => round($total),
            'presupuesto' => round($presupuestoAnual),
            'pct' => $presupuestoAnual > 0 ? round($total / $presupuestoAnual * 100, 1) : null,
            'anterior' => $ventaAnterior !== null ? round($ventaAnterior) : null,
            'variacion' => $ventaAnterior ? round(($total / $ventaAnterior - 1) * 100, 1) : null,
            'base' => $base,
            'mesesReales' => $mesesReales,
            'falta' => round(max(0, $presupuestoAnual - $real)),
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard.tendencia');
    }
}
