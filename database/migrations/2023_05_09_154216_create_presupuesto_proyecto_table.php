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

            $table->double('margen_general', 15, 10)->default(0.0);
            $table->decimal('venta_proy', 15, 2)->default(0);            
            $table->decimal('costos_proy', 15, 2)->default(0);            
            $table->decimal('margen_proy', 15, 2)->default(0);            
            $table->decimal('margen_bruto', 15, 2)->default(0);             

            $table->integer('cod_cot');             
            $table->string('cod_cc')->nullable();                        
            $table->date('fecha_cc')->nullable();               
            
            $table->decimal('imprevistos', 15, 2)->default(0);             
            $table->decimal('administracion', 15, 2)->default(0);             
            $table->decimal('fee', 15, 2)->default(0);             
            $table->integer('tiempo_factura')->default(30);
            $table->string('notas')->nullable();
            
            $table->foreignId('productor')->nullable();
            $table->foreign('productor')->references('id')->on('users'); 
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
