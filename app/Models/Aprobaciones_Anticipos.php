<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aprobaciones_Anticipos extends Model{
    use HasFactory;
    protected $table = 'aprobaciones_anticipos';

    protected $fillable = [
        'anticipo_id',
        'etapa',
        'orden',
        'usuario_decicion_id',
        'feca_decision',
        'obervacion'
    ];

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_decicion_id', 'id');
    }

    public function anticipo(){
        return $this->belongsTo(User::class, 'usuario_decicion_id', 'id');
    }
}
