<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asistente_id');
            $table->foreign('asistente_id')->references('id')->on('users');
            $table->foreignId('comercial_id');
            $table->foreign('comercial_id')->references('id')->on('users');
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
        Schema::dropIfExists('asistentes');
    }
}
