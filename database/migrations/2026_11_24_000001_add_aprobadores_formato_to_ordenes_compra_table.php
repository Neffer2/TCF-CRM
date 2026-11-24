<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAprobadoresFormatoToOrdenesCompraTable extends Migration
{
    public function up()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->json('aprobadores_formato')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->dropColumn('aprobadores_formato');
        });
    }
}
