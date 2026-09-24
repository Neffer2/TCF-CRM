<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Anticipo jurídico según la matriz de flujos de Gerencia (sep-2026), fila "Anticipo jurídica":
 *
 *   Productor ─► Líder de producción ─► Controller ─► ≤ $10 M: aprobado
 *                                                  └► > $10 M: Gerencia ─► aprobado
 *
 * Cualquier revisor puede devolverlo y el productor lo corrige. Al quedar aprobado el sistema
 * genera la orden de anticipo (PDF); después Contabilidad lo causa (o lo devuelve al Controller)
 * y Tesorería lo paga. Del diagrama anterior se conserva el borrador sin orden de compra previa.
 *
 * Cambios:
 * - anticipos.tipo 'juridico' | 'productor': el tipo ya no se puede deducir de oc_id, porque un
 *   A.J. borrador no tiene orden.
 * - anticipos: proveedor, valor base del porcentaje, orden de anticipo (código, PDF, fecha) y la
 *   observación con la que Contabilidad lo devuelve.
 * - estados 15 "Revisión controller" y 16 "Rechazo controller".
 * - anticipo_pasos: historial de cada decisión (quién, cuándo, de qué estado a cuál, con qué
 *   observación). Alimenta el recorrido en pantalla y las firmas de la orden de anticipo.
 * - evidencias_anticipo.item_id opcional: los soportes del A.J. no van por ítem.
 * - Los A.J. que estaban en "Revisión" (2), el paso único del flujo anterior, entran al nuevo en
 *   "Revisión líder de producción" (8).
 */
class AnticipoJuridicoFlujo extends Migration
{
    public function up()
    {
        Schema::table('anticipos', function (Blueprint $t) {
            if (!Schema::hasColumn('anticipos', 'tipo')) {
                $t->string('tipo', 12)->default('productor')->after('id')->index();
            }
            if (!Schema::hasColumn('anticipos', 'proveedor_id')) {
                $t->unsignedBigInteger('proveedor_id')->nullable()->after('presupuesto_id');
                $t->foreign('proveedor_id')->references('id')->on('proveedores')->onUpdate('cascade')->onDelete('restrict');
            }
            if (!Schema::hasColumn('anticipos', 'valor_base')) {
                $t->decimal('valor_base', 15, 2)->nullable()->after('porcentaje_anticipo');
            }
            if (!Schema::hasColumn('anticipos', 'cod_orden_anticipo')) {
                $t->string('cod_orden_anticipo', 20)->nullable()->unique()->after('total_anticipo');
                $t->string('archivo_orden_anticipo')->nullable()->after('cod_orden_anticipo');
                $t->dateTime('fecha_orden_anticipo')->nullable()->after('archivo_orden_anticipo');
            }
            if (!Schema::hasColumn('anticipos', 'observaciones_contabilidad')) {
                $t->text('observaciones_contabilidad')->nullable()->after('observacion_causal');
            }
        });

        if (!Schema::hasTable('anticipo_pasos')) {
            Schema::create('anticipo_pasos', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('anticipo_id');
                $t->string('accion', 30);
                $t->unsignedBigInteger('estado_desde')->nullable();
                $t->unsignedBigInteger('estado_hasta');
                $t->unsignedBigInteger('user_id')->nullable();
                $t->unsignedSmallInteger('rol')->nullable();
                $t->text('observacion')->nullable();
                $t->timestamp('created_at')->nullable();
                $t->foreign('anticipo_id')->references('id')->on('anticipos')->onDelete('cascade');
                $t->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $t->index(['anticipo_id', 'created_at']);
            });
        }

