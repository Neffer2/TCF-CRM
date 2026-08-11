<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cliente_parametros_cc extends Model
{
    use HasFactory;
    protected $table = 'cliente_parametros_cc';

    protected $fillable = [
        'cliente_id',
        'nombre_empresa',
        'codigo_cc',
    ];
}
