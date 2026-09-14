<?php

namespace App\Jobs;

use App\Models\NotificacionLog;
use App\Services\Notificador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Envía una notificación registrada (correo o SMS). Con QUEUE_CONNECTION=sync
 * corre en el mismo request; con una cola real (database/redis + worker)
 * el usuario no espera al SMTP. Los reintentos los maneja Notificador.
 */
class EnviarNotificacion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 120;

    public function __construct(public int $logId) {}

    public function handle()
    {
        $log = NotificacionLog::find($this->logId);
        if ($log && $log->estado !== 'enviado') {
            Notificador::procesar($log);
        }
    }
}
