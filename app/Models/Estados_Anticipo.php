<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Estados_Anticipo extends Model
{
    use HasFactory;
    protected $table = 'estados_anticipos';

    protected $fillable = [
        'codigo',
        'descripction'
    ];

    public function anticipos(){
        return $this->hasMany(Anticipo::class, 'estado_id', 'id');
    }
}