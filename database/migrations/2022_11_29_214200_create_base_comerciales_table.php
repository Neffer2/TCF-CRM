<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseComercialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_comerciales', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('nom_cliente');
            $table->string('nom_proyecto'); 
            $table->string('cod_cc')->nullable();
            $table->decimal('valor_proyecto', 15, 2)->default(0)->nullable();
            $table->string('com_1')->nullable();
            $table->string('com_2')->nullable();
            $table->string('com_3')->nullable();
            $table->foreignId('id_estado');
            $table->foreign('id_estado')->references('id')->on('estados_cuenta');
            $table->date('fecha_inicio')->nullable();
            $table->date('dura_mes')->nullable();
            $table->foreignId('id_user');
            $table->foreign('id_user')->references('id')->on('users');        
            $table->foreignId('id_asistente')->nullable();
            $table->foreign('id_asistente')->references('id')->on('users');
            $table->foreignId('id_cuenta')->default(1);
            $table->foreign('id_cuenta')->references('id')->on('cuentas');

            $table->foreignId('id_gestion')->nullable();
            $table->foreign('id_gestion')->references('id')->on('gestion_comercial');
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
        Schema::dropIfExists('base_comerciales');
    }
}
