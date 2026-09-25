<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use App\Services\GestionKamDatos as D;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Gestión comercial por KAM: cómo trabaja cada comercial su embudo y cuánto de lo que
 * vende ya se facturó. La clasificación de etapas y el cruce con Helisa viven en
 * App\Services\GestionKamDatos para que la página y el panel del comercial coincidan.
 */
class GestionKam extends Component
{
    /** Con menos gestiones que esto, un porcentaje no dice nada: la fila baja al final. */
    const MINIMO_SIGNIFICATIVO = 10;

    public $anio;
    public $lider = '';
    public $diasQuieta = 30;
    public $orden = 'vendido';
    public $dir = 'desc';

    public $anios = [];
    public $lideres = [];

    public function mount()
    {
        $this->anios = DB::table('gestion_comercial')->selectRaw('DISTINCT YEAR(created_at) a')
            ->whereNotNull('created_at')->orderByDesc('a')->pluck('a')->filter()->values()->all();
        $this->anio = $this->anios[0] ?? (int) now()->year;

        $this->lideres = User::whereHas('comerciales')->orderBy('name')->get(['id', 'name'])
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->all();

        // Un líder comercial ve solo a su equipo
        if (Auth::user()->esLiderComercial()) { $this->lider = (string) Auth::id(); }
    }

    public function ordenarPor($campo)
    {
        if ($this->orden === $campo) { $this->dir = $this->dir === 'desc' ? 'asc' : 'desc'; }
        else { $this->orden = $campo; $this->dir = $campo === 'nombre' ? 'asc' : 'desc'; }
    }

    private function comercialesDelFiltro(): ?array
    {
        // El líder comercial siempre ve su equipo: el selector deshabilitado no impide cambiar $lider desde el navegador
        if (Auth::user()->esLiderComercial()) { $this->lider = (string) Auth::id(); }
        if (!$this->lider) { return null; }
        $lider = User::find($this->lider);
        if (!$lider) { return null; }
        $ids = $lider->comerciales()->pluck('users.id')->all();
        $ids[] = (int) $this->lider;
        return array_values(array_unique($ids));
    }

    public function render()
    {
        $gestiones = D::gestiones((int) $this->anio, $this->comercialesDelFiltro());
        $total = D::resumen($gestiones, (int) $this->diasQuieta);

        // Embudo en el orden real; "Cotización" (3) solo aparece si tiene datos
        $embudo = [];
        foreach (D::ETAPAS as $id => $e) {
            $grupo = $gestiones->where('id_estado', $id);
            if ($id === 3 && $grupo->isEmpty()) { continue; }
            $embudo[] = $e + ['id' => $id, 'n' => $grupo->count(), 'valor' => $grupo->sum('valor'), 'grupo' => D::grupo($id)];
        }

        // Una fila por comercial
        $nombres = User::whereIn('id', $gestiones->pluck('user_id')->unique())->pluck('name', 'id');
        $lideresDe = D::lideres();

        $filas = $gestiones->groupBy('user_id')->map(function ($g, $uid) use ($nombres, $lideresDe) {
            $r = D::resumen($g, (int) $this->diasQuieta);
            return (object) ($r + [
                'reparto' => D::reparto($r),
                'id' => (int) $uid,
                'nombre' => $nombres[$uid] ?? 'Sin nombre',
                'lider' => $lideresDe[$uid] ?? null,
                'poco_volumen' => $r['gestiones'] < self::MINIMO_SIGNIFICATIVO,
            ]);
        })->values();

        $filas = $this->ordenar($filas);

        return view('livewire.admin.gestion-kam', [
            'total' => $total,
            'embudo' => $embudo,
            'filas' => $filas,
            'maxEmbudo' => max(1, collect($embudo)->max('n')),
            'minimo' => self::MINIMO_SIGNIFICATIVO,
        ]);
    }

    private function ordenar($filas)
    {
        $campo = $this->orden;
        $asc = $this->dir === 'asc';
        return $filas->sort(function ($a, $b) use ($campo, $asc) {
            if ($campo === 'nombre') { $c = strcmp($a->nombre, $b->nombre); return $asc ? $c : -$c; }
            // En porcentajes, quien tiene muy pocas gestiones va al final sin importar el sentido
            if (in_array($campo, ['conversion', 'pct_facturado']) && $a->poco_volumen !== $b->poco_volumen) {
                return $a->poco_volumen ? 1 : -1;
            }
            $c = ($a->$campo <=> $b->$campo) ?: ($a->gestiones <=> $b->gestiones);
            return $asc ? $c : -$c;
        })->values();
    }
}
