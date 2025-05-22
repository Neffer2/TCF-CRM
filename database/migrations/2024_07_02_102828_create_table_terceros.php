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
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('cedula')->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('servicio')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('banco')->nullable();
            $table->string('num_rut')->nullable();
            $table->string('rut')->nullable();
            $table->string('cert_bancaria')->nullable();
            $table->string('copia_cedula')->nullable();
            $table->foreignId('estado')->default(1);
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
