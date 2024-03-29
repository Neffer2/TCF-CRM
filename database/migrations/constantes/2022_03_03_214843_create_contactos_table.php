<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */ 
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido'); 
            $table->string('empresa');
            $table->string('cargo')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->nullable();
            $table->string('web')->nullable();
            $table->string('pbx')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->foreignId('id_user');
            $table->foreign('id_user')->references('id')->on('users');  
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
        Schema::dropIfExists('contactos');
    }
}
