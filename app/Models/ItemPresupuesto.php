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

    public function ordenes_compra(){
        return $this->hasMany(OcItem::class, 'item_id', 'id');  
    }
}
