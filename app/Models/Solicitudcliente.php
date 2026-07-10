<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class solicitudcliente extends Model{
    use HasFactory;
    protected $table = 'solicitudcliente';

    protected $fillable = [
        'id_user',
        'tipo_cliente',
        'nombre_cliente',
        'apellido_cliente',
        'direccion_cliente',
        'telefono_cliente',
        'email_cliente',
        'descripcion_cliente',
        'estado',
        'nueva_empresa_datos',
    ];

    protected $casts = [
        'nueva_empresa_datos' => 'array',
    ];

    public function comercial()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
