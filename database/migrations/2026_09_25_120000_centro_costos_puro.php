<?php

use App\Support\EsquemaBd;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * MEJORAS DE BASE DE DATOS 3/7 — Centro de costos con código puro.
 *
 * cod_cc guarda lo que se digitó: en dos de cada tres presupuestos trae el nombre del proyecto pegado
 * ("44230702 POSTOBON CRISTAL MMCALI" o "74230102 - Proyecto"). Como ese código es la llave con Helisa,
 * se añade centro_costos con solo el código (6 a 12 caracteres alfanuméricos con al menos 4 dígitos),
 * con índice, en presupuesto_proyecto y base_comerciales. cod_cc se conserva tal cual (no se pierde nada)
 * y los modelos llenan centro_costos automáticamente al guardar cod_cc.
 * La unicidad sigue siendo regla de la aplicación: hay 62 códigos históricos (casi todos de 2023) compartidos por
 * 134 presupuestos (database/correcciones-datos/07_centros_costo_repetidos.sql); deben resolverse antes de poner UNIQUE.
 */
class CentroCostosPuro extends Migration
{
    // Primer token de cod_cc (antes de " - " y antes del primer espacio)
    const TOKEN = "SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(cod_cc), ' - ', 1), ' ', 1)";

    public function up()
    {
        foreach (['presupuesto_proyecto', 'base_comerciales'] as $t) {
            if (!EsquemaBd::columna($t, 'cod_cc')) { continue; }
            if (!EsquemaBd::columna($t, 'centro_costos')) {
                DB::statement("ALTER TABLE `{$t}` ADD COLUMN `centro_costos` VARCHAR(12) NULL COMMENT 'Código puro del centro de costos (el que usa Helisa), sin el nombre del proyecto; se llena solo al guardar cod_cc' AFTER `cod_cc`");
            }
            $token = self::TOKEN;
            DB::statement("UPDATE `{$t}` SET centro_costos = UPPER({$token})
                WHERE cod_cc IS NOT NULL AND TRIM(cod_cc) <> ''
                  AND {$token} REGEXP '^[A-Za-z0-9]{6,12}$'
                  AND {$token} REGEXP '[0-9].*[0-9].*[0-9].*[0-9]'");
            // Segunda pasada en PHP para lo que el SQL no cubre (código pegado con guion, tabulación o después de una palabra)
            foreach (DB::table($t)->select('id', 'cod_cc')->whereNull('centro_costos')->whereNotNull('cod_cc')->where('cod_cc', '<>', '')->cursor() as $fila) {
                if ($codigo = \App\Models\PresupuestoProyecto::codigoPuro($fila->cod_cc)) {
                    DB::table($t)->where('id', $fila->id)->update(['centro_costos' => $codigo]);
                }
            }
            if (!EsquemaBd::indice($t, "{$t}_centro_costos_index")) {
                DB::statement("ALTER TABLE `{$t}` ADD INDEX `{$t}_centro_costos_index` (`centro_costos`)");
            }
        }
    }

    public function down()
    {
        foreach (['presupuesto_proyecto', 'base_comerciales'] as $t) {
            if (EsquemaBd::columna($t, 'centro_costos')) { DB::statement("ALTER TABLE `{$t}` DROP COLUMN `centro_costos`"); }
        }
    }
}
