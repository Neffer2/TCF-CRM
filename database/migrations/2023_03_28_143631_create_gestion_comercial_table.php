<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGestionComercialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestion_comercial', function (Blueprint $table) {
            $table->id();
            // Prospecto
            $table->string('nombre'); 
            $table->string('apellido');
            $table->string('empresa');
            $table->string('cargo');
            $table->string('celular');
            $table->string('correo');
            $table->string('web');
            $table->string('pbx');
            $table->string('direccion');
            // Oportunidad
            $table->string('contacto')->nullable();
            $table->string('desc_contacto')->nullable();
            // Cotizacion 
            $table->decimal('presto_cot', 15, 2)->default(0);
            $table->string('nom_proyecto_cot')->nullable();
            $table->date('fecha_estimada_cot')->nullable();
            $table->string('cotizacion_file')->nullable();
            // Propuesta 
            $table->decimal('presto_prop', 15, 2)->default(0);
            $table->string('nom_proyecto_prop')->nullable();
            $table->date('fecha_estimada_prop')->nullable();
            $table->string('propuesta_url')->nullable();
            // Perdido
            $table->string('causa')->nullable();
            // Estados
            $table->foreignId('id_estado')->default(1);
            $table->foreign('id_estado')->references('id')->on('estados_gestion_comercial');
            // Usuarios 
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
        Schema::dropIfExists('gestion_comercial');
    }
}
