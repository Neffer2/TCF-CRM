<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anticipo extends Model
{
    public const ESTADO_APROBADO = 8;

    protected $table = 'anticipos';

    protected $fillable = [
        'oc_id',
        'porcentaje_anticipo',
        'total_anticipo',
        'estado_id',
        'justificacion_rechazo',
        'justificacion_productor',
        'fecha_aprobacion',
        'fecha_solicitud',
        'productor_id',
    ];

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'oc_id');
    }

    public function productor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'productor_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estados_Anticipo::class, 'estado_id');
    }

    public function aprobaciones(): HasMany
    {
        return $this->hasMany(Aprobaciones_Anticipos::class, 'anticipo_id')->orderBy('orden', 'asc');
    }
    
    public function itemPresupuesto()
    {
        return $this->belongsTo(ItemPresupuesto::class, 'item_id', 'id');
    }
}
