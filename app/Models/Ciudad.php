<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de ciudades (tabla ciudades). Reemplaza la lista fija que traía el código y unifica el texto
 * libre de ítems, proveedores, terceros y contactos: cada texto se normaliza a una clave y se enlaza por ciudad_id.
 */
class Ciudad extends Model
{
    protected $table = 'ciudades';
    protected $fillable = ['nombre', 'clave', 'orden', 'activa'];
    protected $casts = ['activa' => 'boolean'];

    /** Clave de comparación: mayúsculas, sin tildes, sin puntos ni comas, un solo espacio. "Bogotá, D.C." → "BOGOTA DC". */
    public static function clave(?string $texto): string
    {
        $s = self::limpiar($texto);
        $s = strtr($s, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N', 'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U']);
        $s = preg_replace('/[^A-Z0-9 ]+/', '', $s);
        return trim(preg_replace('/\s+/', ' ', $s));
    }

    /** Nombre para mostrar: UTF-8 válido (los datos viejos traen bytes latin1), mayúsculas, sin espacios dobles. */
    public static function limpiar(?string $texto): string
    {
        $s = (string) $texto;
        if (!mb_check_encoding($s, 'UTF-8')) { $s = mb_convert_encoding($s, 'UTF-8', 'ISO-8859-1'); }
        return trim(preg_replace('/\s+/', ' ', mb_strtoupper($s, 'UTF-8')));
    }

    /** Ciudad que corresponde a un texto; si no existe se crea inactiva (no sale en desplegables) para no perder el dato. */
    public static function desdeTexto(?string $texto): ?self
    {
        $clave = self::clave($texto);
        if ($clave === '' || $clave === '0') { return null; }
        return self::firstOrCreate(['clave' => $clave], ['nombre' => self::limpiar($texto), 'orden' => 999, 'activa' => false]);
    }

    /** Nombres para los desplegables, en el orden oficial. */
    public static function listaActiva(): array
    {
        return self::where('activa', true)->orderBy('orden')->orderBy('nombre')->pluck('nombre')->all();
    }
}
