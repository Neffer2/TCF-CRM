<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use App\Services\GestionKamDatos as D;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Panel a pantalla completa con todos los proyectos de un comercial en el año.
 * En vez de grupos plegables, se organiza por preguntas (pestañas): qué falta
 * facturar, qué está quieto, qué sigue en curso, qué se vendió y qué se perdió.
 * Se abre con la señal 'abrirKam' desde la tabla de la página.
 *
 * OJO: los datos calculados en render() NO pueden ser propiedades públicas con el
 * mismo nombre: Livewire aplica las públicas DESPUÉS y pisaría lo que se pasa a la vista.
 */
class DetalleKam extends Component
{
    const VISTAS = [
        'todas'        => ['Todas',          'Todos los proyectos que abrió en el año, del más reciente al más antiguo.'],
        'por_facturar' => ['Por facturar',   'Ventas ya ejecutadas o facturadas solo en parte: es plata que se puede cobrar ya. Arriba las de mayor saldo.'],
        'ejecucion'    => ['En ejecución',   'Ventas que todavía se están ejecutando: aún no toca facturarlas.'],
        'quietas'      => ['Quietas',        'Gestiones en curso que nadie ha movido en los días elegidos. Son las primeras a revisar con el comercial.'],
        'en_curso'     => ['En curso',       'Todavía no son venta ni pérdida: prospectos, oportunidades, cotizaciones y propuestas enviadas.'],
        'perdidas'     => ['Perdidas',       'Gestiones que el comercial marcó como no dadas, con la causa que registró.'],
        'revisar'      => ['Revisar datos',  'Ventas donde dos pantallas del CRM no dicen lo mismo. No es un error del comercial necesariamente: hay que confirmar cuál dato es el correcto.'],
    ];

    public $abierto = false;
    public $kamId = null;
    public $kam = '';
    public $equipo = null;
    public $anio;
    public $diasQuieta = 30;
    public $vista = 'todas';
    public $buscar = '';

    protected $listeners = ['abrirKam' => 'abrir'];

    public function abrir($kamId, $anio, $diasQuieta = 30)
    {
        $this->kamId = (int) $kamId;
        $this->anio = (int) $anio;
        $this->diasQuieta = (int) $diasQuieta;
        $u = User::find($this->kamId);
        $this->kam = $u ? $u->name : 'Comercial';
        $this->equipo = D::lideres()[$this->kamId] ?? null;
        $this->vista = 'todas';
        $this->buscar = '';
        $this->abierto = true;
        $this->dispatchBrowserEvent('crm-modal', ['abierto' => true]);
    }

    public function cerrar()
    {
        $this->abierto = false;
        $this->dispatchBrowserEvent('crm-modal', ['abierto' => false]);
    }

    public function verVista($v)
    {
        if (isset(self::VISTAS[$v])) { $this->vista = $v; }
    }

    private function enVista($p, string $vista): bool
    {
        switch ($vista) {
            case 'por_facturar': return $p->por_facturar > 0;
            case 'ejecucion':    return $p->cobro === 'ejecucion';
            case 'quietas':      return $p->grupo === 'curso' && $p->dias_quieta > $this->diasQuieta;
            case 'en_curso':     return $p->grupo === 'curso';
            case 'perdidas':     return $p->grupo === 'perdida';
            case 'revisar':      return $p->revisar;
            default:             return true;
        }
    }

    public function render()
    {
        if (!$this->abierto || !$this->kamId) {
            return view('livewire.admin.detalle-kam', ['proyectos' => collect(), 'resumen' => null, 'conteos' => []]);
        }

        $todos = D::gestiones($this->anio, null, $this->kamId);
        $resumen = D::resumen($todos, $this->diasQuieta);

        // Búsqueda sobre todo; las pestañas se cuentan después de buscar
        $t = mb_strtolower(trim($this->buscar));
        $base = $t === '' ? $todos : $todos->filter(function ($p) use ($t) {
            foreach (['proyecto', 'cliente', 'contacto', 'centro', 'cod_cc'] as $c) {
                if ($p->$c && str_contains(mb_strtolower($p->$c), $t)) { return true; }
            }
            return false;
        });

        $conteos = [];
        foreach (array_keys(self::VISTAS) as $v) {
            $conteos[$v] = $base->filter(fn ($p) => $this->enVista($p, $v))->count();
        }

        $proyectos = $base->filter(fn ($p) => $this->enVista($p, $this->vista))->values();
        if ($this->vista === 'por_facturar') { $proyectos = $proyectos->sortByDesc('por_facturar')->values(); }
        if ($this->vista === 'quietas')      { $proyectos = $proyectos->sortByDesc('dias_quieta')->values(); }

        return view('livewire.admin.detalle-kam', compact('proyectos', 'resumen', 'conteos'));
    }
}
