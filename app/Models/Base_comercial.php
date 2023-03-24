<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Base_comercial extends Model
{
    use HasFactory;
    protected $table = 'base_comerciales';

    protected $fillable = [
        'fecha',
        'nom_cliente',
        'nom_proyecto', 
        'cod_cc',
        'valor_proyecto',
        'com_1',
        'com_2',
        'com_3',
        'id_estado',
        'id_cuenta',
        'fecha_inicio',
        'dura_mes', 
        'id_user',
        'id_asistente', 
    ];

    public function comercial (){
        return $this->hasOne(User::class, 'id', 'id_user');
    }

    public function asistente (){
        return $this->hasOne(User::class, 'id', 'id_asistente');
    } 

    public function estado_cuenta (){
        return $this->hasOne(EstadoCuenta::class, 'id', 'id_estado');
    }

    /* Cuenta (Bull o V2V) */
    public function cuenta (){
        return $this->hasOne(Cuenta::class, 'id', 'id_cuenta');
    }
}
  