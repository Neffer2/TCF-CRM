<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresupuestoProyecto extends Model
{ 
    use HasFactory;
    protected $table = "presupuesto_proyecto";
 
    public function gestion (){
        return $this->hasOne(GestionComercial::class, 'id', 'id_gestion'); 
    }

    public function estado (){
        return $this->hasOne(EstadosPresupuesto::class, 'id', 'estado_id');
    }

    public function presupuestoItems(){
        return $this->hasMany(ItemPresupuesto::class, 'presupuesto_id', 'id');
    }
} 
