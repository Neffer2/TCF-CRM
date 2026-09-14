<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * REESTRUCTURACIÓN 3/5 — Catálogo de estados propio para anticipos.
 *
 * `anticipos.estado_id` apuntaba al catálogo de órdenes de compra, cuyos
 * nombres no describen el flujo del anticipo (p.ej. 5 = "Comprobado" en
 * órdenes, pero "Causado, pendiente de pago" en anticipos). Se crea
 * `estados_anticipo` conservando los MISMOS ids que ya usa el código, y se
 * agrega el 13 "Rechazo contabilidad" (antes el rechazo contable mandaba
 * el anticipo de productor al estado 2, ajeno a su flujo).
 */
class CrearEstadosAnticipo extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('estados_anticipo')) {
            Schema::create('estados_anticipo', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary();
                $table->string('description');
                $table->string('flujo', 20)->nullable();
                $table->timestamps();
            });
            DB::statement("ALTER TABLE estados_anticipo COMMENT = 'Estados del ciclo de vida de un anticipo. Mismos ids que usa el código; flujo = juridico | productor | ambos'");

            $estados = [
                1  => ['Aprobado — pendiente de causar', 'ambos'],
                2  => ['Revisión (anticipo jurídico)', 'juridico'],
                3  => ['Editable', 'ambos'],
                4  => ['Recibido', 'ambos'],
                5  => ['Causado — pendiente de pago', 'ambos'],
                6  => ['Anulado', 'ambos'],
                7  => ['Cargue de evidencias por el productor', 'productor'],
                8  => ['Revisión líder de producción', 'productor'],
                9  => ['Revisión gerencia', 'productor'],
                10 => ['Revisión de evidencias', 'productor'],
                11 => ['Rechazo líder de producción', 'productor'],
                12 => ['Rechazo gerencia', 'productor'],
                13 => ['Rechazo contabilidad', 'productor'],
            ];
            foreach ($estados as $id => [$desc, $flujo]) {
                DB::table('estados_anticipo')->insert([
                    'id' => $id, 'description' => $desc, 'flujo' => $flujo,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // Cualquier estado en uso que no esté en el catálogo nuevo se agrega
        // para no romper la FK (no debería ocurrir, es una salvaguarda).
        $enUso = DB::table('anticipos')->distinct()->pluck('estado_id');
        foreach ($enUso as $id) {
            if ($id && !DB::table('estados_anticipo')->where('id', $id)->exists()) {
                DB::table('estados_anticipo')->insert(['id' => $id, 'description' => "Estado {$id} (heredado)", 'flujo' => 'ambos', 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // alias explícito: MySQL devuelve CONSTRAINT_NAME en mayúsculas, MariaDB en minúsculas
        $fk = DB::selectOne("SELECT constraint_name AS constraint_name FROM information_schema.key_column_usage WHERE table_schema = DATABASE() AND table_name = 'anticipos' AND column_name = 'estado_id' AND referenced_table_name IS NOT NULL");
        if ($fk && $fk->constraint_name) {
            DB::statement("ALTER TABLE anticipos DROP FOREIGN KEY `{$fk->constraint_name}`");
        }
        Schema::table('anticipos', function (Blueprint $table) {
            $table->foreign('estado_id', 'anticipos_estado_id_fk_estados_anticipo')->references('id')->on('estados_anticipo');
        });
    }

    public function down()
    {
        Schema::table('anticipos', function (Blueprint $table) {
            $table->dropForeign('anticipos_estado_id_fk_estados_anticipo');
            $table->foreign('estado_id')->references('id')->on('estados_ordenes_compra');
        });
        Schema::dropIfExists('estados_anticipo');
    }
}
