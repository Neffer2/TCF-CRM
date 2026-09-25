<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Ayudas para migraciones que deben correr igual en MySQL (local) y MariaDB (producción):
 * preguntar por tablas, columnas, índices y llaves, y comentar columnas sin cambiar su definición.
 */
class EsquemaBd
{
    public static function tabla(string $tabla): bool
    {
        return (bool) DB::selectOne("SELECT 1 AS x FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?", [$tabla]);
    }

    public static function columna(string $tabla, string $columna): bool
    {
        return (bool) DB::selectOne("SELECT 1 AS x FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", [$tabla, $columna]);
    }

    public static function indice(string $tabla, string $indice): bool
    {
        return (bool) DB::selectOne("SELECT 1 AS x FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?", [$tabla, $indice]);
    }

    /** Nombre de la llave foránea que sale de la columna, o null si no tiene. */
    public static function fkDeColumna(string $tabla, string $columna): ?string
    {
        $r = DB::selectOne("SELECT constraint_name c FROM information_schema.key_column_usage WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? AND referenced_table_name IS NOT NULL", [$tabla, $columna]);
        return $r ? $r->c : null;
    }

    public static function restriccion(string $tabla, string $nombre): bool
    {
        return (bool) DB::selectOne("SELECT 1 AS x FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = ?", [$tabla, $nombre]);
    }

    public static function tienePk(string $tabla): bool
    {
        return (bool) DB::selectOne("SELECT 1 AS x FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = ? AND constraint_type = 'PRIMARY KEY'", [$tabla]);
    }

    public static function tipoColumna(string $tabla, string $columna): ?string
    {
        $r = DB::selectOne("SELECT column_type t FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", [$tabla, $columna]);
        return $r ? strtolower($r->t) : null;
    }

    public static function comentarTabla(string $tabla, string $comentario): void
    {
        if (!self::tabla($tabla)) { return; }
        DB::statement("ALTER TABLE `{$tabla}` COMMENT = ".DB::getPdo()->quote($comentario));
    }

    /**
     * Pone (o reemplaza) el comentario de una columna repitiendo su definición actual, para que no cambie
     * tipo, nulidad, default ni auto_increment. Las columnas generadas se dejan como están.
     */
    public static function comentarColumna(string $tabla, string $columna, string $comentario): void
    {
        $def = self::definicionColumna($tabla, $columna);
        if ($def === null) { return; }
        DB::statement("ALTER TABLE `{$tabla}` MODIFY COLUMN {$def} COMMENT ".DB::getPdo()->quote($comentario));
    }

    /** Definición SQL de la columna (sin COMMENT), tal como está hoy. null si no existe o es generada. */
    public static function definicionColumna(string $tabla, string $columna): ?string
    {
        $c = DB::selectOne("SELECT COLUMN_TYPE t, IS_NULLABLE n, COLUMN_DEFAULT d, EXTRA e, CHARACTER_SET_NAME cs, COLLATION_NAME co FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", [$tabla, $columna]);
        if (!$c || stripos($c->e, 'GENERATED') !== false && stripos($c->e, 'DEFAULT_GENERATED') === false) { return null; }
        $def = "`{$columna}` {$c->t}";
        if ($c->cs) { $def .= " CHARACTER SET {$c->cs} COLLATE {$c->co}"; }
        $def .= $c->n === 'YES' ? ' NULL' : ' NOT NULL';
        $d = $c->d;
        // MariaDB devuelve el default como expresión: 'texto' entre comillas y NULL como la palabra NULL.
        if ($d === 'NULL') { $d = null; }
        elseif (is_string($d) && strlen($d) >= 2 && $d[0] === "'" && substr($d, -1) === "'") { $d = str_replace("''", "'", substr($d, 1, -1)); }
        $auto = stripos($c->e, 'auto_increment') !== false;
        if (!$auto) {
            if ($d !== null) {
                $def .= preg_match('/^current_timestamp/i', $d) ? ' DEFAULT CURRENT_TIMESTAMP' : ' DEFAULT '.DB::getPdo()->quote($d);
            } elseif ($c->n === 'YES') {
                $def .= ' DEFAULT NULL';
            }
        } else {
            $def .= ' AUTO_INCREMENT';
        }
        if (preg_match('/on update current_timestamp/i', $c->e)) { $def .= ' ON UPDATE CURRENT_TIMESTAMP'; }
        return $def;
    }
}
