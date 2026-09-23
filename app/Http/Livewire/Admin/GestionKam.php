<?php

namespace App\Http\Livewire\Admin;

use App\Models\Año;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Gestión comercial por KAM: mide el trabajo del comercial, no solo la venta.
 *
 * Fuente: gestion_comercial (el embudo) + gestion_participantes (posición 1 = responsable).
 * Etapas: 1 Prospecto · 2 Oportunidad · 3 Cotización · 4 Propuesta · 5 Venta · 6 Perdido · 7 Presupuesto.
 * Se cuenta como GANADA una gestión en Venta (5) o Presupuesto (7), y como ABIERTA las que
 * siguen en 1, 2, 3 o 4. Perdido (6) es la única pérdida explícita.
 */
class GestionKam extends Component
{
    const GANADAS  = [5, 7];
    const PERDIDAS = [6];
    const ABIERTAS = [1, 2, 3, 4];

    public $anio;               // año calendario (created_at de la gestión)
    public $lider = '';         // filtrar por equipo de un líder comercial
    public $orden = 'efectividad'; // efectividad | gestiones | valor | estancadas
    public $diasEstancado = 30;

    /** Con menos gestiones que esto, un porcentaje no dice nada: la fila baja al final del ranking. */
    const MINIMO_SIGNIFICATIVO = 10;

    public $anios = [];
    public $lideres = [];

    public function mount()
    {
        $this->anio = (int) (Año::orderBy('created_at', 'desc')->value('description') ?: now()->year);
        $this->anios = DB::table('gestion_comercial')->selectRaw('DISTINCT YEAR(created_at) a')
            ->whereNotNull('created_at')->orderByDesc('a')->pluck('a')->filter()->values()->all();
        if (!in_array($this->anio, $this->anios) && $this->anios) { $this->anio = $this->anios[0]; }

        $this->lideres = User::whereHas('comerciales')->orderBy('name')->get(['id', 'name'])
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->all();

        // Un líder comercial solo se ve a sí mismo y a su equipo
        if (Auth::user()->esLiderComercial()) { $this->lider = (string) Auth::id(); }
    }

    /** Ids de los comerciales a incluir, o null para todos. */
    private function comercialesDelFiltro()
    {
        if (!$this->lider) { return null; }
        $lider = User::find($this->lider);
        if (!$lider) { return null; }
        $ids = $lider->comerciales()->pluck('users.id')->all();
        $ids[] = (int) $this->lider;   // el líder también gestiona
        return array_values(array_unique($ids));
    }

    /** Una fila por comercial con sus indicadores de gestión del año elegido. */
    private function filas()
    {
        $ganadas = implode(',', self::GANADAS);
        $perdidas = implode(',', self::PERDIDAS);
        $abiertas = implode(',', self::ABIERTAS);

        $q = DB::table('gestion_comercial as g')
            ->join('gestion_participantes as gp', function ($j) {
                $j->on('gp.gestion_id', '=', 'g.id')->where('gp.posicion', 1);
            })
            ->join('users as u', 'u.id', '=', 'gp.user_id')
            ->whereYear('g.created_at', $this->anio)
            ->groupBy('u.id', 'u.name', 'u.avatar')
            ->selectRaw("u.id, u.name, u.avatar,
                COUNT(*) gestiones,
                SUM(g.id_estado IN ($ganadas)) ganadas,
                SUM(g.id_estado IN ($perdidas)) perdidas,
                SUM(g.id_estado IN ($abiertas)) abiertas,
                SUM(CASE WHEN g.id_estado IN ($abiertas) AND DATEDIFF(NOW(), g.updated_at) > ? THEN 1 ELSE 0 END) estancadas,
                SUM(CASE WHEN g.id_estado IN ($ganadas) THEN g.presto_cot ELSE 0 END) valor_ganado,
                SUM(g.presto_cot) valor_gestionado,
                AVG(CASE WHEN g.id_estado IN ($ganadas) THEN DATEDIFF(g.updated_at, g.created_at) END) dias_cierre",
                [$this->diasEstancado]);

        $ids = $this->comercialesDelFiltro();
        if ($ids !== null) { $q->whereIn('gp.user_id', $ids); }

        $filas = $q->get()->map(function ($f) {
            // Efectividad: de todo lo que abrió en el año, cuánto llevó a Venta o Presupuesto.
            // Se usa esta y no ganadas/(ganadas+perdidas) porque el estado Perdido casi no se
            // registra: esa fórmula daría 100 % a todo el mundo y no distinguiría a nadie.
            $f->efectividad = $f->gestiones > 0 ? round($f->ganadas / $f->gestiones * 100, 1) : 0.0;
            $decididas = $f->ganadas + $f->perdidas;
            $f->conversion = $decididas > 0 ? round($f->ganadas / $decididas * 100, 1) : 0.0;
            $f->ticket = $f->ganadas > 0 ? $f->valor_ganado / $f->ganadas : 0;
            $f->dias_cierre = $f->dias_cierre !== null ? round($f->dias_cierre) : null;
            $f->pct_estancadas = $f->abiertas > 0 ? round($f->estancadas / $f->abiertas * 100) : 0;
            $f->poco_volumen = $f->gestiones < self::MINIMO_SIGNIFICATIVO;
            return $f;
        });

        // Se ordena por lo elegido; a igualdad, por volumen de gestiones
        $filas = $filas->sortBy([
            fn ($a, $b) => $this->valorOrden($b) <=> $this->valorOrden($a),
            fn ($a, $b) => $b->gestiones <=> $a->gestiones,
        ])->values();

        return $filas;
    }

