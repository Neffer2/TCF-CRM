<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    use HasFactory;
    protected $table = "evidencias";

    protected $fillable = [
        'fecha_evidencia',
        'foto_evidencia',
        'observacion_evidencia', 
        'tercero_id'
    ];
}
