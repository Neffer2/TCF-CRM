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

            $table->string('desc_oc');
            $table->string('cant_oc');
            $table->string('vunit_oc');
            $table->string('vtotal_oc');
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
