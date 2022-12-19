<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Mes extends Model
{
    use HasFactory;

    protected $table = "meses";

    protected $fillable = [
        'description',
        'ano_id',
        'f_inicio',
        'f_fin' 
    ]; 
} 
 