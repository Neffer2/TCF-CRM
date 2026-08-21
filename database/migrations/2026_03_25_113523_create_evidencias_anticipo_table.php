<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvidenciasAnticipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evidencias_anticipo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('anticipo_id')
                ->constrained('anticipos')
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained('items_presupuesto');

            $table->date('fecha_evidencia')->nullable();
            $table->string('foto_evidencia')->nullable();
            $table->string('observacion_evidencia')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evidencias_anticipo');
    }
}
