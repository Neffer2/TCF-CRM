<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Panel a pantalla completa con TODOS los proyectos de un comercial en el año:
 * agrupados por etapa del embudo, con cliente, valor cotizado, lo realmente
 * facturado (Helisa, cruzando por centro de costos), cuándo se abrió y cuánto
 * lleva sin moverse. Se abre con la señal 'abrirKam' desde la tabla de KAM.
 */
class DetalleKam extends Component
{
    const ETAPAS = [5 => 'Venta', 7 => 'Presupuesto', 4 => 'Propuesta', 2 => 'Oportunidad', 1 => 'Prospecto', 3 => 'Cotización', 6 => 'Perdido'];
    const TONOS  = [5 => 'tone-ok', 7 => 'tone-warn', 4 => 'tone-warn', 2 => 'tone-neutral', 1 => 'tone-muted', 3 => 'tone-neutral', 6 => 'tone-bad'];
    const GANADAS = [5, 7];

    public $abierto = false;
    public $kamId = null;
    public $kam = '';
    public $anio;
    public $diasEstancado = 30;
    public $buscar = '';
    public $etapaAbierta = null;   // filtra a una sola etapa al pulsar su ficha

    public $resumen = [];
    // OJO: los grupos NO son propiedad pública. Livewire aplica las propiedades públicas
    // DESPUÉS de los datos que se pasan a la vista, así que una propiedad con el mismo
    // nombre pisaría lo calculado en render() y el panel saldría vacío.

    protected $listeners = ['abrirKam' => 'abrir'];

    public function abrir($kamId, $anio, $diasEstancado = 30)
    {
        $this->kamId = (int) $kamId;
        $this->anio = (int) $anio;
        $this->diasEstancado = (int) $diasEstancado;
        $this->kam = optional(User::find($this->kamId))->name ?: 'Comercial';
        $this->buscar = '';
        $this->etapaAbierta = null;
        $this->abierto = true;
        $this->dispatchBrowserEvent('crm-modal', ['abierto' => true]);
    }

    public function cerrar()
    {
        $this->abierto = false;
        $this->dispatchBrowserEvent('crm-modal', ['abierto' => false]);
    }

    public function verEtapa($id)
    {
        $this->etapaAbierta = $this->etapaAbierta === (int) $id ? null : (int) $id;
    }

    /** Proyectos del comercial en el año, con lo facturado real por centro de costos. */
    private function proyectos()
    {
        if (!$this->kamId) { return collect(); }

        $q = DB::table('gestion_comercial as g')
            ->join('gestion_participantes as gp', function ($j) {
                $j->on('gp.gestion_id', '=', 'g.id')->where('gp.posicion', 1);
            })
            ->leftJoin('contactos as c', 'c.id', '=', 'g.id_contacto')
            ->leftJoin('presupuesto_proyecto as pp', 'pp.id_gestion', '=', 'g.id')
            ->where('gp.user_id', $this->kamId)
            ->whereYear('g.created_at', $this->anio)
            ->selectRaw("g.id, g.nom_proyecto_cot proyecto, g.id_estado, g.presto_cot valor,
                g.created_at abierta, g.updated_at movida, g.causa,
                DATEDIFF(NOW(), g.updated_at) dias_quieta,
                DATEDIFF(g.updated_at, g.created_at) dias_abierta,
                c.empresa cliente, c.nombre contacto,
                pp.id presupuesto_id, pp.cod_cc,
                TRIM(SUBSTRING_INDEX(pp.cod_cc, ' -', 1)) centro");

        if (trim($this->buscar) !== '') {
            $t = '%'.trim($this->buscar).'%';
            $q->where(function ($w) use ($t) {
                $w->where('g.nom_proyecto_cot', 'LIKE', $t)->orWhere('c.empresa', 'LIKE', $t)
                  ->orWhere('c.nombre', 'LIKE', $t)->orWhere('pp.cod_cc', 'LIKE', $t);
            });
        }
        if ($this->etapaAbierta) { $q->where('g.id_estado', $this->etapaAbierta); }

        $filas = $q->orderByDesc('g.created_at')->get();

        // Facturación real: se cruza por centro de costos en una sola consulta
        $centros = $filas->pluck('centro')->filter()->unique()->values();
        $facturado = $centros->isEmpty() ? collect() : DB::table('helisa')
            ->selectRaw('TRIM(centro) c, SUM(base_factura) total')
            ->whereIn(DB::raw('TRIM(centro)'), $centros->all())
            ->groupBy('c')->pluck('total', 'c');

        return $filas->map(function ($f) use ($facturado) {
            $f->facturado = $f->centro ? (float) ($facturado[$f->centro] ?? 0) : 0.0;
            $f->quieta = in_array($f->id_estado, self::GANADAS) ? false : $f->dias_quieta > $this->diasEstancado;
            $f->cumplimiento = $f->valor > 0 ? round($f->facturado / $f->valor * 100) : null;
            return $f;
        });
    }

    public function render()
    {
        $proyectos = $this->abierto ? $this->proyectos() : collect();

        $grupos = [];
        foreach (self::ETAPAS as $id => $nombre) {
            $p = $proyectos->where('id_estado', $id)->values();
            if ($p->isEmpty()) { continue; }
            $grupos[] = [
                'id' => $id, 'etapa' => $nombre, 'tono' => self::TONOS[$id],
                'n' => $p->count(), 'valor' => $p->sum('valor'), 'facturado' => $p->sum('facturado'),
                'quietas' => $p->where('quieta', true)->count(),
                'proyectos' => $p,
            ];
        }

        $ganados = $proyectos->whereIn('id_estado', self::GANADAS);
        $this->resumen = [
            'n' => $proyectos->count(),
            'valor' => $proyectos->sum('valor'),
            'ganados' => $ganados->count(),
            'valor_ganado' => $ganados->sum('valor'),
            'facturado' => $proyectos->sum('facturado'),
            'quietas' => $proyectos->where('quieta', true)->count(),
            'sin_cc' => $proyectos->whereIn('id_estado', self::GANADAS)->whereNull('cod_cc')->count(),
        ];

        return view('livewire.admin.detalle-kam', ['grupos' => $grupos]);
    }
}
