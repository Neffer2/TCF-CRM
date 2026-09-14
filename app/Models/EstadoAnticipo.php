<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Catálogo de estados del ciclo de vida de un anticipo (tabla estados_anticipo). */
class EstadoAnticipo extends Model
{
    protected $table = 'estados_anticipo';
    public $incrementing = false;
    protected $fillable = ['id', 'description', 'flujo'];

    const APROBADO = 1;              // pendiente de causar (contabilidad)
    const REVISION_JURIDICO = 2;
    const CAUSADO = 5;               // pendiente de pago (tesorería)
    const EVIDENCIAS = 7;
    const REVISION_LIDER = 8;
    const REVISION_GERENCIA = 9;
    const REVISION_EVIDENCIAS = 10;
    const RECHAZO_LIDER = 11;
    const RECHAZO_GERENCIA = 12;
    const RECHAZO_CONTABILIDAD = 13;
}
