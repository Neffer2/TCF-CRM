<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Destinatarios de notificación por área (reemplaza el directorio de
 * correos quemado en app/Traits/Email.php). Una fila apunta a un usuario
 * de la plataforma (user_id) o a un buzón externo (email_externo).
 */
class NotificacionDestinatario extends Model
{
    protected $table = 'notificacion_destinatarios';
    protected $fillable = ['area', 'user_id', 'email_externo', 'nombre', 'activo'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Destinatarios activos de un área en el formato de sendMail.
     * Las filas por user_id excluyen automáticamente a los suspendidos.
     */
    public static function area(string $area): array
    {
        return static::where('area', $area)
            ->where('activo', true)
            ->with('usuario')
            ->get()
            ->map(function ($d) {
                if ($d->user_id) {
                    if (!$d->usuario || $d->usuario->rol == 4) {
                        return null; // usuario suspendido o eliminado
                    }
                    return ['name' => $d->usuario->name, 'email' => $d->usuario->email];
                }
                return $d->email_externo ? ['name' => $d->nombre ?? '', 'email' => $d->email_externo] : null;
            })
            ->filter()
            ->values()
            ->all();
    }
}
