<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientes extends Model
{
    use HasFactory;
    protected $table = 'clientes';

    protected $fillable = [
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
        'adjuntar_archivos'
    ];
    public function parametroCC()
    {
        return $this->hasOne(cliente_parametros_cc::class, 'cliente_id');

    }
}
