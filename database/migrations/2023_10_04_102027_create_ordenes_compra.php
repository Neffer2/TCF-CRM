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

            $table->foreignId('estado_id');
            $table->foreign('estado_id')->references('id')->on('estados_ordenes_compra'); 
            
            $table->foreignId('presto_id');
            $table->foreign('presto_id')->references('id')->on('presupuesto_proyecto');

            $table->string('proveedor');
            $table->string('email_prov');
            $table->string('contacto_prov');
            $table->string('telefono_prov');
            $table->string('archivo_cot');
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
