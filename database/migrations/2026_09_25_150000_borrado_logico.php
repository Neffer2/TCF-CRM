<?php

use App\Support\EsquemaBd;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * MEJORAS DE BASE DE DATOS 6/7 — Borrado lógico (papelera).
 *
 * Borrar un proyecto, un cliente, un contacto, un proveedor, un registro de base comercial o Helisa, una
 * persona (tercero) o un borrador de orden de compra era físico. Ahora esas tablas tienen deleted_at:
 * la fila sigue en la base, desaparece de las pantallas y se restaura desde Administración → Papelera.
 * presupuesto_proyecto la lleva para acompañar a su gestión (se borra y restaura con ella).
 */
class BorradoLogico extends Migration
{
    const TABLAS = ['gestion_comercial', 'presupuesto_proyecto', 'base_comerciales', 'helisa', 'clientes', 'contactos', 'proveedores', 'terceros', 'ordenes_compra'];

    public function up()
    {
        foreach (self::TABLAS as $t) {
            if (EsquemaBd::tabla($t) && !EsquemaBd::columna($t, 'deleted_at')) {
                DB::statement("ALTER TABLE `{$t}` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Borrado lógico: cuándo se envió a la papelera (NULL = vigente); se restaura desde Administración → Papelera'");
            }
        }
    }

    public function down()
    {
        // Quitar la columna vuelve visibles las filas que estaban en la papelera.
        foreach (self::TABLAS as $t) {
            if (EsquemaBd::columna($t, 'deleted_at')) { DB::statement("ALTER TABLE `{$t}` DROP COLUMN `deleted_at`"); }
        }
    }
}
