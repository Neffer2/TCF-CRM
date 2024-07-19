<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNaturalInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('natural_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oc_id');
            $table->foreign('oc_id')->references('id')->on('ordenes_compra');
            $table->foreignId('tercero_id');
            $table->foreign('tercero_id')->references('id')->on('terceros');
            $table->foreignId('productor_id');
            $table->foreign('productor_id')->references('id')->on('users');
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
        Schema::dropIfExists('natural_info');
    }
}
