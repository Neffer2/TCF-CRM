<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meses', function (Blueprint $table) {
            $table->id();
            $table->string('description');

            $table->foreignId('ano_id');
            $table->foreign('ano_id')->references('id')->on('anos');
            $table->date('f_inicio');
            $table->date('f_fin');
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
        Schema::dropIfExists('meses');
    }
}
