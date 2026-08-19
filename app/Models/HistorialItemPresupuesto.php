<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialItemPresupuesto extends Model
{
    protected $table = 'historial_items_presupuesto';

    protected $fillable = [
        'item_presupuesto_id',
        'valores_anteriores',
        'user_id',
    ];

    protected $casts = [
        'valores_anteriores' => 'array'
    ];

    public function itemPresupuesto()
    {
        return $this->belongsTo(ItemPresupuesto::class);
    }

}
