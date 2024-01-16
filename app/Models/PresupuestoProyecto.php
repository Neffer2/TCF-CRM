<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresupuestoProyecto extends Model
{ 
    use HasFactory;
    protected $table = "presupuesto_proyecto";
 
    public function gestion(){ 
        return $this->hasOne(GestionComercial::class, 'id', 'id_gestion'); 
    } 

    public function baseComercial(){ 
        return $this->hasMany(Base_comercial::class, 'id_gestion', 'id_gestion'); 
    } 
   
    public function estado(){
        return $this->hasOne(EstadosPresupuesto::class, 'id', 'estado_id');
    }
 
    public function presupuestoItems(){
        return $this->hasMany(ItemPresupuesto::class, 'presupuesto_id', 'id');
    } 

    public function ordenesCompra(){
        return $this->hasMany(OrdenCompra::class, 'presupuesto_id', 'id'); 
    }

    public function productor_info(){
        return $this->hasOne(User::class, 'id', 'productor');
    }
} 
