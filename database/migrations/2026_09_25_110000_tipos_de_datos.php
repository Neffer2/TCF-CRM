<?php

use App\Support\EsquemaBd;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * MEJORAS DE BASE DE DATOS 2/7 — Tipos de datos.
 *
 * - porcentaje (base_comerciales, helisa): texto → DECIMAL(9,4). Se comprueba antes que todos los valores sean números.
 * - historial_items_presupuesto.valores_anteriores: texto → JSON (se comprueba que todas las filas sean JSON válido).
 * - items_presupuesto.mes guardaba como texto el id del periodo (meses.id): pasa a mes_id con llave al catálogo
 *   (0 y vacío = sin mes → NULL). Se comprueba antes que todos los valores existan en meses.
 * Si alguna comprobación falla, la migración se detiene sin cambiar nada y dice qué revisar.
 */
class TiposDeDatos extends Migration
{
    public function up()
    {
        foreach (['base_comerciales', 'helisa'] as $t) {
            if (!EsquemaBd::columna($t, 'porcentaje') || strpos((string) EsquemaBd::tipoColumna($t, 'porcentaje'), 'decimal') === 0) { continue; }
            $malos = DB::selectOne("SELECT COUNT(*) n FROM `{$t}` WHERE porcentaje IS NOT NULL AND porcentaje <> '' AND porcentaje NOT REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$'")->n;
            if ($malos > 0) { throw new RuntimeException("{$t}.porcentaje tiene {$malos} valores que no son números: corrígelos antes de migrar."); }
            $largos = DB::selectOne("SELECT COUNT(*) n FROM `{$t}` WHERE porcentaje LIKE '%.%' AND LENGTH(SUBSTRING_INDEX(porcentaje, '.', -1)) > 4")->n;
            if ($largos > 0) { throw new RuntimeException("{$t}.porcentaje tiene {$largos} valores con más de 4 decimales; se perderían al convertir."); }
            DB::statement("UPDATE `{$t}` SET porcentaje = NULL WHERE porcentaje = ''");
            $que = $t === 'helisa' ? 'Participación del comercial en la factura (%)' : 'Participación del comercial en el proyecto (%)';
            DB::statement("ALTER TABLE `{$t}` MODIFY COLUMN `porcentaje` DECIMAL(9,4) NULL COMMENT '{$que}'");
        }

        if (EsquemaBd::columna('historial_items_presupuesto', 'valores_anteriores') && strpos((string) EsquemaBd::tipoColumna('historial_items_presupuesto', 'valores_anteriores'), 'json') !== 0) {
            $inv = DB::selectOne('SELECT COUNT(*) n FROM historial_items_presupuesto WHERE valores_anteriores IS NOT NULL AND NOT JSON_VALID(valores_anteriores)')->n;
            if ($inv > 0) { throw new RuntimeException("historial_items_presupuesto.valores_anteriores tiene {$inv} filas que no son JSON válido."); }
            $nulo = DB::selectOne("SELECT is_nullable n FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'historial_items_presupuesto' AND column_name = 'valores_anteriores'")->n === 'YES';
            DB::statement("ALTER TABLE `historial_items_presupuesto` MODIFY COLUMN `valores_anteriores` JSON ".($nulo ? 'NULL' : 'NOT NULL')." COMMENT 'Copia del ítem antes del cambio (JSON); con tipo_registro = snapshot_aprobacion guarda todos los ítems al aprobar'");
        }

        if (EsquemaBd::columna('items_presupuesto', 'mes') && !EsquemaBd::columna('items_presupuesto', 'mes_id')) {
            $raros = DB::selectOne("SELECT COUNT(*) n FROM items_presupuesto WHERE mes IS NOT NULL AND mes <> '' AND mes NOT REGEXP '^[0-9]+$'")->n;
            if ($raros > 0) { throw new RuntimeException("items_presupuesto.mes tiene {$raros} valores que no son números: revísalos antes de migrar."); }
            $sinCatalogo = DB::selectOne("SELECT COUNT(*) n FROM items_presupuesto WHERE mes REGEXP '^[0-9]+$' AND CAST(mes AS UNSIGNED) > 0 AND CAST(mes AS UNSIGNED) NOT IN (SELECT id FROM meses)")->n;
            if ($sinCatalogo > 0) { throw new RuntimeException("items_presupuesto: {$sinCatalogo} ítems tienen un mes que no existe en el catálogo meses."); }
            DB::statement("ALTER TABLE `items_presupuesto` ADD COLUMN `mes_id` BIGINT UNSIGNED NULL COMMENT 'Periodo (meses.id) en que se ejecuta el ítem; NULL = sin mes (eventos)' AFTER `mes`");
            DB::statement("UPDATE items_presupuesto SET mes_id = CAST(mes AS UNSIGNED) WHERE mes REGEXP '^[0-9]+$' AND CAST(mes AS UNSIGNED) > 0");
            DB::statement('ALTER TABLE `items_presupuesto` ADD CONSTRAINT `items_presupuesto_mes_id_foreign` FOREIGN KEY (`mes_id`) REFERENCES `meses`(`id`) ON DELETE SET NULL');
            DB::statement('ALTER TABLE `items_presupuesto` DROP COLUMN `mes`');
        }
    }

    public function down()
    {
        if (EsquemaBd::columna('items_presupuesto', 'mes_id') && !EsquemaBd::columna('items_presupuesto', 'mes')) {
            DB::statement("ALTER TABLE `items_presupuesto` ADD COLUMN `mes` VARCHAR(255) NULL AFTER `mes_id`");
            DB::statement("UPDATE items_presupuesto SET mes = IFNULL(mes_id, '')");
            if ($fk = EsquemaBd::fkDeColumna('items_presupuesto', 'mes_id')) { DB::statement("ALTER TABLE `items_presupuesto` DROP FOREIGN KEY `{$fk}`"); }
            DB::statement('ALTER TABLE `items_presupuesto` DROP COLUMN `mes_id`');
        }
        if (strpos((string) EsquemaBd::tipoColumna('historial_items_presupuesto', 'valores_anteriores'), 'json') === 0) {
            DB::statement('ALTER TABLE `historial_items_presupuesto` MODIFY COLUMN `valores_anteriores` LONGTEXT NULL');
        }
        foreach (['base_comerciales', 'helisa'] as $t) {
            if (strpos((string) EsquemaBd::tipoColumna($t, 'porcentaje'), 'decimal') === 0) {
                DB::statement("ALTER TABLE `{$t}` MODIFY COLUMN `porcentaje` VARCHAR(255) NULL");
            }
        }
    }
}
