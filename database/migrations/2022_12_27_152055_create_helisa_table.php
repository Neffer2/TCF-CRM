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
            $table->string('concepto')->nullable();
            $table->string('identidad');
            $table->string('nom_tercero');
            $table->string('centro'); 
            $table->string('nom_centro_costo'); 
            $table->decimal('debito', 12, 2)->nullable();
            $table->decimal('credito', 12, 2)->nullable();
            $table->string('porcentaje')->default(100);
            $table->foreignId('comercial');
            $table->foreign('comercial')->references('id')->on('users');
            $table->foreignId('id_cuenta')->default(1);
            $table->foreign('id_cuenta')->references('id')->on('cuentas');
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
