<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $table = 'proveedores';

    public function categoria(){
        return $this->hasOne(CategoriaProveedor::class, 'id', 'categoria_id');
    }

    /** Estado del acuerdo (catálogo estados_proveedor). */
    public function estado(){
        return $this->belongsTo(EstadoProveedor::class, 'estado_id');
    }

    /** Nombre del estado, para listas y exports. */
    public function getEstadoNombreAttribute(): string
    {
        return optional($this->estado)->nombre ?? '';
    }
}
