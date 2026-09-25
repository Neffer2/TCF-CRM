<?php

use App\Support\EsquemaBd;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * MEJORAS DE BASE DE DATOS 1/7 — Nombres, llaves e integridad.
 *
 * - roles_user pasa a llamarse roles: era el catálogo de roles y, junto a la pivote role_user, confundía.
 * - Llaves foráneas en el registro de actividad y en el recorrido de anticipos (usuario y rol).
 * - Clave primaria en password_resets; se quita presupuesto_proyecto.comercial_id (vacía en todas las filas
 *   y sin uso en el código; se comprueba antes de quitarla).
 * - Índices para búsquedas frecuentes: cédula de terceros y código de la orden de compra.
 * - Una sola collation (utf8mb4_unicode_ci).
 * - CHECK en los dominios cortos que fija el código (tipo de anticipo, estado de notificación, tipo de actividad).
 * No borra datos.
 */
class RolesEIntegridad extends Migration
{
    const CHECKS = [
        ['anticipos', 'chk_anticipos_tipo', "tipo IN ('juridico','productor')"],
        ['notificaciones_log', 'chk_notificaciones_log_estado', "estado IN ('pendiente','enviado','fallido')"],
        ['actividad_log', 'chk_actividad_log_tipo', "tipo IN ('accion','datos','pagina','sesion')"],
    ];

