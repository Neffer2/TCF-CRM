<?php

namespace App\Observers;

use App\Models\HistorialEstado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Escribe una fila en historial_estados cada vez que un registro nace o cambia de estado:
 * estado anterior y nuevo, quién, con qué rol, desde qué pantalla y la observación o justificación
 * que se escribió en ese mismo guardado (si la hubo).
 */
class HistorialEstadoObserver
{
    /** Columna de estado por tabla (las demás usan estado_id). */
    const COLUMNAS = ['gestion_comercial' => 'id_estado', 'terceros' => 'estado'];

    public function created(Model $m)
    {
        $this->anotar($m, null, $m->getAttributes());
    }

    public function updated(Model $m)
    {
        $col = $this->columna($m);
        if (!$m->wasChanged($col)) { return; }
        $this->anotar($m, $m->getOriginal($col), $m->getChanges());
    }

    private function columna(Model $m): string
    {
        return self::COLUMNAS[$m->getTable()] ?? 'estado_id';
    }

    private function anotar(Model $m, $anterior, array $candidatos): void
    {
        $nuevo = $m->getAttribute($this->columna($m));
        if ($nuevo === null) { return; }
        $observacion = null;
        foreach ($candidatos as $campo => $valor) {
            if (is_string($valor) && trim($valor) !== '' && preg_match('/observ|justific|rechazo|notas|motivo/i', $campo)) {
                $observacion = mb_substr(trim($valor), 0, 2000);
                break;
            }
        }
        try {
            HistorialEstado::create([
                'tabla' => $m->getTable(),
                'registro_id' => $m->getKey(),
                'estado_anterior' => $anterior !== null ? (int) $anterior : null,
                'estado_nuevo' => (int) $nuevo,
                'user_id' => Auth::id(),
                'rol' => Auth::check() ? (int) Auth::user()->rol : null,
                'observacion' => $observacion,
                'origen' => app()->runningInConsole() ? 'consola' : '/'.ltrim(request()->path(), '/'),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('historial_estados: no se pudo anotar '.$m->getTable().':'.$m->getKey().' — '.$e->getMessage());
        }
    }
}
