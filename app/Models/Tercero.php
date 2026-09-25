<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ResuelveCiudad;
use App\Models\Concerns\TieneHistorialEstados;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tercero extends Model
{
    use HasFactory, SoftDeletes, ResuelveCiudad, TieneHistorialEstados;
    protected $table = 'terceros';
    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'correo',
        'telefono',
        'servicio',
        'ciudad',
        'banco',
        'estado'
    ];
}
