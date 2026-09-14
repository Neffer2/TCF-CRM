<?php

namespace App\Observers;

use App\Models\ActividadLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Deja rastro de cada creación, cambio y borrado de los registros
 * principales: qué campos cambiaron y de qué valor a cuál.
 */
class AuditoriaObserver
{
    const IGNORAR = ['updated_at', 'created_at', 'remember_token', 'password'];

    public function created(Model $m) { $this->log($m, 'creado', ActividadLog::limpiar(array_diff_key($m->getAttributes(), array_flip(self::IGNORAR)))); }

    public function updated(Model $m)
    {
        $cambios = [];
        foreach ($m->getChanges() as $campo => $nuevo) {
            if (in_array($campo, self::IGNORAR, true)) { continue; }
            $cambios[$campo] = ['antes' => ActividadLog::limpiar($m->getOriginal($campo)), 'despues' => ActividadLog::limpiar($nuevo)];
        }
        if ($cambios) { $this->log($m, 'modificado', $cambios); }
    }

    public function deleted(Model $m) { $this->log($m, 'borrado', ActividadLog::limpiar(array_intersect_key($m->getAttributes(), array_flip(['id', 'estado_id', 'cod_oc', 'cod_cc', 'nom_proyecto', 'name', 'email'])))); }

    private function log(Model $m, string $evento, $detalle)
    {
        ActividadLog::registrar('datos', $m->getTable().':'.$evento, [
            'objeto' => $m->getTable().':'.$m->getKey(),
            'detalle' => $detalle ?: null,
            'ruta' => request() ? '/'.ltrim(request()->path(), '/') : null,
        ]);
    }
}
