<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenciaAnticipo extends Model
{
    use HasFactory;

    protected $table = 'evidencias_anticipo';

    protected $fillable = [
        'anticipo_id',
        'item_id',
        'fecha_evidencia',
        'foto_evidencia',
        'observacion_evidencia',
    ];

    public function itemPresupuesto() {
        return $this->belongsTo(ItemPresupuesto::class, 'item_id');
    }
}
