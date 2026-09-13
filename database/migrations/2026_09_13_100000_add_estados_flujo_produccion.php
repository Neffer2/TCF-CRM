<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Estados 8–14 del catálogo `estados_ordenes_compra`.
 *
 * El código de nómina (Productor\Ordenes\Nomina) y de anticipos de productor
 * (Productor\Ordenes\AnticipoProductor, LiderProduccion, Contabilidad,
 * Tesoreria) escribe estos estados, pero el catálogo en producción solo
 * tiene los ids 1–7. Como `ordenes_compra.estado_id` y `anticipos.estado_id`
 * tienen FOREIGN KEY al catálogo, cualquier nómina o anticipo de productor
 * fallaba con violación de FK (por eso en producción no existe ninguno).
 *
 * Inserción idempotente con ids explícitos: si un id ya existe se respeta.
 */
class AddEstadosFlujoProduccion extends Migration
{
    public function up()
    {
        $estados = [
            8  => 'Revisión líder producción',
            9  => 'Revisión gerencia',
            10 => 'Revisión evidencias',
            11 => 'Rechazo revisión líder',
            12 => 'Rechazo revisión gerencia',
            13 => 'Rechazo revisión remisión',
            14 => 'Remisión aprobada controller',
        ];

        foreach ($estados as $id => $descripcion) {
            if (!DB::table('estados_ordenes_compra')->where('id', $id)->exists()) {
                DB::table('estados_ordenes_compra')->insert([
                    'id' => $id,
                    'description' => $descripcion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        // Solo se eliminan si ninguna orden/anticipo los usa.
        foreach ([8, 9, 10, 11, 12, 13, 14] as $id) {
            $enUso = DB::table('ordenes_compra')->where('estado_id', $id)->exists()
                || DB::table('anticipos')->where('estado_id', $id)->exists();
            if (!$enUso) {
                DB::table('estados_ordenes_compra')->where('id', $id)->delete();
            }
        }
    }
}
