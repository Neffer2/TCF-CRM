<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPresupuesto extends Model
{
    use HasFactory; 
    protected $table = "items_presupuesto";

    public function mesDescription (){
        return $this->hasOne(Mes::class, 'id', 'mes');  
    }  

    public function consumidos(){
        return $this->hasMany(OcItem::class, 'item_id', 'id');  
    }

    public function presto(){
        return $this->hasOne(PresupuestoProyecto::class, 'id', 'presupuesto_id');  
    }

    // Proveedor no tiene relacion en base de datos porque se implementaron después de creado el modulo presupuestos.
    public function proveedorInfo(){
        return $this->hasOne(Proveedor::class, 'id', 'proveedor');
    }
}
