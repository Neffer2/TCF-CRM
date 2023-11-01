<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('categorias_proveedor');

            $table->string('tercero');
            $table->string('tipo'); 
            $table->string('tipo_doc');
            $table->string('documento')->unique();
            $table->string('dv');
            $table->string('direccion');
            $table->string('departamento');
            $table->string('ciudad');
            $table->string('servicio');
            $table->string('celular');
            $table->string('fijo');
            $table->string('correo');
            $table->string('plazo');
            $table->string('contacto');
            $table->string('web');
            $table->string('observaciones');
            $table->string('estado');
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
        Schema::dropIfExists('proveedores');
    }
}
