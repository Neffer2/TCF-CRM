<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelisaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('helisa', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('tipo_doc');
            $table->string('num_doc');
            $table->string('concepto');
            $table->string('identidad');
            $table->string('nom_tercero');
            $table->string('centro'); 
            $table->string('nom_centro_costo');
            $table->decimal('debito', 12, 2)->nullable();
            $table->decimal('credito', 12, 2)->nullable();
            $table->foreignId('comercial');
            $table->foreign('comercial')->references('id')->on('users');
            $table->string('participacion');
            $table->decimal('base_factura', 12, 2); 
            $table->string('mes');
            $table->string('año');
            $table->decimal('comision', 12, 2); 
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
        Schema::dropIfExists('helisa');
    }
}
