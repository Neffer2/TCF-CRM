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
        'fecha_inicio',
        'dura_mes', 
        'id_user' 
    ];

    public function comercial (){
        return $this->hasOne(User::class, 'id', 'id_user');
    } 

    // public function user_rol(){
    //     return $this->hasOne(Rol::class, 'id', 'rol');
    // } 

    public function estado_cuenta (){
        return $this->hasOne(EstadoCuenta::class, 'id', 'id_estado');
    }
}
  