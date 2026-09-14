<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * REESTRUCTURACIÓN 1/5 — Proveedores de los ítems de presupuesto.
 *
 * `items_presupuesto.proveedor` guardaba un array PHP serializado dentro de
 * un varchar (`a:1:{i:0;s:1:"3";}`) y el código lo consultaba con LIKE.
 * Pasa a la tabla pivote `item_presupuesto_proveedor` (una fila por
 * proveedor del ítem) y la columna vieja se conserva como
 * `proveedor_legacy` únicamente para auditoría de valores no migrables
 * ('na', 'bull', '0', ...).
 */
class NormalizarProveedoresItemsPresupuesto extends Migration
{
    public function up()
    {
        if (Schema::hasTable('item_presupuesto_proveedor')) {
            return;
        }

        Schema::create('item_presupuesto_proveedor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_presupuesto_id');
            $table->unsignedBigInteger('proveedor_id');
            $table->unique(['item_presupuesto_id', 'proveedor_id'], 'item_proveedor_unico');
            $table->foreign('item_presupuesto_id')->references('id')->on('items_presupuesto')->onDelete('cascade');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');
        });
        DB::statement("ALTER TABLE item_presupuesto_proveedor COMMENT = 'Proveedores asignados a cada ítem de presupuesto (N:M). Reemplaza el array serializado de items_presupuesto.proveedor'");

        // --- Migración de datos ---
        $proveedoresExistentes = DB::table('proveedores')->pluck('id')->flip();
        $migrados = 0; $sinMigrar = 0; $idsInexistentes = 0;

        DB::table('items_presupuesto')->select('id', 'proveedor')->orderBy('id')
            ->chunk(2000, function ($items) use ($proveedoresExistentes, &$migrados, &$sinMigrar, &$idsInexistentes) {
                $filas = [];
                foreach ($items as $item) {
                    $valor = trim((string) $item->proveedor);
                    $ids = [];
                    if (strpos($valor, 'a:') === 0) {
                        $arr = @unserialize($valor);
                        $ids = is_array($arr) ? $arr : [];
                    } elseif (ctype_digit($valor) && $valor !== '0') {
                        $ids = [$valor];
                    }

                    $ids = array_unique(array_map('intval', $ids));
                    if (empty($ids)) { $sinMigrar++; continue; }

                    foreach ($ids as $pid) {
                        if (!isset($proveedoresExistentes[$pid])) { $idsInexistentes++; continue; }
                        $filas[] = ['item_presupuesto_id' => $item->id, 'proveedor_id' => $pid];
                    }
                    $migrados++;
                }
                if ($filas) {
                    DB::table('item_presupuesto_proveedor')->insertOrIgnore($filas);
                }
            });

        // --- La columna vieja queda solo como auditoría ---
        Schema::table('items_presupuesto', function (Blueprint $table) {
            $table->renameColumn('proveedor', 'proveedor_legacy');
        });
        DB::statement("ALTER TABLE items_presupuesto MODIFY COLUMN proveedor_legacy VARCHAR(255) NULL COMMENT 'OBSOLETO: valor original (array serializado o texto libre). Los proveedores viven en item_presupuesto_proveedor'");

        echo "  proveedores migrados: {$migrados} ítems | sin proveedor migrable (texto libre): {$sinMigrar} | ids inexistentes omitidos: {$idsInexistentes}\n";
    }

    public function down()
    {
        Schema::table('items_presupuesto', function (Blueprint $table) {
            $table->renameColumn('proveedor_legacy', 'proveedor');
        });
        Schema::dropIfExists('item_presupuesto_proveedor');
    }
}
