<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * proveedores.estado era texto libre (varchar 255). En producción había 3 valores
 * escritos a mano ("CONFIRMADO", "NO APLICA", "CONFIRMADO - COMUNICADO") y el
 * formulario ofrecía un cuarto ("SE CANCELO ACUERDO"). Cualquier filtro o reporte
 * por estado dependía de que el texto coincidiera letra por letra.
 *
 * Ahora hay un catálogo `estados_proveedor` y `proveedores.estado_id` con llave
 * foránea. El texto viejo se conserva en `estado_legacy` (no se usa en el código).
 */
class EstadosProveedor extends Migration
{
    private $catalogo = [
        ['id' => 1, 'nombre' => 'CONFIRMADO',              'descripcion' => 'Acuerdo confirmado con el proveedor'],
        ['id' => 2, 'nombre' => 'CONFIRMADO - COMUNICADO', 'descripcion' => 'Acuerdo confirmado y comunicado al equipo'],
        ['id' => 3, 'nombre' => 'NO APLICA',               'descripcion' => 'Sin acuerdo de tarifas/anticipo (proveedor ocasional)'],
        ['id' => 4, 'nombre' => 'SE CANCELO ACUERDO',      'descripcion' => 'El acuerdo con el proveedor fue cancelado'],
    ];

    public function up()
    {
        if (!Schema::hasTable('estados_proveedor')) {
            Schema::create('estados_proveedor', function (Blueprint $t) {
                $t->unsignedBigInteger('id')->primary();
                $t->string('nombre', 60)->unique();
                $t->string('descripcion', 200)->nullable();
                $t->boolean('activo')->default(true);
                $t->timestamps();
            });
        }
        foreach ($this->catalogo as $e) {
            if (!DB::table('estados_proveedor')->where('id', $e['id'])->exists()) {
                DB::table('estados_proveedor')->insert($e + ['activo' => true, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        if (!Schema::hasColumn('proveedores', 'estado_id')) {
            Schema::table('proveedores', function (Blueprint $t) {
                $t->unsignedBigInteger('estado_id')->nullable()->after('estado');
            });
        }

        // Cruce del texto viejo (sin importar mayúsculas, espacios ni tildes) con el catálogo
        foreach ($this->catalogo as $e) {
            DB::table('proveedores')->whereNull('estado_id')
                ->whereRaw("UPPER(TRIM(REPLACE(REPLACE(estado,'Ó','O'),'ó','o'))) = ?", [$e['nombre']])
                ->update(['estado_id' => $e['id']]);
        }
        // Lo que no cuadre con nada (vacío o texto raro) queda como "NO APLICA" y se deja constancia en el log
        $sueltos = DB::table('proveedores')->whereNull('estado_id')->select('id', 'estado')->get();
        foreach ($sueltos as $p) {
            logger()->warning("Migración estados_proveedor: proveedor {$p->id} tenía estado '{$p->estado}', se asignó NO APLICA");
        }
        DB::table('proveedores')->whereNull('estado_id')->update(['estado_id' => 3]);

        Schema::table('proveedores', function (Blueprint $t) {
            $t->unsignedBigInteger('estado_id')->nullable(false)->change();
            $t->foreign('estado_id')->references('id')->on('estados_proveedor')->onUpdate('cascade')->onDelete('restrict');
        });
        if (Schema::hasColumn('proveedores', 'estado') && !Schema::hasColumn('proveedores', 'estado_legacy')) {
            Schema::table('proveedores', function (Blueprint $t) { $t->renameColumn('estado', 'estado_legacy'); });
        }
    }

    public function down()
    {
        Schema::table('proveedores', function (Blueprint $t) { $t->dropForeign(['estado_id']); });
        if (Schema::hasColumn('proveedores', 'estado_legacy')) {
            Schema::table('proveedores', function (Blueprint $t) { $t->renameColumn('estado_legacy', 'estado'); });
        }
        // Restaura el texto a partir del catálogo antes de soltar la columna
        foreach ($this->catalogo as $e) {
            DB::table('proveedores')->where('estado_id', $e['id'])->update(['estado' => $e['nombre']]);
        }
        Schema::table('proveedores', function (Blueprint $t) { $t->dropColumn('estado_id'); });
        Schema::dropIfExists('estados_proveedor');
    }
}
