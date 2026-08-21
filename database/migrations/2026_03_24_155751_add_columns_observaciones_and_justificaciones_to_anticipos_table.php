<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsObservacionesAndJustificacionesToAnticiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anticipos', function (Blueprint $table) {
            $table->string('observaciones_revision_lider')->nullable()->after('fecha_comprobante_pago');
            $table->string('rechazo_revision_lider')->nullable()->after('observaciones_revision_lider');
            $table->string('observaciones_revision_gerencia')->nullable()->after('rechazo_revision_lider');
            $table->string('rechazo_revision_gerencia')->nullable()->after('observaciones_revision_gerencia');
            $table->string('observaciones_revision_evidencias')->nullable()->after('rechazo_revision_gerencia');
            $table->string('rechazo_revision_evidencias')->nullable()->after('observaciones_revision_evidencias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('anticipos', function (Blueprint $table) {
            $table->dropColumn('observaciones_revision_lider');
            $table->dropColumn('rechazo_revision_lider');
            $table->dropColumn('observaciones_revision_gerencia');
            $table->dropColumn('rechazo_revision_gerencia');
            $table->dropColumn('observaciones_revision_evidencias');
            $table->dropColumn('rechazo_revision_evidencias');
        });
    }
}
