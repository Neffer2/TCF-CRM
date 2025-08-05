<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oc_id');
            $table->foreign('oc_id')->references('id')->on('ordenes_compra');

            $table->foreignId('item_id');
            $table->foreign('item_id')->references('id')->on('items_presupuesto');
            $table->string('display_item')->nullable();

            $table->string('desc_oc', 2000);
            $table->integer('cant_oc');
            $table->integer('dias_oc');
            $table->integer('otros_oc');
            $table->decimal('vunit_oc', 15, 2)->default(0);
            $table->decimal('vtotal_oc', 15, 2)->default(0);
            $table->string('tipo_servicio')->nullable();
            $table->string('tipo_contrato')->nullable();
            $table->integer('cantidad_horas')->nullable();
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
        Schema::dropIfExists('oc_items');
    }
}
