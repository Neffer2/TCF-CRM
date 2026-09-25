<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Base_comercial extends Model
{
    use HasFactory, SoftDeletes;

    /** Al guardar cod_cc se llena centro_costos (código puro, el que usa Helisa). */
    public function setCodCcAttribute($valor)
    {
        $this->attributes['cod_cc'] = $valor;
        $this->attributes['centro_costos'] = PresupuestoProyecto::codigoPuro($valor);
    }
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
        'id_gestion',  
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

    /* Gestion comercial */
    public function gestion (){
        return $this->hasOne(GestionComercial::class, 'id', 'id_gestion');
    }
}
  