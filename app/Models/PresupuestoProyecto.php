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
}
