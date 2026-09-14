<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El flujo de órdenes de nómina (Livewire Productor\Ordenes\Nomina) crea
 * órdenes con tipo_oc = 3, pero el catálogo tipo_ordenes_compra solo tenía
 * 1 Jurídica y 2 Natural: la clave foránea rechazaba TODA nómina.
 */
class TipoOrdenNomina extends Migration
{
    public function up()
    {
        if (!DB::table('tipo_ordenes_compra')->where('id', 3)->exists()) {
            DB::table('tipo_ordenes_compra')->insert(['id' => 3, 'description' => 'Nomina', 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down()
    {
        if (!DB::table('ordenes_compra')->where('tipo_oc', 3)->exists()) {
            DB::table('tipo_ordenes_compra')->where('id', 3)->delete();
        }
    }
}
