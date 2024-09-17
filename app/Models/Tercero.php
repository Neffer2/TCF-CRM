<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tercero extends Model
{
    use HasFactory;
    protected $table = 'terceros';
    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'correo',
        'telefono',
        'ciudad',
        'banco'
    ];
}
