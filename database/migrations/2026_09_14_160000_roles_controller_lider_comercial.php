<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Roles propios para CONTROLLER (11) y LÍDER COMERCIAL (12).
 *
 * Hasta ahora ambos trabajaban con cuentas Admin (rol 1): "controller" era
 * solo un área de notificación y "líder comercial" solo la tabla
 * lider_comercial_user. Decisión de negocio (13-sep-2026): cada uno pasa a
 * ser un rol con su propio menú y sus propias pantallas.
 *
 * - Controller: las cuentas del área controller (por correo) reciben el rol
 *   11 como rol activo. Conservan el rol Admin como rol secundario en
 *   role_user para poder volver mientras se valida el alcance.
 * - Líder comercial: todo usuario que aparece como lider_id en
 *   lider_comercial_user y hoy es Admin recibe el rol 12 como rol activo,
 *   también con Admin como secundario.
 *
 * Idempotente: puede correr varias veces sin duplicar nada.
 */
class RolesControllerLiderComercial extends Migration
{
    const ROL_CONTROLLER = 11;
    const ROL_LIDER_COMERCIAL = 12;

    /** Correos de las cuentas del área controller (cuentas Admin hoy). */
    const CORREOS_CONTROLLER = [
        'adriana.trujillo@bullmarketing.com.co',
        'controllercuentascobro@bullmarketing.com.co',
        'Lider.Controller@bullmarketing.com.co',
    ];

    public function up()
    {
        foreach ([self::ROL_CONTROLLER => 'Controller', self::ROL_LIDER_COMERCIAL => 'Líder comercial'] as $id => $nombre) {
            if (!DB::table('roles_user')->where('id', $id)->exists()) {
                DB::table('roles_user')->insert(['id' => $id, 'description' => $nombre, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Controller
        $controllers = DB::table('users')->whereIn(DB::raw('LOWER(email)'), array_map('strtolower', self::CORREOS_CONTROLLER))->get(['id', 'rol']);
        foreach ($controllers as $u) {
            $this->asignar($u, self::ROL_CONTROLLER);
        }

        // Líderes comerciales = quienes tienen comerciales a cargo
        $lideres = DB::table('users')
            ->whereIn('id', DB::table('lider_comercial_user')->distinct()->pluck('lider_id'))
            ->get(['id', 'rol']);
        foreach ($lideres as $u) {
            $this->asignar($u, self::ROL_LIDER_COMERCIAL);
        }
    }

    /** Da el rol en role_user y lo deja como activo si el usuario era Admin. */
    private function asignar($u, $rol)
    {
        if (!DB::table('role_user')->where(['user_id' => $u->id, 'rol_id' => $rol])->exists()) {
            DB::table('role_user')->insert(['user_id' => $u->id, 'rol_id' => $rol, 'created_at' => now(), 'updated_at' => now()]);
        }
        // Conserva Admin como rol secundario (si lo tenía) y activa el nuevo rol
        if ((int) $u->rol === 1) {
            if (!DB::table('role_user')->where(['user_id' => $u->id, 'rol_id' => 1])->exists()) {
                DB::table('role_user')->insert(['user_id' => $u->id, 'rol_id' => 1, 'created_at' => now(), 'updated_at' => now()]);
            }
            DB::table('users')->where('id', $u->id)->update(['rol' => $rol]);
        }
    }

    public function down()
    {
        foreach ([self::ROL_CONTROLLER, self::ROL_LIDER_COMERCIAL] as $rol) {
            // Quien tenía el rol activo vuelve a Admin
            DB::table('users')->where('rol', $rol)->update(['rol' => 1]);
            DB::table('role_user')->where('rol_id', $rol)->delete();
            DB::table('permiso_rol')->where('rol_id', $rol)->delete();
            DB::table('roles_user')->where('id', $rol)->delete();
        }
    }
}
