<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anticipo extends Model
{
    use HasFactory;

    protected $table = 'anticipos';

    protected $fillable = [
        'oc_id',
        'presupuesto_id',
        'porcentaje_anticipo',
        'total_anticipo',
        'fecha_solicitud',
        'fecha_aprobacion',
        'justificacion_rechazo',
        'estado_id',
        'productor_id',
        'firma_productor'
    ];

    // Catálogo propio de anticipos (estados_anticipo), ya no el de órdenes de compra
    public function estado(){
        return $this->belongsTo(EstadoAnticipo::class, 'estado_id');
    }

    public function ordenCompra(){
        return $this->belongsTo(OrdenCompra::class, 'oc_id');
    }

    public function presupuesto() {
        return $this->belongsTo(PresupuestoProyecto::class, 'presupuesto_id', 'id');
    }

    public function anticipoItems() {
        return $this->hasMany(ItemAnticipo::class, 'anticipo_id', 'id');
    }

    public function productor_info(){
        return $this->hasOne(User::class, 'id', 'productor_id');
    }

    // true si es un anticipo de productor (sobre presupuesto); false si es jurídico (sobre orden)
    public function esDeProductor(): bool
    {
        return !is_null($this->presupuesto_id);
    }
}
