<?php

namespace App\Http\Livewire\Admin;

use App\Models\ActividadLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Registro de actividad: quién entró, qué páginas vio, qué acciones hizo
 * (con los datos enviados) y qué registros cambió (antes -> después).
 */
class Actividad extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $usuario = '';
    public $tipo = '';
    public $desde = '';
    public $hasta = '';
    public $buscar = '';
    public $abierto = null;
    public $usuarios = [];

    public function mount()
    {
        $this->desde = now()->subDays(7)->toDateString();
        $this->hasta = now()->toDateString();
        $this->usuarios = User::select('id', 'name')->whereIn('id', ActividadLog::select('user_id')->distinct())->orderBy('name')->get()
            ->map(function ($u) { return ['id' => $u->id, 'name' => $u->name]; })->all();
    }

    public function updating($campo) { if (in_array($campo, ['usuario', 'tipo', 'desde', 'hasta', 'buscar'], true)) { $this->resetPage(); } }
    public function ver($id) { $this->abierto = $this->abierto == $id ? null : $id; }

    private function consulta()
    {
        $q = ActividadLog::query();
        if ($this->usuario) { $q->where('user_id', $this->usuario); }
        if ($this->tipo) { $q->where('tipo', $this->tipo); }
        if ($this->desde) { $q->where('created_at', '>=', $this->desde.' 00:00:00'); }
        if ($this->hasta) { $q->where('created_at', '<=', $this->hasta.' 23:59:59'); }
        if (trim($this->buscar) !== '') {
            $t = '%'.trim($this->buscar).'%';
            $q->where(function ($w) use ($t) { $w->where('accion', 'LIKE', $t)->orWhere('ruta', 'LIKE', $t)->orWhere('objeto', 'LIKE', $t)->orWhere('usuario', 'LIKE', $t)->orWhere('detalle', 'LIKE', $t)->orWhere('ip', 'LIKE', $t); });
        }
        return $q;
    }

    public function render()
    {
        $hoy = now()->toDateString();
        $resumen = [
            'usuarios_hoy' => ActividadLog::where('created_at', '>=', $hoy)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'sesiones_hoy' => ActividadLog::where('created_at', '>=', $hoy)->where('tipo', 'sesion')->where('accion', 'login')->count(),
            'acciones_hoy' => ActividadLog::where('created_at', '>=', $hoy)->where('tipo', 'accion')->count(),
            'cambios_hoy' => ActividadLog::where('created_at', '>=', $hoy)->where('tipo', 'datos')->count(),
            'fallidos_hoy' => ActividadLog::where('created_at', '>=', $hoy)->where('accion', 'login fallido')->count(),
        ];
        $porUsuario = ActividadLog::selectRaw('usuario, COUNT(*) n, MAX(created_at) ultimo')->whereNotNull('user_id')
            ->when($this->desde, fn($q) => $q->where('created_at', '>=', $this->desde.' 00:00:00'))
            ->when($this->hasta, fn($q) => $q->where('created_at', '<=', $this->hasta.' 23:59:59'))
            ->groupBy('usuario')->orderByDesc('n')->limit(8)->get();

        return view('livewire.admin.actividad', [
            'registros' => $this->consulta()->latest('created_at')->latest('id')->paginate(40),
            'resumen' => $resumen,
            'porUsuario' => $porUsuario,
        ]);
    }
}
