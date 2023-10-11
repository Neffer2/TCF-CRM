<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class GestionComercial extends Model 
{
    use HasFactory; 
    protected $table = 'gestion_comercial';  
 
    public function contacto (){ 
        return $this->hasOne(Contacto::class, 'id', 'id_contacto');
    } 

    public function comercial (){ 
        return $this->hasOne(User::class, 'id', 'id_user');
    }

    public function presupuesto (){
        return $this->hasOne(PresupuestoProyecto::class, 'id_gestion', 'id');
    }  

    public function baseComercial (){
        return $this->hasMany(Base_comercial::class, 'id_gestion', 'id');
    }
}
