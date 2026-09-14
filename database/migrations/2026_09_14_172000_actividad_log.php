<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de actividad (auditoría): cada página vista, cada acción de
 * Livewire (con los datos enviados, sin claves), cada inicio/cierre de
 * sesión y cada cambio en los registros principales (antes/después).
 */
class ActividadLog extends Migration
{
    public function up()
    {
        if (Schema::hasTable('actividad_log')) { return; }
        Schema::create('actividad_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('usuario', 120)->nullable()->comment('Nombre en el momento (por si la cuenta cambia)');
            $table->unsignedSmallInteger('rol')->nullable();
            $table->string('tipo', 12)->index()->comment('sesion | pagina | accion | datos');
            $table->string('accion', 160)->comment('Qué hizo: ruta, componente.metodo, modelo:evento, login/logout');
            $table->string('objeto', 120)->nullable()->comment('Registro afectado, p. ej. ordenes_compra:123');
            $table->json('detalle')->nullable()->comment('Datos enviados o cambios (antes -> después), sin claves');
            $table->string('metodo', 8)->nullable();
            $table->string('ruta', 255)->nullable();
            $table->unsignedSmallInteger('estado_http')->nullable();
            $table->unsignedInteger('duracion_ms')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('navegador', 255)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
        DB::statement("ALTER TABLE actividad_log COMMENT = 'Auditoría: páginas, acciones, sesiones y cambios de datos por usuario'");
    }

    public function down()
    {
        Schema::dropIfExists('actividad_log');
    }
}