    public function up()
    {
        if (EsquemaBd::tabla('roles_user') && !EsquemaBd::tabla('roles')) {
            DB::statement('RENAME TABLE `roles_user` TO `roles`');
        }
        EsquemaBd::comentarTabla('roles', 'Catálogo de roles (antes roles_user). users.rol es el rol activo; role_user, los roles que cada cuenta puede usar');

        // Rol en los registros: era SMALLINT y roles.id es BIGINT; se igualan para poder poner la llave.
        foreach (['actividad_log', 'anticipo_pasos'] as $t) {
            if (!EsquemaBd::columna($t, 'rol') || EsquemaBd::fkDeColumna($t, 'rol')) { continue; }
            DB::statement("UPDATE `{$t}` SET rol = NULL WHERE rol IS NOT NULL AND rol NOT IN (SELECT id FROM roles)");
            DB::statement("ALTER TABLE `{$t}` MODIFY COLUMN `rol` BIGINT UNSIGNED NULL");
            DB::statement("ALTER TABLE `{$t}` ADD CONSTRAINT `{$t}_rol_foreign` FOREIGN KEY (`rol`) REFERENCES `roles`(`id`) ON DELETE SET NULL");
        }
        if (EsquemaBd::columna('actividad_log', 'user_id') && !EsquemaBd::fkDeColumna('actividad_log', 'user_id')) {
            DB::statement('UPDATE actividad_log SET user_id = NULL WHERE user_id IS NOT NULL AND user_id NOT IN (SELECT id FROM users)');
            DB::statement('ALTER TABLE `actividad_log` ADD CONSTRAINT `actividad_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL');
        }

        if (EsquemaBd::tabla('password_resets') && !EsquemaBd::tienePk('password_resets')) {
            $repetidos = DB::selectOne('SELECT COUNT(*) n FROM (SELECT email FROM password_resets GROUP BY email HAVING COUNT(*) > 1) d')->n;
            if ($repetidos == 0) { DB::statement('ALTER TABLE `password_resets` ADD PRIMARY KEY (`email`)'); }
            else { echo "  aviso: password_resets tiene correos repetidos; no se pone la clave primaria.\n"; }
        }

        // presupuesto_proyecto.comercial_id: nunca se usó (vacía en todas las filas y sin referencias en el código)
        if (EsquemaBd::columna('presupuesto_proyecto', 'comercial_id')) {
            $usadas = DB::selectOne('SELECT COUNT(*) n FROM presupuesto_proyecto WHERE comercial_id IS NOT NULL')->n;
            if ($usadas == 0) {
                if ($fk = EsquemaBd::fkDeColumna('presupuesto_proyecto', 'comercial_id')) { DB::statement("ALTER TABLE `presupuesto_proyecto` DROP FOREIGN KEY `{$fk}`"); }
                if (EsquemaBd::indice('presupuesto_proyecto', 'presupuesto_proyecto_comercial_id_foreign')) { DB::statement('ALTER TABLE `presupuesto_proyecto` DROP INDEX `presupuesto_proyecto_comercial_id_foreign`'); }
                DB::statement('ALTER TABLE `presupuesto_proyecto` DROP COLUMN `comercial_id`');
            } else {
                echo "  aviso: presupuesto_proyecto.comercial_id tiene {$usadas} valores; se conserva.\n";
            }
        }

        if (EsquemaBd::columna('terceros', 'cedula') && !EsquemaBd::indice('terceros', 'terceros_cedula_index')) {
            DB::statement('ALTER TABLE `terceros` ADD INDEX `terceros_cedula_index` (`cedula`)');
        }
        if (EsquemaBd::columna('ordenes_compra', 'cod_oc') && !EsquemaBd::indice('ordenes_compra', 'ordenes_compra_cod_oc_index')) {
            DB::statement('ALTER TABLE `ordenes_compra` ADD INDEX `ordenes_compra_cod_oc_index` (`cod_oc`)');
        }

        $col = DB::selectOne("SELECT table_collation c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'cliente_parametros_cc'");
        if ($col && $col->c !== 'utf8mb4_unicode_ci') {
            DB::statement('ALTER TABLE `cliente_parametros_cc` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }

        // Descripción del estado de la solicitud (los valores del enum no cambian)
        if (EsquemaBd::columna('solicitud_contactos', 'estado')) {
            EsquemaBd::comentarColumna('solicitud_contactos', 'estado', 'Pendiente, En revisión, Aprobado o Rechazado');
        }

        foreach (self::CHECKS as [$t, $nombre, $cond]) {
            if (!EsquemaBd::tabla($t) || EsquemaBd::restriccion($t, $nombre)) { continue; }
            $fuera = DB::selectOne("SELECT COUNT(*) n FROM `{$t}` WHERE NOT ({$cond})")->n;
            if ($fuera > 0) { echo "  aviso: {$t} tiene {$fuera} filas fuera del dominio; no se pone {$nombre}.\n"; continue; }
            try { DB::statement("ALTER TABLE `{$t}` ADD CONSTRAINT `{$nombre}` CHECK ({$cond})"); }
            catch (\Throwable $e) { echo "  aviso: {$nombre}: {$e->getMessage()}\n"; }
        }
    }

    public function down()
    {
        foreach (self::CHECKS as [$t, $nombre]) {
            if (!EsquemaBd::restriccion($t, $nombre)) { continue; }
            try { DB::statement("ALTER TABLE `{$t}` DROP CHECK `{$nombre}`"); }
            catch (\Throwable $e) { DB::statement("ALTER TABLE `{$t}` DROP CONSTRAINT `{$nombre}`"); }
        }
        if (EsquemaBd::indice('terceros', 'terceros_cedula_index')) { DB::statement('ALTER TABLE `terceros` DROP INDEX `terceros_cedula_index`'); }
        if (EsquemaBd::indice('ordenes_compra', 'ordenes_compra_cod_oc_index')) { DB::statement('ALTER TABLE `ordenes_compra` DROP INDEX `ordenes_compra_cod_oc_index`'); }
        if (!EsquemaBd::columna('presupuesto_proyecto', 'comercial_id')) {
            DB::statement('ALTER TABLE `presupuesto_proyecto` ADD COLUMN `comercial_id` BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE `presupuesto_proyecto` ADD CONSTRAINT `presupuesto_proyecto_comercial_id_foreign` FOREIGN KEY (`comercial_id`) REFERENCES `users`(`id`)');
        }
        if ($fk = EsquemaBd::fkDeColumna('actividad_log', 'user_id')) { DB::statement("ALTER TABLE `actividad_log` DROP FOREIGN KEY `{$fk}`"); }
        foreach (['actividad_log', 'anticipo_pasos'] as $t) {
            if ($fk = EsquemaBd::fkDeColumna($t, 'rol')) { DB::statement("ALTER TABLE `{$t}` DROP FOREIGN KEY `{$fk}`"); }
        }
        if (EsquemaBd::tabla('password_resets') && EsquemaBd::tienePk('password_resets')) { DB::statement('ALTER TABLE `password_resets` DROP PRIMARY KEY'); }
        if (EsquemaBd::tabla('roles') && !EsquemaBd::tabla('roles_user')) { DB::statement('RENAME TABLE `roles` TO `roles_user`'); }
    }
}
