<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 1) Registro de todas las notificaciones (correo y SMS) con su resultado,
 *    para auditar "quién recibió qué", reintentar y alertar a desarrollo.
 * 2) Estado 14 "Pagado" para anticipos: antes, tras subir el comprobante
 *    de pago, el anticipo seguía en "Causado – pendiente de pago".
 */
class NotificacionesLogYEstadoPagado extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notificaciones_log')) {
            Schema::create('notificaciones_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('canal', 10)->comment('correo | sms');
                $table->string('evento', 80)->comment('Función que originó el aviso (p. ej. mailOrdenAprobada)');
                $table->string('referencia', 120)->nullable()->comment('Objeto relacionado, p. ej. oc:123, anticipo:4');
                $table->json('destinatarios')->comment('Correos/teléfonos a los que iba dirigido');
                $table->json('copias')->nullable();
                $table->string('asunto', 255)->nullable();
                $table->longText('cuerpo')->comment('HTML del correo o texto del SMS');
                $table->json('adjuntos')->nullable()->comment('Rutas en storage de los adjuntos');
                $table->string('estado', 12)->default('pendiente')->comment('pendiente | enviado | fallido');
                $table->unsignedTinyInteger('intentos')->default(0);
                $table->text('error')->nullable();
                $table->timestamp('enviado_at')->nullable();
                $table->timestamp('alertado_at')->nullable()->comment('Cuándo se avisó a desarrollo del fallo');
                $table->timestamps();
                $table->index(['estado', 'created_at']);
                $table->index(['canal', 'created_at']);
            });
            DB::statement("ALTER TABLE notificaciones_log COMMENT = 'Registro de correos y SMS enviados por el CRM, con resultado y reintentos'");
        }

        if (Schema::hasTable('estados_anticipo') && !DB::table('estados_anticipo')->where('id', 14)->exists()) {
            DB::table('estados_anticipo')->insert(['id' => 14, 'description' => 'Pagado', 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('notificaciones_log');
        DB::table('anticipos')->where('estado_id', 14)->update(['estado_id' => 5]);
        DB::table('estados_anticipo')->where('id', 14)->delete();
    }
}
