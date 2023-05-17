<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ItemsPresupuestoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->string('presupuesto_id');
            $table->integer('cod');
            $table->string('evento')->default(0);
            $table->integer('cantidad');
            $table->integer('dia');
            $table->integer('otros');
            $table->string('descripcion');
            $table->decimal('v_unitario', 15, 2)->default(0);            
            $table->decimal('v_total', 15, 2)->default(0);          
            $table->string('proveedor');
            $table->double('margen_utilidad', 15, 10)->default(0.0);
            $table->string('mes');
            $table->integer('dias');
            $table->string('ciudad');

            $table->decimal('v_unitario_cot', 15, 2)->default(0);            
            $table->decimal('v_total_cot', 15, 2)->default(0);
            $table->decimal('rentabilidad', 15, 2)->default(0);
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
        Schema::dropIfExists('items_presupuesto');
    }
}
