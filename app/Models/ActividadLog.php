<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Una fila por cada cosa que pasa en el CRM: página vista, acción de
 * Livewire (con los datos), sesión y cambio de datos.
 */
class ActividadLog extends Model
{
    protected $table = 'actividad_log';
    public $timestamps = false;
    protected $fillable = ['user_id', 'usuario', 'rol', 'tipo', 'accion', 'objeto', 'detalle', 'metodo', 'ruta', 'estado_http', 'duracion_ms', 'ip', 'navegador', 'created_at'];
    protected $casts = ['detalle' => 'array', 'created_at' => 'datetime'];

    /** Claves que nunca se guardan en el detalle. */
    const OCULTAR = ['password', 'password_confirmation', 'clave', '_token', 'remember', 'checksum', 'htmlHash', 'serverMemo', 'signature'];

    public static function registrar(string $tipo, string $accion, array $extra = []): ?self
    {
        try {
            $u = Auth::user();
            return self::create(array_merge([
                'user_id' => optional($u)->id, 'usuario' => optional($u)->name, 'rol' => optional($u)->rol,
                'tipo' => $tipo, 'accion' => mb_substr($accion, 0, 160),
                'ip' => request()->ip(), 'navegador' => mb_substr((string) request()->userAgent(), 0, 255),
                'created_at' => now(),
            ], $extra));
        } catch (\Throwable $e) {
            \Log::warning('actividad_log: '.$e->getMessage());
            return null;
        }
    }

    /** Quita claves sensibles y acorta valores largos, recursivamente. */
    public static function limpiar($datos, int $nivel = 0)
    {
        if (!is_array($datos)) {
            if (is_string($datos)) {
                $datos = mb_check_encoding($datos, 'UTF-8') ? $datos : mb_convert_encoding($datos, 'UTF-8', 'ISO-8859-1');
                return mb_strlen($datos) > 500 ? mb_substr($datos, 0, 500).'…' : $datos;
            }
            return $datos;
        }
        if ($nivel > 4) { return '[…]'; }
        $out = [];
        foreach ($datos as $k => $v) {
            if (in_array((string) $k, self::OCULTAR, true) || preg_match('/pass|token|secret|clave/i', (string) $k)) { $out[$k] = '••••'; continue; }
            $out[$k] = self::limpiar($v, $nivel + 1);
        }
        return $out;
    }

    public function user() { return $this->belongsTo(User::class); }
}
