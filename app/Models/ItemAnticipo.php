<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAnticipo extends Model
{
    use HasFactory;
    protected $table = 'items_anticipo';
    protected $fillable = ['anticipo_id', 'item_id', 'display_item', 'desc', 'cant', 'dias', 'otros', 'vunit', 'vtotal', 'vanticipo', 'saldo'];

    public function itemPresupuesto() {
        return $this->hasOne(ItemPresupuesto::class, 'id', 'item_id');
    }

    public function anticipo() {
        return $this->belongsTo(Anticipo::class, 'anticipo_id');
    }
}
