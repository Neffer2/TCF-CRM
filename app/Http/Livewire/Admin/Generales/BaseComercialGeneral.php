<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Año;
use App\Models\Mes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;

/**
 * Base comercial general: todos los registros de la base comercial con
 * filtros (año, mes, estado, comercial, texto), resumen del filtro aplicado,
 * distribución por estado, orden por columna, paginación y exportación.
 *
 * Los filtros pueden llegar por URL (desde el dashboard): año como id o como
 * descripción ("2026"), mes y comercial como id, estado como id.
 */
class BaseComercialGeneral extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filtros
    public $año;
    public $mes;
    public $estado;
    public $comercial;
    public $centro = '';           // texto: centro de costos, cliente o proyecto
    public $buscarComercial = '';  // texto del buscador de comercial
    public $orden = 'fecha';
    public $dir = 'desc';
    public $porPagina = 25;

    // Catálogos
    public $años = [];        // [{id, description}]
    public $meses = [];       // [{id, description, f_inicio, f_fin}] del año elegido
    public $estados = [];     // [{id, description}]
    public $comerciales = []; // [{id, name}] usuarios con registros en la base

    // Resumen del filtro aplicado (sin contar el filtro de estado, para las fichas)
    public $resumen = ['valor' => 0, 'registros' => 0, 'clientes' => 0, 'comerciales' => 0];
    public $porEstado = [];   // [id => ['n' => .., 'valor' => ..]]

    public $requested_filters;

    protected $columnasOrdenables = [
        'fecha', 'nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_proyecto', 'id_estado', 'fecha_inicio', 'dura_mes',
    ];

    public function mount()
    {
        $this->años = Año::select('id', 'description')->orderBy('description', 'desc')->get()
            ->map(function ($a) { return ['id' => $a->id, 'description' => $a->description]; })->all();
        $this->estados = EstadoCuenta::select('id', 'description')->orderBy('id')->get()
            ->map(function ($e) { return ['id' => $e->id, 'description' => $e->description]; })->all();
        $this->comerciales = User::select('id', 'name')
            ->whereIn('id', Base_comercial::select('id_user')->distinct())
            ->orderBy('name')->get()
            ->map(function ($u) { return ['id' => $u->id, 'name' => $u->name]; })->all();

        $this->aplicarFiltrosSolicitados();
        if (!$this->año && !empty($this->años)) {
            $this->año = $this->años[0]['id'];
        }
        $this->cargarMeses();
    }

    /** Filtros que llegan por URL (el dashboard manda el año como descripción). */
    private function aplicarFiltrosSolicitados()
    {
        $f = $this->requested_filters ?: [];
        if (!empty($f['año'])) {
            $a = collect($this->años)->first(function ($x) use ($f) {
                return (string) $x['id'] === (string) $f['año'] || (string) $x['description'] === (string) $f['año'];
            });
            $this->año = $a ? $a['id'] : null;
        }
        $this->mes = !empty($f['mes']) ? (int) $f['mes'] : null;
        $this->estado = !empty($f['estado']) ? (int) $f['estado'] : null;
        $this->comercial = !empty($f['comercial']) ? (int) $f['comercial'] : null;
        if ($this->comercial) {
            $c = collect($this->comerciales)->firstWhere('id', $this->comercial);
            $this->buscarComercial = $c ? $c['name'] : '';
        }
    }

    private function cargarMeses()
    {
        $this->meses = $this->año
            ? Mes::select('id', 'description', 'f_inicio', 'f_fin')->where('ano_id', $this->año)
                ->orderByRaw('CAST(identifier AS UNSIGNED)')->get()->map(function ($m) {
                    return ['id' => $m->id, 'description' => $m->description, 'f_inicio' => $m->f_inicio, 'f_fin' => $m->f_fin];
                })->all()
            : [];
        if ($this->mes && !collect($this->meses)->firstWhere('id', (int) $this->mes)) {
            $this->mes = null;
        }
    }

    // --- Cambios de filtro: recargar dependientes y volver a la página 1 ---
    public function updatedAño()
    {
        $this->año = $this->año ?: null;
        $this->cargarMeses();
        $this->resetPage();
    }
    public function updatedMes() { $this->mes = $this->mes ?: null; $this->resetPage(); }
    public function updatedEstado() { $this->estado = $this->estado ?: null; $this->resetPage(); }
    public function updatedCentro() { $this->resetPage(); }
    public function updatedPorPagina() { $this->resetPage(); }

    /** Ficha de estado: pulsar la activa la quita. */
    public function filtrarEstado($id)
    {
        $this->estado = ($this->estado == $id) ? null : (int) $id;
        $this->resetPage();
    }

    public function ordenar($columna)
    {
        if (!in_array($columna, $this->columnasOrdenables, true)) { return; }
        if ($this->orden === $columna) {
            $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orden = $columna;
            $this->dir = in_array($columna, ['fecha', 'valor_proyecto', 'fecha_inicio', 'dura_mes'], true) ? 'desc' : 'asc';
        }
        $this->resetPage();
    }

    public function limpiar()
    {
        $this->mes = null;
        $this->estado = null;
        $this->comercial = null;
        $this->centro = '';
        $this->buscarComercial = '';
        $this->año = !empty($this->años) ? $this->años[0]['id'] : null;
        $this->cargarMeses();
        $this->resetPage();
    }

    // --- Buscador de comercial (lista desplegable en la que se escribe) ---
    public function getComercialesFiltradosProperty()
    {
        $texto = mb_strtolower(trim($this->buscarComercial));
        return collect($this->comerciales)->filter(function ($c) use ($texto) {
            return $texto === '' || mb_strpos(mb_strtolower($c['name']), $texto) !== false;
        })->values();
    }

    public function elegirComercial($id = null)
    {
        $this->comercial = $id ? (int) $id : null;
        $c = collect($this->comerciales)->firstWhere('id', (int) $id);
        $this->buscarComercial = $c ? $c['name'] : '';
        $this->resetPage();
    }

    public function limpiarComercial() { $this->elegirComercial(null); }

    /** Consulta base con todos los filtros; $conEstado=false omite el de estado (para las fichas). */
    private function consulta($conEstado = true)
    {
        $q = Base_comercial::query();

        if ($this->año && !empty($this->meses)) {
            $rango = $this->mes ? collect($this->meses)->firstWhere('id', (int) $this->mes) : null;
            $desde = $rango ? $rango['f_inicio'] : $this->meses[0]['f_inicio'];
            $hasta = $rango ? $rango['f_fin'] : $this->meses[count($this->meses) - 1]['f_fin'];
            $q->whereBetween('fecha', [$desde, $hasta]);
        }
        if ($this->comercial) {
            $q->where('id_user', $this->comercial);
        }
        if (trim($this->centro) !== '') {
            $t = '%'.trim($this->centro).'%';
            $q->where(function ($w) use ($t) {
                $w->where('cod_cc', 'LIKE', $t)->orWhere('nom_cliente', 'LIKE', $t)->orWhere('nom_proyecto', 'LIKE', $t);
            });
        }
        if ($conEstado && $this->estado) {
            $q->where('id_estado', $this->estado);
        }
        return $q;
    }

    /** Filtros en el formato [[col, op, val]] que espera BaseExport. */
    private function filtrosParaExport()
    {
        $filtros = [];
        if ($this->año && !empty($this->meses)) {
            $rango = $this->mes ? collect($this->meses)->firstWhere('id', (int) $this->mes) : null;
            $filtros[] = ['fecha', '>=', $rango ? $rango['f_inicio'] : $this->meses[0]['f_inicio']];
            $filtros[] = ['fecha', '<=', $rango ? $rango['f_fin'] : $this->meses[count($this->meses) - 1]['f_fin']];
        }
        if ($this->comercial) { $filtros[] = ['id_user', $this->comercial]; }
        if (trim($this->centro) !== '') { $filtros[] = ['cod_cc', 'LIKE', '%'.trim($this->centro).'%']; }
        if ($this->estado) { $filtros[] = ['id_estado', $this->estado]; }
        return $filtros;
    }

    public function exportar()
    {
        return Excel::download(new BaseExport(['filtros' => $this->filtrosParaExport()]), 'Reporte Base Comercial.xlsx');
    }

    /** Texto del alcance actual para el encabezado ("2026 · Marzo · Lady Ortiz"). */
    public function alcance()
    {
        $partes = [];
        $a = collect($this->años)->firstWhere('id', (int) $this->año);
        if ($a) { $partes[] = $a['description']; }
        $m = $this->mes ? collect($this->meses)->firstWhere('id', (int) $this->mes) : null;
        if ($m) { $partes[] = $m['description']; }
        $c = $this->comercial ? collect($this->comerciales)->firstWhere('id', (int) $this->comercial) : null;
        if ($c) { $partes[] = $c['name']; }
        return implode(' · ', $partes) ?: 'Todo el histórico';
    }

    public static function etiquetaEstado($descripcion)
    {
        $mapa = [
            'EJECUCIONXFACTURAR' => 'Ejecución por facturar',
            'VENTAEJECUCION' => 'Venta en ejecución',
            'FACTURACION PARCIAL' => 'Facturación parcial',
            'COTIZACION' => 'Cotización',
        ];
        return $mapa[$descripcion] ?? mb_convert_case(mb_strtolower($descripcion), MB_CASE_TITLE);
    }

    public static function tonoEstado($id)
    {
        switch ((int) $id) {
            case 1: case 9: return 'tone-ok';        // Cerrado, Facturado
            case 3: case 10: return 'tone-warn';     // Ejecución por facturar, Facturación parcial
            case 4: return 'tone-bad';               // Perdido
            case 6: case 7: return 'tone-neutral';   // Venta, Venta en ejecución
            default: return 'tone-muted';            // Cotización, Propuesta, Interno
        }
    }

    public function render()
    {
        // Resumen del filtro (sin estado) y distribución por estado para las fichas
        $base = $this->consulta(false);
        $agg = (clone $base)->select(DB::raw('COUNT(*) AS n, COALESCE(SUM(valor_proyecto),0) AS valor, COUNT(DISTINCT nom_cliente) AS clientes, COUNT(DISTINCT id_user) AS comerciales'))->first();
        $this->porEstado = (clone $base)->select('id_estado', DB::raw('COUNT(*) AS n'), DB::raw('COALESCE(SUM(valor_proyecto),0) AS valor'))
            ->groupBy('id_estado')->get()->keyBy('id_estado')
            ->map(function ($r) { return ['n' => (int) $r->n, 'valor' => (float) $r->valor]; })->all();

        // Con estado: lo que realmente se lista
        $filtrada = $this->consulta(true);
        $valorFiltrado = (float) (clone $filtrada)->sum('valor_proyecto');
        $this->resumen = [
            'valor' => $this->estado ? $valorFiltrado : (float) $agg->valor,
            'registros' => (int) $agg->n,
            'clientes' => (int) $agg->clientes,
            'comerciales' => (int) $agg->comerciales,
        ];

        $registros = $filtrada->with(['comercial:id,name,avatar', 'estado_cuenta:id,description'])
            ->orderBy($this->orden, $this->dir)->orderBy('id', 'desc')
            ->paginate((int) $this->porPagina ?: 25);

        return view('livewire.admin.generales.base-comercial-general', [
            'registros' => $registros,
            'valorFiltrado' => $valorFiltrado,
            'alcance' => $this->alcance(),
        ]);
    }
}
