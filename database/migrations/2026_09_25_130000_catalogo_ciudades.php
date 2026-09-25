<?php

use App\Models\Ciudad;
use App\Support\EsquemaBd;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MEJORAS DE BASE DE DATOS 4/7 — Catálogo de ciudades.
 *
 * La ciudad era texto libre en ítems del presupuesto, proveedores, terceros y contactos (98 escrituras distintas
 * para unas 40 ciudades: "BOGOTA D.C", "BOGOTÁ D.C.", "BOGOTA DC"...). Se crea la tabla ciudades con la lista
 * oficial que traía el código (activas, en su orden) más las variantes que hay en los datos (inactivas: no salen
 * en los desplegables pero enlazan las filas viejas), y cada tabla recibe ciudad_id con llave al catálogo.
 * La columna de texto se conserva; los modelos mantienen ciudad_id al día al guardar.
 */
class CatalogoCiudades extends Migration
{
    const TABLAS = ['items_presupuesto', 'proveedores', 'terceros', 'contactos'];

    public function up()
    {
        if (!EsquemaBd::tabla('ciudades')) {
            Schema::create('ciudades', function (Blueprint $t) {
                $t->id();
                $t->string('nombre', 120)->comment('Nombre para mostrar (mayúsculas, como en los formularios)');
                $t->string('clave', 120)->unique()->comment('Nombre normalizado (sin tildes, puntos ni espacios dobles): une variantes como BOGOTA D.C, BOGOTÁ D.C. y BOGOTA DC');
                $t->unsignedSmallInteger('orden')->default(999)->comment('Posición en los desplegables (la lista oficial primero)');
                $t->boolean('activa')->default(true)->comment('1 = se ofrece en los desplegables; 0 = variante vieja de los datos, solo para enlazar filas antiguas');
                $t->timestamps();
            });
            DB::statement("ALTER TABLE `ciudades` COMMENT = 'Catálogo de ciudades: reemplaza la lista fija del código y unifica el texto libre de ítems, proveedores, terceros y contactos'");
        }

        $orden = 0;
        foreach ((array) config('ciudades', []) as $nombre) { $this->asegurar($nombre, $orden++, true); }

        foreach (self::TABLAS as $t) {
            if (!EsquemaBd::columna($t, 'ciudad')) { continue; }
            if (!EsquemaBd::columna($t, 'ciudad_id')) {
                DB::statement("ALTER TABLE `{$t}` ADD COLUMN `ciudad_id` BIGINT UNSIGNED NULL COMMENT 'Ciudad del catálogo (ciudades.id) que corresponde al texto de la columna ciudad' AFTER `ciudad`");
            }
            $valores = DB::table($t)->select('ciudad')->distinct()->whereNotNull('ciudad')->where('ciudad', '<>', '')->pluck('ciudad');
            foreach ($valores as $v) {
                $id = $this->asegurar($v, 999, false);
                if ($id) { DB::table($t)->where('ciudad', $v)->whereNull('ciudad_id')->update(['ciudad_id' => $id]); }
            }
            if (!EsquemaBd::fkDeColumna($t, 'ciudad_id')) {
                DB::statement("ALTER TABLE `{$t}` ADD CONSTRAINT `{$t}_ciudad_id_foreign` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades`(`id`) ON DELETE SET NULL");
            }
        }
    }

    /** Devuelve el id de la ciudad con esa clave, creándola si no existe. La lista oficial manda nombre, orden y activa. */
    private function asegurar(string $nombre, int $orden, bool $oficial): ?int
    {
        $clave = Ciudad::clave($nombre);
        if ($clave === '' || $clave === '0') { return null; }
        $existente = DB::table('ciudades')->where('clave', $clave)->first();
        if ($existente) {
            if ($oficial) { DB::table('ciudades')->where('id', $existente->id)->update(['nombre' => Ciudad::limpiar($nombre), 'orden' => $orden, 'activa' => 1]); }
            return (int) $existente->id;
        }
        return (int) DB::table('ciudades')->insertGetId([
            'nombre' => Ciudad::limpiar($nombre), 'clave' => $clave, 'orden' => $orden, 'activa' => $oficial ? 1 : 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down()
    {
        foreach (self::TABLAS as $t) {
            if ($fk = EsquemaBd::fkDeColumna($t, 'ciudad_id')) { DB::statement("ALTER TABLE `{$t}` DROP FOREIGN KEY `{$fk}`"); }
            if (EsquemaBd::columna($t, 'ciudad_id')) { DB::statement("ALTER TABLE `{$t}` DROP COLUMN `ciudad_id`"); }
        }
        Schema::dropIfExists('ciudades');
    }
}
