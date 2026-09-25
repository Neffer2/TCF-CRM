<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\TieneHistorialEstados;
use Illuminate\Database\Eloquent\Model;

class Anticipo extends Model
{
    use HasFactory, TieneHistorialEstados;

    protected $table = 'anticipos';

    protected $fillable = [
        'tipo',
        'oc_id',
        'presupuesto_id',
        'proveedor_id',
        'porcentaje_anticipo',
        'valor_base',
        'total_anticipo',
        'fecha_solicitud',
        'fecha_aprobacion',
        'justificacion_rechazo',
        'estado_id',
        'productor_id',
        'firma_productor'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_orden_anticipo' => 'datetime',
    ];

    const TIPO_JURIDICO = 'juridico';
    const TIPO_PRODUCTOR = 'productor';

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

    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /** Historial de decisiones del flujo (quién, cuándo, de qué estado a cuál, con qué observación). */
    public function pasos(){
        return $this->hasMany(AnticipoPaso::class, 'anticipo_id')->orderBy('created_at')->orderBy('id');
    }

    public function evidencias(){
        return $this->hasMany(EvidenciaAnticipo::class, 'anticipo_id');
    }

    // El tipo es explícito (columna tipo): un anticipo jurídico borrador no tiene orden de compra
    // pero sí presupuesto, así que ya no se puede deducir de oc_id ni de presupuesto_id.
    public function esJuridico(): bool
    {
        return $this->tipo === self::TIPO_JURIDICO;
    }

    public function esDeProductor(): bool
    {
        return !$this->esJuridico();
    }
}
