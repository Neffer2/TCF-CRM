<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPorcentajeToOrdenesCompraTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('ordenes_compra', 'porcentaje')) {
            return;
        }

        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->decimal('porcentaje', 5, 2)->nullable();
        });
    }

    public function down()
    {
        // Se conserva el campo: puede existir previamente y contener datos de negociación.
    }
}
