<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class OcItem extends Model
{
    use HasFactory;
    protected $table = "oc_items";

    public function itemPresupuesto(){ 
        return $this->hasOne(ItemPresupuesto::class, 'id', 'item_id');
    }
} 
