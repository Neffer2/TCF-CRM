<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresupuestoProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()  
    {
        Schema::create('presupuesto_proyecto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_gestion'); 
            $table->foreign('id_gestion')->references('id')->on('gestion_comercial');

            $table->foreignId('estado_id')->default(3);
            $table->foreign('estado_id')->references('id')->on('estados_presupuesto');

            $table->decimal('margen_general', 15, 2)->default(0);            
            $table->decimal('venta_proy', 15, 2)->default(0);            
            $table->decimal('costos_proy', 15, 2)->default(0);            
            $table->decimal('margen_proy', 15, 2)->default(0);            
            $table->decimal('margen_bruto', 15, 2)->default(0);            
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
        Schema::dropIfExists('presupuesto_proyecto');
    }
}