        foreach ([15 => 'Revisión controller', 16 => 'Rechazo controller'] as $id => $desc) {
            if (!DB::table('estados_anticipo')->where('id', $id)->exists()) {
                DB::table('estados_anticipo')->insert(['id' => $id, 'description' => $desc, 'flujo' => 'juridico', 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        DB::table('estados_anticipo')->whereIn('id', [8, 9, 11, 12, 13])->update(['flujo' => 'ambos']);
        DB::table('estados_anticipo')->where('id', 2)->update(['description' => 'Revisión (flujo anterior, ya no se usa)']);

        // Tipo explícito; al A.J. existente se le copian proveedor, presupuesto y valor base de su orden
        DB::table('anticipos')->whereNotNull('oc_id')->update(['tipo' => 'juridico']);
        DB::statement("UPDATE anticipos a
            JOIN ordenes_compra o ON o.id = a.oc_id
            LEFT JOIN (SELECT oc_id, SUM(vtotal_oc) total FROM oc_items GROUP BY oc_id) i ON i.oc_id = o.id
            SET a.proveedor_id = COALESCE(a.proveedor_id, o.proveedor_id),
                a.presupuesto_id = COALESCE(a.presupuesto_id, o.presupuesto_id),
                a.valor_base = COALESCE(a.valor_base, i.total)
            WHERE a.tipo = 'juridico'");

        // Un A.J. con comprobante de pago ya está pagado aunque siga en "Causado – pendiente de pago" (5):
        // se pagó antes de que existiera el estado 14
        DB::table('anticipos')->where('tipo', 'juridico')->where('estado_id', 5)->whereNotNull('comprobante_pago')->update(['estado_id' => 14]);

        // Los A.J. existentes estrenan su recorrido con la solicitud (con el flujo anterior pasaba a "Revisión", 2)
        DB::statement("INSERT INTO anticipo_pasos (anticipo_id, accion, estado_desde, estado_hasta, user_id, rol, observacion, created_at)
            SELECT a.id, 'solicitar', NULL, 2, a.productor_id, 7, NULL, COALESCE(a.fecha_solicitud, a.created_at)
            FROM anticipos a
            WHERE a.tipo = 'juridico' AND NOT EXISTS (SELECT 1 FROM anticipo_pasos p WHERE p.anticipo_id = a.id)");

        // Los A.J. del paso único anterior entran al flujo nuevo por la revisión del líder
        DB::table('anticipos')->where('tipo', 'juridico')->where('estado_id', 2)->update(['estado_id' => 8]);

        DB::table('permisos')->where('clave', 'revisar-anticipos-gerencia')
            ->update(['descripcion' => 'Revisión de gerencia sobre anticipos (de productor y jurídicos por encima del umbral)']);

        Schema::table('evidencias_anticipo', function (Blueprint $t) {
            $t->unsignedBigInteger('item_id')->nullable()->change();
        });
    }

    public function down()
    {
        DB::table('anticipos')->where('tipo', 'juridico')->whereIn('estado_id', [8, 11, 15, 16])->whereNotNull('oc_id')->update(['estado_id' => 2]);
        // Antes el A.J. no guardaba presupuesto (lo tomaba de la orden)
        DB::table('anticipos')->where('tipo', 'juridico')->whereNotNull('oc_id')->update(['presupuesto_id' => null]);
        Schema::dropIfExists('anticipo_pasos');
        DB::table('estados_anticipo')->where('id', 2)->update(['description' => 'Revisión (anticipo jurídico)']);
        DB::table('permisos')->where('clave', 'revisar-anticipos-gerencia')->update(['descripcion' => 'Revisión de gerencia sobre anticipos de productor']);
        DB::table('estados_anticipo')->whereIn('id', [8, 9, 11, 12, 13])->update(['flujo' => 'productor']);
        if (!DB::table('anticipos')->whereIn('estado_id', [15, 16])->exists()) {
            DB::table('estados_anticipo')->whereIn('id', [15, 16])->delete();
        }
        Schema::table('anticipos', function (Blueprint $t) {
            $t->dropForeign(['proveedor_id']);
            $t->dropUnique(['cod_orden_anticipo']);
            $t->dropColumn(['tipo', 'proveedor_id', 'valor_base', 'cod_orden_anticipo', 'archivo_orden_anticipo',
                            'fecha_orden_anticipo', 'observaciones_contabilidad']);
        });
        // evidencias_anticipo.item_id se deja opcional: volverlo obligatorio fallaría con soportes sin ítem
    }
}
