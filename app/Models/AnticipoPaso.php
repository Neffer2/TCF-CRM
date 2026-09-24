<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Una decisión dentro del flujo de un anticipo: quién, cuándo, de qué estado a cuál y por qué. */
class AnticipoPaso extends Model
{
    protected $table = 'anticipo_pasos';
    public $timestamps = false;
    protected $fillable = ['anticipo_id', 'accion', 'estado_desde', 'estado_hasta', 'user_id', 'rol', 'observacion', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
    public function anticipo() { return $this->belongsTo(Anticipo::class, 'anticipo_id'); }
}
