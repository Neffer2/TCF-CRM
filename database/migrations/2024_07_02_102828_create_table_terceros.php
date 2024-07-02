<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTerceros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terceros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula');
            $table->string('correo');
            $table->string('telefono');
            $table->string('ciudad');
            $table->string('banco');
            $table->boolean('rut');
            $table->string('cert_bancaria');
            $table->string('firma');
            $table->boolean('terminos');
            $table->foreignId('estado');
            $table->foreign('estado')->references('id')->on('estados_tercero');        
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
        Schema::dropIfExists('terceros');
    }
}
