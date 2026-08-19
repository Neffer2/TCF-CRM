<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudCliente extends Model
{
    use HasFactory;
    protected $table = 'solicitud_contactos';

    protected $fillable = [
        'id_user',
        'estado',
        'nombre',
        'razon_social',
        'nit',
        'direccion',
        'telefono',
        'numero_telefono',
        'cargo',
        'correo',
        'pagina_web',
        'correo_recpcion_facturas',
        'adjuntar_archivos',
    ];

    public function comercial()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
