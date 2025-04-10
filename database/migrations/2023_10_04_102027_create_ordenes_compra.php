<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesCompra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_oc');
            $table->foreign('tipo_oc')->references('id')->on('tipo_ordenes_compra');

            $table->foreignId('estado_id')->default(2);
            $table->foreign('estado_id')->references('id')->on('estados_ordenes_compra');

            $table->foreignId('presupuesto_id')->nullable();
            $table->foreign('presupuesto_id')->references('id')->on('presupuesto_proyecto');

            $table->foreignId('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('proveedores');

            $table->string('justificacion_rechazo')->nullable();
            $table->string('archivo_cot')->nullable();
            $table->string('archivo_orden_helisa')->nullable();
            $table->string('cod_causal')->nullable();
            $table->string('observacion_causal')->nullable();

            $table->string('archivo_comprobante_pago')->nullable();

            $table->string('archivo_remision')->nullable();
            $table->string('observacion_remision')->nullable();
            $table->string('archivo_firma')->nullable();

            $table->string('cod_oc')->nullable();
            $table->string('gr')->nullable();
            $table->string('observaciones_anulacion')->nullable();
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
        Schema::dropIfExists('ordenes_compra');
    }
}
