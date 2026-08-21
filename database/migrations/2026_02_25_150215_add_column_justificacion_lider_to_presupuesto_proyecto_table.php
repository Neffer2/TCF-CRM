<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnJustificacionLiderToPresupuestoProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presupuesto_proyecto', function (Blueprint $table) {
            $table->string('justificacion_lider')->nullable()->after('justificacion_compras');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presupuesto_proyecto', function (Blueprint $table) {
            $table->dropColumn('justificacion_lider');
        });
    }
}
