<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Permisos con nombre (reemplazan los IDs de usuario quemados en vistas y
 * traits). Un usuario tiene un permiso si alguno de sus ROLES lo tiene
 * (tabla role_user -> permiso_rol) o si se le concedió directamente
 * (permiso_user). Los usuarios suspendidos (rol activo 4) nunca lo tienen.
 */
class Permiso extends Model
{
    protected $table = 'permisos';
    protected $fillable = ['clave', 'descripcion'];

    /** Cache por request: [clave][user_id] => bool */
    protected static $cache = [];

    public static function tiene($user, string $clave): bool
    {
        if (!$user || $user->rol == 4) {
            return false;
        }
        if (isset(static::$cache[$clave][$user->id])) {
            return static::$cache[$clave][$user->id];
        }

        $permisoId = DB::table('permisos')->where('clave', $clave)->value('id');
        $resultado = false;
        if ($permisoId) {
            $directo = DB::table('permiso_user')
                ->where('permiso_id', $permisoId)
                ->where('user_id', $user->id)
                ->exists();
            $porRol = $directo ? true : DB::table('permiso_rol')
                ->where('permiso_id', $permisoId)
                ->whereIn('rol_id', function ($q) use ($user) {
                    $q->select('rol_id')->from('role_user')->where('user_id', $user->id);
                })
                ->exists();
            $resultado = $directo || $porRol;
        }

        return static::$cache[$clave][$user->id] = $resultado;
    }

    /**
     * Usuarios activos (no suspendidos) que tienen el permiso, en el formato
     * [['name' => ..., 'email' => ...], ...] que espera sendMail.
     */
    public static function usuariosCon(string $clave): array
    {
        $permisoId = DB::table('permisos')->where('clave', $clave)->value('id');
        if (!$permisoId) {
            return [];
        }

        return User::where('rol', '!=', 4)
            ->where(function ($q) use ($permisoId) {
                $q->whereIn('id', function ($sub) use ($permisoId) {
                    $sub->select('user_id')->from('permiso_user')->where('permiso_id', $permisoId);
                })->orWhereIn('id', function ($sub) use ($permisoId) {
                    $sub->select('user_id')->from('role_user')
                        ->whereIn('rol_id', function ($s2) use ($permisoId) {
                            $s2->select('rol_id')->from('permiso_rol')->where('permiso_id', $permisoId);
                        });
                });
            })
            ->get(['id', 'name', 'email'])
            ->map(function ($u) {
                return ['name' => $u->name, 'email' => $u->email];
            })
            ->all();
    }
}