    private function valorOrden($f)
    {
        switch ($this->orden) {
            case 'gestiones':  return $f->gestiones;
            case 'valor':      return $f->valor_ganado;
            case 'estancadas': return $f->estancadas;
            // Al ordenar por porcentaje, quien tiene 1 o 2 gestiones quedaría primero con 100 %.
            // Esas filas se mandan al final (efectividad negativa) en vez de ocultarlas.
            default: return $f->gestiones >= self::MINIMO_SIGNIFICATIVO ? $f->efectividad : $f->efectividad - 1000;
        }
    }

    /** Embudo del periodo (todas las gestiones del filtro, por etapa). */
    private function embudo()
    {
        $q = DB::table('gestion_comercial as g')
            ->join('gestion_participantes as gp', function ($j) {
                $j->on('gp.gestion_id', '=', 'g.id')->where('gp.posicion', 1);
            })
            ->leftJoin('estados_gestion_comercial as e', 'e.id', '=', 'g.id_estado')
            ->whereYear('g.created_at', $this->anio)
            ->groupBy('g.id_estado', 'e.description')
            ->selectRaw('g.id_estado, e.description etapa, COUNT(*) n, SUM(g.presto_cot) valor');

        $ids = $this->comercialesDelFiltro();
        if ($ids !== null) { $q->whereIn('gp.user_id', $ids); }

        return $q->get()->keyBy('id_estado');
    }

    public function render()
    {
        $filas = $this->filas();
        $embudo = $this->embudo();

        $tot = [
            'gestiones' => $filas->sum('gestiones'),
            'ganadas' => $filas->sum('ganadas'),
            'perdidas' => $filas->sum('perdidas'),
            'abiertas' => $filas->sum('abiertas'),
            'estancadas' => $filas->sum('estancadas'),
            'valor_ganado' => $filas->sum('valor_ganado'),
            'kams' => $filas->count(),
        ];
        $tot['efectividad'] = $tot['gestiones'] > 0 ? round($tot['ganadas'] / $tot['gestiones'] * 100, 1) : 0;
        $decididas = $tot['ganadas'] + $tot['perdidas'];
        $tot['conversion'] = $decididas > 0 ? round($tot['ganadas'] / $decididas * 100, 1) : 0;
        $tot['ticket'] = $tot['ganadas'] > 0 ? $tot['valor_ganado'] / $tot['ganadas'] : 0;
        $diasKam = $filas->pluck('dias_cierre')->filter(fn ($d) => $d !== null);
        $tot['dias_cierre'] = $diasKam->count() ? round($diasKam->avg()) : null;

        return view('livewire.admin.gestion-kam', [
            'filas' => $filas,
            'embudo' => $embudo,
            'tot' => $tot,
            'maxGestiones' => max(1, $filas->max('gestiones') ?: 1),
            'minimo' => self::MINIMO_SIGNIFICATIVO,
        ]);
    }
}
