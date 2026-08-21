<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnActualizadoToOrdenesCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->boolean('actualizado')
                ->after('gr')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->dropColumn('actualizado');
        });
    }
}
