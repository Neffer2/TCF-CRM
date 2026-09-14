<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * REESTRUCTURACIÓN 4/5 — Integridad referencial, índices y limpieza.
 *
 * - Elimina los ítems de presupuesto huérfanos (su presupuesto ya no
 *   existe) junto con sus dependencias, y protege la relación con FK.
 * - Agrega las FKs que faltaban: clientes.estado_id y
 *   presupuesto_proyecto.comercial_id.
 * - Índices para las consultas más frecuentes de dashboards y listados.
 * - Corrige los contactos con PBX y sitio web intercambiados (huella del
 *   bug de edición de contactos, ya corregido en código).
 * NOTA: los asientos Helisa repetidos por (centro, documento, comercial)
 * son legítimos (notas crédito/ajustes), por eso llevan índice y NO clave única.
 */
class IntegridadIndicesYLimpieza extends Migration
{
    public function up()
    {
        // --- Ítems de presupuesto huérfanos ---
        $huerfanos = DB::table('items_presupuesto as i')
            ->leftJoin('presupuesto_proyecto as p', 'p.id', '=', 'i.presupuesto_id')
            ->whereNull('p.id')->pluck('i.id');
        if ($huerfanos->isNotEmpty()) {
            $ocItems = DB::table('oc_items')->whereIn('item_id', $huerfanos)->delete();
            $antItems = Schema::hasTable('items_anticipo') ? DB::table('items_anticipo')->whereIn('item_id', $huerfanos)->delete() : 0;
            $hist = Schema::hasTable('historial_items_presupuesto') ? DB::table('historial_items_presupuesto')->whereIn('item_presupuesto_id', $huerfanos)->delete() : 0;
            DB::table('items_presupuesto')->whereIn('id', $huerfanos)->delete();
            echo "  ítems huérfanos eliminados: {$huerfanos->count()} (líneas de OC: {$ocItems}, de anticipo: {$antItems}, historial: {$hist})\n";
        }
        $this->fkSiNoExiste('items_presupuesto', 'presupuesto_id', 'presupuesto_proyecto');
        $this->fkSiNoExiste('clientes', 'estado_id', 'estados_cuenta');
        $this->fkSiNoExiste('presupuesto_proyecto', 'comercial_id', 'users');

        // --- Índices ---
        $this->indiceSiNoExiste('helisa', ['centro', 'num_doc'], 'helisa_centro_num_doc_idx');
        $this->indiceSiNoExiste('helisa', ['comercial', 'fecha'], 'helisa_comercial_fecha_idx');
        $this->indiceSiNoExiste('items_presupuesto', ['presupuesto_id', 'disponible'], 'items_presupuesto_presto_disponible_idx');
        $this->indiceSiNoExiste('gestion_comercial', ['id_user', 'id_estado'], 'gestion_comercial_user_estado_idx');
        $this->indiceSiNoExiste('base_comerciales', ['id_user', 'fecha'], 'base_comerciales_user_fecha_idx');
        $this->indiceSiNoExiste('ordenes_compra', ['presupuesto_id', 'estado_id'], 'ordenes_compra_presto_estado_idx');

        // --- Contactos con PBX y web intercambiados ---
        $cruzados = DB::table('contactos')
            ->where(function ($q) { $q->where('pbx', 'LIKE', '%www.%')->orWhere('pbx', 'LIKE', '%http%')->orWhere('pbx', 'LIKE', '%.com%'); })
            ->whereRaw("web REGEXP '^[0-9 +()-]+$'")
            ->get(['id', 'pbx', 'web']);
        foreach ($cruzados as $c) {
            DB::table('contactos')->where('id', $c->id)->update(['pbx' => $c->web, 'web' => $c->pbx]);
        }
        echo "  contactos con pbx/web intercambiados corregidos: {$cruzados->count()}\n";
    }

    private function fkSiNoExiste(string $tabla, string $columna, string $referencia): void
    {
        $existe = DB::selectOne("SELECT 1 AS x FROM information_schema.key_column_usage WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? AND referenced_table_name IS NOT NULL", [$tabla, $columna]);
        if ($existe) { return; }

        // Una FK exige el mismo tipo en ambos lados. En esquemas creados a mano
        // la columna referenciadora suele ser INT (con signo) y la PK BIGINT UNSIGNED:
        // se alinea el tipo conservando la nulabilidad.
        $ref = DB::selectOne("SELECT column_type AS t FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = 'id'", [$referencia]);
        $col = DB::selectOne("SELECT column_type AS t, is_nullable AS n FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", [$tabla, $columna]);
        if ($ref && $col && strcasecmp($ref->t, $col->t) !== 0) {
            $nulo = $col->n === 'YES' ? 'NULL' : 'NOT NULL';
            DB::statement("ALTER TABLE `{$tabla}` MODIFY COLUMN `{$columna}` {$ref->t} {$nulo}");
        }

        Schema::table($tabla, function (Blueprint $table) use ($columna, $referencia) {
            $table->foreign($columna)->references('id')->on($referencia);
        });
    }

    private function indiceSiNoExiste(string $tabla, array $columnas, string $nombre): void
    {
        $existe = DB::selectOne("SELECT 1 AS x FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?", [$tabla, $nombre]);
        if ($existe) { return; }
        Schema::table($tabla, function (Blueprint $table) use ($columnas, $nombre) {
            $table->index($columnas, $nombre);
        });
    }

    public function down()
    {
        // Las FKs e índices se conservan; la limpieza de datos no es reversible.
    }
}
