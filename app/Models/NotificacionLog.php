<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Una fila por cada correo o SMS que el CRM intenta enviar, con su resultado.
 */
class NotificacionLog extends Model
{
    protected $table = 'notificaciones_log';

    protected $fillable = [
        'canal', 'evento', 'referencia', 'destinatarios', 'copias', 'asunto', 'cuerpo', 'adjuntos',
        'estado', 'intentos', 'error', 'enviado_at', 'alertado_at',
    ];

    protected $casts = [
        'destinatarios' => 'array',
        'copias' => 'array',
        'adjuntos' => 'array',
        'enviado_at' => 'datetime',
        'alertado_at' => 'datetime',
    ];

    public function scopeFallidos($q) { return $q->where('estado', 'fallido'); }
    public function scopeUltimas24h($q) { return $q->where('created_at', '>=', now()->subDay()); }
}
