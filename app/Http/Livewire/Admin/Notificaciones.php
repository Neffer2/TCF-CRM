<?php

namespace App\Http\Livewire\Admin;

use App\Models\NotificacionLog;
use App\Services\Notificador;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Pantalla de administración: qué correos y SMS ha enviado el CRM, cuáles
 * fallaron y por qué, con reenvío manual. Es la fuente de las alertas a
 * desarrollo.
 */
class Notificaciones extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $canal = '';
    public $estado = '';
    public $buscar = '';
    public $abierto = null; // id del registro desplegado

    public function updating($campo) { if (in_array($campo, ['canal', 'estado', 'buscar'], true)) { $this->resetPage(); } }

    public function reintentar($id)
    {
        $log = NotificacionLog::find($id);
        if ($log) { Notificador::reintentar($log); }
    }

    public function ver($id) { $this->abierto = $this->abierto == $id ? null : $id; }

    public function render()
    {
        $q = NotificacionLog::query()->latest();
        if ($this->canal) { $q->where('canal', $this->canal); }
        if ($this->estado) { $q->where('estado', $this->estado); }
        if (trim($this->buscar) !== '') {
            $t = '%'.trim($this->buscar).'%';
            $q->where(function ($w) use ($t) { $w->where('evento', 'LIKE', $t)->orWhere('asunto', 'LIKE', $t)->orWhere('referencia', 'LIKE', $t)->orWhere('destinatarios', 'LIKE', $t)->orWhere('error', 'LIKE', $t); });
        }
        return view('livewire.admin.notificaciones', [
            'registros' => $q->paginate(25),
            'salud' => Notificador::salud(),
            'porCanal' => NotificacionLog::ultimas24h()->selectRaw('canal, estado, COUNT(*) n')->groupBy('canal', 'estado')->get()->groupBy('canal'),
            'alertas' => config('crm.notificaciones.alertas'),
        ]);
    }
}
