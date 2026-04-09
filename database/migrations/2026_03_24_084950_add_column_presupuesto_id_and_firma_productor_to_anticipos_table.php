<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPresupuestoIdAndFirmaProductorToAnticiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anticipos', function (Blueprint $table) {
            $table->foreignId('presupuesto_id')
                ->nullable()
                ->after('oc_id')
                ->constrained('presupuesto_proyecto')
                ->cascadeOnDelete();

            $table->string('firma_productor')->nullable()->after('productor_id');
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
            $table->dropForeign(['presupuesto_id']);
            $table->dropColumn('presupuesto_id');

            $table->dropColumn('firma_productor');
        });
    }
}
