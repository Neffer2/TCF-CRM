<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Catálogo de estados del acuerdo con un proveedor (tabla estados_proveedor). */
class EstadoProveedor extends Model
{
    protected $table = 'estados_proveedor';
    protected $fillable = ['id', 'nombre', 'descripcion', 'activo'];

    const CONFIRMADO = 1;
    const CONFIRMADO_COMUNICADO = 2;
    const NO_APLICA = 3;
    const ACUERDO_CANCELADO = 4;

    public function scopeActivos($q) { return $q->where('activo', true)->orderBy('id'); }

    public function proveedores() { return $this->hasMany(Proveedor::class, 'estado_id'); }
}
