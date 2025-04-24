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
            $table->string('servicio')->nullable();
            $table->string('ciudad');
            $table->string('banco')->nullable();
            $table->string('num_rut')->nullable();
            $table->string('rut')->nullable();
            $table->string('cert_bancaria')->nullable();
            $table->string('copia_cedula')->nullable();
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
