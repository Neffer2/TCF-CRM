<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void 
     */
    public function up()
    {
        Schema::create('tarifario', function (Blueprint $table) {
            $table->id();
            $table->string('concepto')->nullable();
            $table->string('caso')->nullable();
            $table->string('caracteristicas')->nullable();
            $table->decimal('v_unidad', 15, 2)->default(0);
            $table->string('observacion')->nullable();
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
        Schema::dropIfExists('tarifario');
    }
}
