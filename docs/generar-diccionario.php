<?php
/**
 * Genera docs/DICCIONARIO-DATOS.md a partir del esquema real de la base
 * (tablas, columnas, tipos, llaves foráneas y los COMMENT del esquema).
 *
 * Uso:  php artisan tinker docs/generar-diccionario.php
 */

use Illuminate\Support\Facades\DB;

$db = DB::getDatabaseName();
$tablas = DB::select("SELECT table_name AS t, table_comment AS c, table_rows AS n FROM information_schema.tables WHERE table_schema = ? AND table_type = 'BASE TABLE' ORDER BY table_name", [$db]);

$fks = collect(DB::select("SELECT table_name t, column_name col, referenced_table_name rt, referenced_column_name rc FROM information_schema.key_column_usage WHERE table_schema = ? AND referenced_table_name IS NOT NULL", [$db]))
    ->groupBy(function ($f) { return $f->t.'.'.$f->col; });

$md  = "# Diccionario de datos — TCF-CRM (BULLCRM)\n\n";
$md .= "Generado automáticamente desde el esquema de la base `{$db}` el ".date('Y-m-d H:i')." con `php artisan tinker docs/generar-diccionario.php`.\n";
$md .= "Los comentarios provienen de los COMMENT del esquema (migración `2026_09_14_140000_documentar_esquema`).\n\n";

$md .= "## Mapa rápido del flujo\n\n";
$md .= "```\ncontactos ─► gestion_comercial (+ gestion_participantes) ─► presupuesto_proyecto ─► items_presupuesto (+ item_presupuesto_proveedor)\n";
$md .= "   └► base_comerciales (venta por comercial)      └► ordenes_compra ─► oc_items ─► anticipos ─► items_anticipo / evidencias_anticipo\n";
$md .= "helisa (facturación y comisiones)                 └► natural_info ─► terceros ─► evidencias\n```\n\n";

$md .= "## Tablas\n\n| Tabla | Filas aprox. | Descripción |\n|---|---:|---|\n";
foreach ($tablas as $t) {
    $md .= "| [`{$t->t}`](#".str_replace('_', '', $t->t).") | ".number_format((int) $t->n)." | ".($t->c ?: '—')." |\n";
}
$md .= "\n";

foreach ($tablas as $t) {
    $md .= "### {$t->t}\n\n";
    if ($t->c) { $md .= "{$t->c}\n\n"; }
    $md .= "| Columna | Tipo | Nulo | Default | Referencia | Descripción |\n|---|---|---|---|---|---|\n";
    $cols = DB::select("SELECT column_name c, column_type ty, is_nullable n, column_default d, column_comment cm, column_key k FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ordinal_position", [$db, $t->t]);
    foreach ($cols as $c) {
        $ref = '';
        if ($fks->has($t->t.'.'.$c->c)) {
            $f = $fks->get($t->t.'.'.$c->c)->first();
            $ref = "→ `{$f->rt}.{$f->rc}`";
        }
        $nombre = $c->k === 'PRI' ? "**{$c->c}** (PK)" : $c->c;
        $default = $c->d === null ? '' : '`'.$c->d.'`';
        $md .= "| {$nombre} | `{$c->ty}` | ".($c->n === 'YES' ? 'sí' : 'no')." | {$default} | {$ref} | ".str_replace('|', '\\|', (string) $c->cm)." |\n";
    }
    $md .= "\n";
}

file_put_contents(base_path('docs/DICCIONARIO-DATOS.md'), $md);
echo "Diccionario generado: docs/DICCIONARIO-DATOS.md (".count($tablas)." tablas)\n";
