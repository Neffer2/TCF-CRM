<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Helisa;
use App\Models\Año;
use App\Models\Mes;
use App\Models\Cuenta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HelisaExport;

/**
 * Helisa general: movimientos de facturación importados del sistema contable
 * (Helisa), que son la fuente de la "venta facturada" de todos los dashboards.
 * Filtros (año, mes, tipo de documento, cuenta, comercial, texto), resumen,
 * distribución por tipo de documento, orden por columna, paginación y
 * exportación con exactamente lo filtrado.
 */
class HelisaGeneral extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filtros
    public $año;
    public $mes;
    public $tipo;
    public $cuenta;
    public $comercial;
    public $buscar = '';           // centro, nombre del centro, tercero, concepto o n.º de documento
    public $buscarComercial = '';
    public $orden = 'fecha';
    public $dir = 'desc';
    public $porPagina = 25;

    // Catálogos
    public $años = [];
    public $meses = [];
    public $tipos = [];        // [{tipo, n}]
    public $cuentas = [];
    public $comerciales = [];

    public $resumen = ['base' => 0, 'movimientos' => 0, 'terceros' => 0, 'comision' => 0];
    public $porTipo = [];      // [tipo => ['n' => .., 'base' => ..]]
    public $equipo = null;     // rol Líder comercial: ids de su equipo

    protected $columnasOrdenables = ['fecha', 'tipo_doc', 'nom_tercero', 'centro', 'debito', 'credito', 'base_factura', 'comision'];

    public function mount()
    {
        $this->años = Año::select('id', 'description')->orderBy('description', 'desc')->get()
            ->map(function ($a) { return ['id' => $a->id, 'description' => $a->description]; })->all();
        $this->cuentas = Cuenta::select('id', 'description')->get()
            ->map(function ($c) { return ['id' => $c->id, 'description' => $c->description]; })->all();
        $this->tipos = Helisa::select('tipo_doc')->whereNotNull('tipo_doc')->where('tipo_doc', '!=', '')
            ->groupBy('tipo_doc')->orderBy('tipo_doc')->pluck('tipo_doc')->all();

        if (auth()->user()->esLiderComercial()) {
            $this->equipo = auth()->user()->comercialesAsignados()->pluck('users.id')->map('intval')->all() ?: [0];
        }
        $this->comerciales = User::select('id', 'name')
            ->whereIn('id', Helisa::select('comercial')->whereNotNull('comercial')->distinct())
            ->when(is_array($this->equipo), function ($q) { $q->whereIn('id', $this->equipo); })
            ->orderBy('name')->get()
            ->map(function ($u) { return ['id' => $u->id, 'name' => $u->name]; })->all();

        $this->año = !empty($this->años) ? $this->años[0]['id'] : null;
        $this->cargarMeses();
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

    public function updatedAño() { $this->año = $this->año ?: null; $this->cargarMeses(); $this->resetPage(); }
    public function updatedMes() { $this->mes = $this->mes ?: null; $this->resetPage(); }
    public function updatedTipo() { $this->tipo = $this->tipo ?: null; $this->resetPage(); }
    public function updatedCuenta() { $this->cuenta = $this->cuenta ?: null; $this->resetPage(); }
    public function updatedBuscar() { $this->resetPage(); }
    public function updatedPorPagina() { $this->resetPage(); }

    public function filtrarTipo($tipo)
    {
        $this->tipo = ($this->tipo === $tipo) ? null : $tipo;
        $this->resetPage();
    }

    public function ordenar($columna)
    {
        if (!in_array($columna, $this->columnasOrdenables, true)) { return; }
        if ($this->orden === $columna) {
            $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orden = $columna;
            $this->dir = in_array($columna, ['fecha', 'debito', 'credito', 'base_factura', 'comision'], true) ? 'desc' : 'asc';
        }
        $this->resetPage();
    }

    public function limpiar()
    {
        $this->mes = null; $this->tipo = null; $this->cuenta = null; $this->comercial = null;
        $this->buscar = ''; $this->buscarComercial = '';
        $this->año = !empty($this->años) ? $this->años[0]['id'] : null;
        $this->cargarMeses();
        $this->resetPage();
    }

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

    /** Consulta con todos los filtros; $conTipo=false omite el tipo de documento (para las fichas). */
    private function consulta($conTipo = true)
    {
        $q = Helisa::query();
        if (is_array($this->equipo)) { $q->whereIn('comercial', $this->equipo); }
        if ($this->año && !empty($this->meses)) {
            $rango = $this->mes ? collect($this->meses)->firstWhere('id', (int) $this->mes) : null;
            $desde = $rango ? $rango['f_inicio'] : $this->meses[0]['f_inicio'];
            $hasta = $rango ? $rango['f_fin'] : $this->meses[count($this->meses) - 1]['f_fin'];
            $q->whereBetween('fecha', [$desde, $hasta]);
        }
        if ($this->cuenta) { $q->where('id_cuenta', $this->cuenta); }
        if ($this->comercial) { $q->where('comercial', $this->comercial); }
        if (trim($this->buscar) !== '') {
            $t = '%'.trim($this->buscar).'%';
            $q->where(function ($w) use ($t) {
                $w->where('centro', 'LIKE', $t)->orWhere('nom_centro_costo', 'LIKE', $t)
                  ->orWhere('nom_tercero', 'LIKE', $t)->orWhere('concepto', 'LIKE', $t)->orWhere('num_doc', 'LIKE', $t);
            });
        }
        if ($conTipo && $this->tipo) { $q->where('tipo_doc', $this->tipo); }
        return $q;
    }

    /** Exporta exactamente lo que se está viendo (antes el Excel ignoraba el año y el mes). */
    public function exportar()
    {
        $registros = $this->consulta(true)->with('comercial_user')->orderBy($this->orden, $this->dir)->get();
        $nombre = 'Reporte Helisa'.($this->comercial ? ' - '.collect($this->comerciales)->firstWhere('id', (int) $this->comercial)['name'] : '').'.xlsx';
        return Excel::download(new HelisaExport(['registros_helisa' => $registros]), $nombre);
    }

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

    /** Etiqueta legible de los tipos de documento de Helisa. */
    public static function etiquetaTipo($tipo)
    {
        $mapa = [
            'FEVT' => 'Factura electrónica de venta', 'FVET' => 'Factura de venta electrónica', 'FCVT' => 'Factura de venta',
            'FVT' => 'Factura de venta', 'NC' => 'Nota crédito', 'NCEO' => 'Nota crédito electrónica', 'ND' => 'Nota débito',
        ];
        return $mapa[$tipo] ?? $tipo;
    }

    public static function tonoTipo($tipo)
    {
        if (strpos($tipo, 'NC') === 0) { return 'tone-bad'; }   // notas crédito restan
        if (strpos($tipo, 'ND') === 0) { return 'tone-warn'; }
        return 'tone-ok';                                         // facturas suman
    }

    public function render()
    {
        $base = $this->consulta(false);
        $agg = (clone $base)->select(DB::raw('COUNT(*) AS n, COALESCE(SUM(base_factura),0) AS base, COUNT(DISTINCT nom_tercero) AS terceros, COALESCE(SUM(comision),0) AS comision'))->first();
        $this->porTipo = (clone $base)->select('tipo_doc', DB::raw('COUNT(*) AS n'), DB::raw('COALESCE(SUM(base_factura),0) AS base'))
            ->groupBy('tipo_doc')->orderByDesc('n')->get()->keyBy('tipo_doc')
            ->map(function ($r) { return ['n' => (int) $r->n, 'base' => (float) $r->base]; })->all();

        $filtrada = $this->consulta(true);
        $aggF = $this->tipo ? (clone $filtrada)->select(DB::raw('COALESCE(SUM(base_factura),0) AS base, COALESCE(SUM(comision),0) AS comision'))->first() : $agg;
        $this->resumen = [
            'base' => (float) $aggF->base,
            'movimientos' => (int) $agg->n,
            'terceros' => (int) $agg->terceros,
            'comision' => (float) $aggF->comision,
        ];

        $registros = $filtrada->with('comercial_user:id,name')
            ->orderBy($this->orden, $this->dir)->orderBy('id', 'desc')
            ->paginate((int) $this->porPagina ?: 25);

        return view('livewire.admin.generales.helisa-general', [
            'registros' => $registros,
            'baseFiltrada' => (float) $aggF->base,
            'alcance' => $this->alcance(),
        ]);
    }
}
