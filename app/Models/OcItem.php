<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class OcItem extends Model
{
    use HasFactory;
    protected $table = "oc_items";
    protected $fillable = ['oc_id', 'item_id', 'display_item', 'desc_oc', 'cant_oc', 'dias_oc', 'otros_oc', 'vunit_oc', 'vtotal_oc', 'tipo_servicio', 'tipo_contrato', 'cantidad_horas',];

    public function itemPresupuesto(){ 
        return $this->hasOne(ItemPresupuesto::class, 'id', 'item_id');
    }

    public function OrdenCompra(){
        return $this->hasOne(OrdenCompra::class, 'id', 'oc_id');
    }
} 
 