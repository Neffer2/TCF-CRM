<?php

use App\Support\EsquemaBd;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MEJORAS DE BASE DE DATOS 5/7 — Historial de estados en filas, no en columnas.
 *
 * Cada paso de una orden de compra o un anticipo dejaba columnas nuevas (observaciones_revision_lider,
 * rechazo_revision_gerencia, fecha_causal…), casi siempre vacías. Desde ahora cada cambio de estado de
 * órdenes, anticipos, presupuestos, gestiones y terceros queda como una fila en historial_estados
 * (estado anterior, nuevo, quién, cuándo, con qué observación y desde qué pantalla), escrita por
 * App\Observers\HistorialEstadoObserver. Las columnas viejas se conservan con sus datos.
 * Carga inicial: el estado actual de cada registro con la fecha de su última modificación.
 */
class HistorialEstados extends Migration
{
    const FUENTES = [['ordenes_compra', 'estado_id'], ['anticipos', 'estado_id'], ['presupuesto_proyecto', 'estado_id'], ['gestion_comercial', 'id_estado'], ['terceros', 'estado']];

    public function up()
    {
        if (!EsquemaBd::tabla('historial_estados')) {
            Schema::create('historial_estados', function (Blueprint $t) {
                $t->id();
                $t->string('tabla', 40)->comment('Tabla del registro: ordenes_compra, anticipos, presupuesto_proyecto, gestion_comercial o terceros');
                $t->unsignedBigInteger('registro_id')->comment('id del registro en esa tabla');
                $t->unsignedBigInteger('estado_anterior')->nullable()->comment('Estado antes del cambio (NULL al crear el registro o en la carga inicial)');
                $t->unsignedBigInteger('estado_nuevo')->comment('Estado después del cambio (id del catálogo de estados de esa tabla)');
                $t->unsignedBigInteger('user_id')->nullable()->comment('Quién hizo el cambio (users.id); NULL = proceso automático o carga inicial');
                $t->unsignedBigInteger('rol')->nullable()->comment('Rol activo de quien hizo el cambio (roles.id)');
                $t->text('observacion')->nullable()->comment('Justificación u observación escrita en ese paso, si la hubo');
                $t->string('origen', 160)->nullable()->comment('Ruta de la pantalla desde la que se hizo, "consola" o "carga inicial"');
                $t->timestamp('created_at')->nullable()->useCurrent()->comment('Momento del cambio');
                $t->index(['tabla', 'registro_id'], 'historial_estados_registro_idx');
                $t->index('created_at', 'historial_estados_created_at_index');
                $t->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $t->foreign('rol')->references('id')->on('roles')->onDelete('set null');
            });
            DB::statement("ALTER TABLE `historial_estados` COMMENT = 'Historial de cambios de estado (una fila por cambio) de órdenes, anticipos, presupuestos, gestiones y terceros; lo escribe HistorialEstadoObserver'");
        }
        foreach (self::FUENTES as [$t, $c]) {
            if (!EsquemaBd::columna($t, $c)) { continue; }
            $fecha = EsquemaBd::columna($t, 'updated_at') ? 'COALESCE(updated_at, created_at, NOW())' : 'NOW()';
            DB::statement("INSERT INTO historial_estados (tabla, registro_id, estado_anterior, estado_nuevo, user_id, rol, observacion, origen, created_at)
                SELECT '{$t}', f.id, NULL, f.`{$c}`, NULL, NULL, NULL, 'carga inicial', {$fecha}
                FROM `{$t}` f
                WHERE f.`{$c}` IS NOT NULL
                  AND NOT EXISTS (SELECT 1 FROM historial_estados h WHERE h.tabla = '{$t}' AND h.registro_id = f.id)");
        }
    }

    public function down()
    {
        Schema::dropIfExists('historial_estados');
    }
}
