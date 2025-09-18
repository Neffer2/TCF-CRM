<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnticiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anticipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oc_id');
            $table->foreign('oc_id')->references('id')->on('ordenes_compra');
            $table->decimal('porcentaje_anticipo', 5, 2);
            $table->decimal('total_anticipo', 15, 2);
            $table->dateTime('fecha_solicitud')->nullable();
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->string('justificacion_rechazo')->nullable();
            $table->foreignId('productor_id');
            $table->foreign('productor_id')->references('id')->on('users');
            $table->foreignId('estado_id')->default(2);
            $table->foreign('estado_id')->references('id')->on('estados_ordenes_compra');
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
        Schema::dropIfExists('anticipos');
    }
}
