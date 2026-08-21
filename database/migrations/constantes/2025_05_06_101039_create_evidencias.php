<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvidencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oc_id');
            $table->foreign('oc_id')->references('id')->on('ordenes_compra');
            $table->foreignId('tercero_id');
            $table->foreign('tercero_id')->references('id')->on('terceros');
            $table->date('fecha_evidencia')->nullable();
            $table->text('foto_evidencia')->nullable();
            $table->text('observacion_evidencia')->nullable();
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
        Schema::dropIfExists('evidencias');
    }
}
