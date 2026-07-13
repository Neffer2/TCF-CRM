<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientes extends Model
{
    use HasFactory;
    protected $table = 'clientes';

    protected $fillable = [
        'CodigoCliente',
        'TipoCliente',
        'NombreCliente',
        'ApellidoCliente',
        'RazonCliente',
        'DireccionCliente',
        'TelefonoCliente',
        'EmailCliente',
        'DescripcionCliente',
        'id_user',
        'estado_id'
    ];
}
