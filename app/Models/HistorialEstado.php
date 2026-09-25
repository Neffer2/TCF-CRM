<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Una fila por cambio de estado de órdenes, anticipos, presupuestos, gestiones y terceros (tabla historial_estados). */
class HistorialEstado extends Model
{
    protected $table = 'historial_estados';
    public $timestamps = false;
    protected $fillable = ['tabla', 'registro_id', 'estado_anterior', 'estado_nuevo', 'user_id', 'rol', 'observacion', 'origen', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Nombre del estado nuevo según la tabla del registro. */
    public function nombreEstado($id): string
    {
        $catalogo = [
            'ordenes_compra' => 'estados_ordenes_compra', 'anticipos' => 'estados_anticipo', 'presupuesto_proyecto' => 'estados_presupuesto',
            'gestion_comercial' => 'estados_gestion_comercial', 'terceros' => 'estados_tercero',
        ][$this->tabla] ?? null;
        if (!$catalogo || $id === null) { return ''; }
        static $cache = [];
        if (!isset($cache[$catalogo])) {
            $cache[$catalogo] = \DB::table($catalogo)->pluck(\Schema::hasColumn($catalogo, 'description') ? 'description' : 'descripcion', 'id')->all();
        }
        return $cache[$catalogo][$id] ?? (string) $id;
    }
}
