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
            $table->string('direccion')->nullable();
            $table->string('departamento');
            $table->string('ciudad');
            $table->string('servicio');
            $table->string('anticipo');
            $table->string('celular');
            $table->string('fijo')->nullable();
            $table->string('correo')->unique();
            $table->string('plazo')->nullable();
            $table->string('contacto');
            $table->string('web')->nullable();
            $table->string('observaciones')->nullable();
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
