<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Año;
use App\Models\Mes;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Filtros del dashboard de gerencia: año, mes, líder comercial y comercial.
 *
 * El líder comercial sale de la tabla lider_comercial_user (líder -> comerciales
 * a su cargo). Al elegir un líder, los indicadores se calculan con la venta y
 * el presupuesto de todo su equipo y el buscador de comercial se reduce a ese
 * equipo; al elegir además un comercial, se ve solo ese comercial.
 *
 * A los demás componentes se les envía, además de los ids elegidos, la lista
 * resuelta `comerciales` (ids) con la que deben filtrar: [comercial], los ids
 * del equipo del líder, o null (todos).
 */
class Filters extends Component
{
    public $año;        // ID del año seleccionado
    public $mes;        // ID del mes seleccionado
    public $lider;      // ID del líder comercial seleccionado
    public $comercial;  // ID del comercial seleccionado
    public $buscarComercial = ''; // Texto del buscador de comercial

    public $StdAño = [];
    public $StdMes = [];
    public $StdComercial = [];  // [{id, name}] todos los comerciales (rol 2)
    public $StdLider = [];      // [{id, name, equipo:[ids]}] líderes con su equipo
    public $liderFijo = false;  // rol Líder comercial: el filtro de líder queda fijo en su propia cuenta

    public function render()
    {
        return view('livewire.admin.dashboard.filters');
    }

    public function mount()
    {
        $this->StdAño = Año::select('id', 'description')->get();

        // La lista de comerciales no depende del año: siempre disponible para el buscador
        $this->StdComercial = User::select('id', 'name')->where('rol', 2)->orderBy('name')->get()
            ->map(function ($u) { return ['id' => $u->id, 'name' => $u->name]; })->all();

        // Líderes comerciales = usuarios que aparecen como lider_id en lider_comercial_user
        $equipos = DB::table('lider_comercial_user')->select('lider_id', 'comercial_id')->get()->groupBy('lider_id');
        $this->StdLider = User::select('id', 'name')->whereIn('id', $equipos->keys())->orderBy('name')->get()
            ->map(function ($u) use ($equipos) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'equipo' => $equipos[$u->id]->pluck('comercial_id')->unique()->values()->all(),
                ];
            })->all();

        // Líder comercial (rol 12): solo ve su equipo; el filtro de líder no se puede cambiar
        if (auth()->user()->esLiderComercial()) {
            $this->lider = auth()->id();
            $this->liderFijo = true;
            if (!collect($this->StdLider)->firstWhere('id', $this->lider)) {
                $this->StdLider[] = ['id' => $this->lider, 'name' => auth()->user()->name, 'equipo' => []];
            }
        }

        $this->getFilters();

        if ($this->liderFijo) {
            $this->asegurarAño();
            $this->signals();
        }
    }

    public function updatedAño()
    {
        $this->getFilters();
        $this->signals();
    }

    public function updatedMes()
    {
        $this->signals();
    }

    /** Al cambiar el líder: si el comercial elegido no es de su equipo, se quita. */
    public function updatedLider()
    {
        if ($this->liderFijo) { $this->lider = auth()->id(); }
        $this->lider = $this->lider ?: null;
        if ($this->comercial && !in_array($this->comercial, $this->equipoActual(), false)) {
            $this->comercial = null;
            $this->buscarComercial = '';
        }
        $this->asegurarAño();
        $this->signals();
    }

    /** Ids del equipo del líder elegido; vacío si no hay líder. */
    public function equipoActual()
    {
        if (!$this->lider) { return []; }
        $l = collect($this->StdLider)->firstWhere('id', (int) $this->lider);
        return $l ? $l['equipo'] : [];
    }

    /** Lista resuelta de comerciales con la que filtran los indicadores (null = todos). */
    public function comercialesActivos()
    {
        if ($this->comercial) { return [(int) $this->comercial]; }
        if ($this->lider) { return array_map('intval', $this->equipoActual()) ?: [0]; }
        return null;
    }

    /** Nombre corto del alcance actual, para títulos ("Equipo de Lady Ortiz"). */
    public function alcance()
    {
        if ($this->comercial) {
            $c = collect($this->StdComercial)->firstWhere('id', (int) $this->comercial);
            return $c ? $c['name'] : '';
        }
        if ($this->lider) {
            $l = collect($this->StdLider)->firstWhere('id', (int) $this->lider);
            return $l ? 'Equipo de '.$l['name'] : '';
        }
        return '';
    }

    // --- Buscador de comercial: filtra la lista mientras se escribe (y por equipo del líder) ---
    public function getComercialesFiltradosProperty()
    {
        $texto = mb_strtolower(trim($this->buscarComercial));
        $equipo = $this->lider ? $this->equipoActual() : null;
        return collect($this->StdComercial)->filter(function ($c) use ($texto, $equipo) {
            if ($equipo !== null && !in_array($c['id'], $equipo, false)) { return false; }
            return $texto === '' || mb_strpos(mb_strtolower($c['name']), $texto) !== false;
        })->values();
    }

    public function elegirComercial($id = null)
    {
        $this->comercial = $id ?: null;
        $this->asegurarAño();
        $nombre = collect($this->StdComercial)->firstWhere('id', (int) $id);
        $this->buscarComercial = $nombre ? $nombre['name'] : '';
        $this->signals();
    }

    public function limpiarComercial()
    {
        $this->elegirComercial(null);
    }

    /** Sin año elegido, los filtros de líder/comercial aplican sobre el año más reciente. */
    private function asegurarAño()
    {
        if (!$this->año) {
            $ultimo = Año::orderBy('created_at', 'desc')->first();
            if ($ultimo) { $this->año = $ultimo->id; $this->getFilters(); }
        }
    }

    public function signals()
    {
        if (!$this->año) { return; }

        $año_desc = Año::select('description')->where('id', $this->año)->first();
        $comerciales = $this->comercialesActivos();
        $alcance = $this->alcance();

        $this->emit('Block1', [
            'año' => $año_desc->description,
            'mes' => $this->mes,
            'comercial' => $this->comercial,
            'comerciales' => $comerciales,
        ]);
        $this->emit('Block2', [
            'año' => $año_desc->description,
            'mes' => $this->mes,
            'comercial' => $this->comercial,
            'comerciales' => $comerciales,
        ]);
        $this->emit('Tendencia', [
            'año_id' => $this->año,
            'comercial' => $this->comercial,
            'comerciales' => $comerciales,
            'alcance' => $alcance,
        ]);
        $this->emit('Ranking', [
            'año_id' => $this->año,
            'mes' => $this->mes,
            'comercial' => $this->comercial,
            'lider' => $this->lider,
            'comerciales' => $comerciales,
            'alcance' => $alcance,
        ]);
    }

    public function getFilters()
    {
        if ($this->año) {
            $this->StdMes = Mes::select('id', 'description')
                ->where('ano_id', $this->año)
                ->orderByRaw('CAST(identifier AS UNSIGNED)')
                ->get();
        } else {
            $this->StdMes = [];
            $this->emit('Block1');
            $this->emit('Block2');
            $this->emit('Tendencia');
            $this->emit('Ranking');
        }
    }
}
