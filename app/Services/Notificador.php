<?php

namespace App\Services;

use App\Jobs\EnviarNotificacion;
use App\Models\NotificacionLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Punto único de salida de correos y SMS del CRM.
 *
 * - Registra cada envío en notificaciones_log (destinatarios, cuerpo, resultado).
 * - Reintenta (config crm.notificaciones.reintentos) con espera creciente.
 * - Si falla definitivamente, alerta a desarrollo por correo y SMS
 *   (config crm.notificaciones.alertas), sin repetir la alerta más de una
 *   vez por canal en el lapso configurado.
 * - Con QUEUE_CONNECTION distinto de sync, el envío sale por cola.
 * - Modo 'log' (MAIL_MAILER=log / SMS_MODO=log): no envía, escribe en el log
 *   y marca como enviado; sirve para probar en local.
 */
class Notificador
{
    /* ------------------------------------------------------------------ */
    /*  Entrada                                                            */
    /* ------------------------------------------------------------------ */

    /**
     * @param array $para   [['email' => ..., 'name' => ...], ...]
     * @param array $copias [['email' => ..., 'name' => ...], ...]
     * @param array $adjuntos [['ruta' => 'public/x.pdf', 'nombre' => 'X.pdf'], ...]
     */
    public static function correo(string $evento, string $asunto, string $html, array $para, array $copias = [], array $adjuntos = [], ?string $referencia = null, ?string $textoPlano = null): bool
    {
        $para = self::limpiarCorreos($para);
        $copias = self::limpiarCorreos($copias);
        $asunto = self::utf8($asunto);
        $html = self::utf8($html);

        $log = NotificacionLog::create([
            'canal' => 'correo', 'evento' => $evento, 'referencia' => $referencia,
            'destinatarios' => $para, 'copias' => $copias, 'asunto' => $asunto,
            'cuerpo' => $html, 'adjuntos' => $adjuntos ?: null,
        ]);

        if (empty($para)) {
            self::marcarFallo($log, 'Sin destinatarios válidos (revisa notificacion_destinatarios o el correo del usuario).');
            return false;
        }
        return self::despachar($log);
    }

    public static function sms(string $evento, ?string $telefono, string $texto, ?string $referencia = null): bool
    {
        $telefono = self::limpiarTelefono($telefono);
        $log = NotificacionLog::create([
            'canal' => 'sms', 'evento' => $evento, 'referencia' => $referencia,
            'destinatarios' => $telefono ? [$telefono] : [], 'cuerpo' => self::utf8($texto),
        ]);
        if (!$telefono) {
            self::marcarFallo($log, 'Teléfono vacío o inválido.');
            return false;
        }
        return self::despachar($log);
    }

    /** Vuelve a intentar un envío desde la pantalla de notificaciones. */
    public static function reintentar(NotificacionLog $log): bool
    {
        $log->update(['estado' => 'pendiente', 'error' => null]);
        return self::procesar($log);
    }

    private static function despachar(NotificacionLog $log): bool
    {
        if (config('queue.default', 'sync') !== 'sync') {
            EnviarNotificacion::dispatch($log->id);
            return true;
        }
        return self::procesar($log);
    }

    /* ------------------------------------------------------------------ */
    /*  Envío con reintentos                                               */
    /* ------------------------------------------------------------------ */

    public static function procesar(NotificacionLog $log): bool
    {
        $max = max(1, (int) config('crm.notificaciones.reintentos', 3));
        $esperas = config('crm.notificaciones.espera_segundos', [2, 5]);
        $ultimoError = null;

        for ($i = 1; $i <= $max; $i++) {
            try {
                $log->canal === 'sms' ? self::enviarSms($log) : self::enviarCorreo($log);
                $log->update(['estado' => 'enviado', 'intentos' => $log->intentos + $i, 'error' => null, 'enviado_at' => now()]);
                return true;
            } catch (\Throwable $e) {
                $ultimoError = $e->getMessage();
                Log::warning("Notificación #{$log->id} ({$log->canal}/{$log->evento}) intento {$i}/{$max}: {$ultimoError}");
                if ($i < $max) { sleep((int) ($esperas[$i - 1] ?? end($esperas) ?: 2)); }
            }
        }
        $log->intentos = $log->intentos + $max;
        self::marcarFallo($log, $ultimoError ?: 'Error desconocido');
        return false;
    }

    private static function marcarFallo(NotificacionLog $log, string $error): void
    {
        $log->update(['estado' => 'fallido', 'error' => mb_substr($error, 0, 2000)]);
        Log::error("Notificación #{$log->id} FALLIDA ({$log->canal}/{$log->evento}): {$error}");
        self::alertarDesarrollo($log);
    }

    /* ------------------------------------------------------------------ */
    /*  Transportes                                                        */
    /* ------------------------------------------------------------------ */

    private static function enviarCorreo(NotificacionLog $log): void
    {
        if (config('crm.notificaciones.correo_modo') === 'log') {
            Log::info("[correo simulado] {$log->asunto} -> ".implode(', ', array_column($log->destinatarios, 'email')));
            return;
        }

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST');
        $mail->Port = (int) env('MAIL_PORT', 587);
        $mail->Timeout = 20;
        $mail->SMTPAuth = filter_var(env('MAIL_SMTP_AUTH', true), FILTER_VALIDATE_BOOLEAN);
        if ($mail->SMTPAuth) {
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
        }
        $enc = env('MAIL_ENCRYPTION');
        if ($enc === 'tls') { $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; }
        elseif ($enc === 'ssl') { $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; }
        else { $mail->SMTPSecure = false; $mail->SMTPAutoTLS = false; }
        $mail->setFrom(env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME', 'no-reply@bullmarketing.com.co')), env('MAIL_FROM_NAME', 'BullMarketing'));
        $mail->CharSet = 'UTF-8';

        foreach ($log->destinatarios as $d) { $mail->addAddress($d['email'], $d['name'] ?? ''); }
        foreach ($log->copias ?? [] as $d) { $mail->addCC($d['email'], $d['name'] ?? ''); }
        foreach ($log->adjuntos ?? [] as $a) {
            $ruta = base_path('storage/app/'.ltrim($a['ruta'], '/'));
            if (is_file($ruta)) { $mail->addAttachment($ruta, $a['nombre'] ?? basename($ruta)); }
            else { Log::warning("Notificación #{$log->id}: adjunto no encontrado {$ruta}"); }
        }
        $mail->isHTML(true);
        $mail->Subject = $log->asunto ?: 'NOTIFICACIÓN CRM';
        $mail->Body = $log->cuerpo;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $log->cuerpo));
        $mail->send();
    }

    private static function enviarSms(NotificacionLog $log): void
    {
        $tel = $log->destinatarios[0] ?? null;
        if (config('crm.sms.modo') === 'log') {
            Log::info("[sms simulado] {$tel}: ".mb_substr($log->cuerpo, 0, 120));
            return;
        }
        $token = config('crm.sms.token');
        if (!$token) { throw new \RuntimeException('SMS_TOKEN no configurado.'); }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.hablame.co/api/sms/v5/send',
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'priority' => true, 'certificate' => true, 'campaignName' => 'BullMarketing SAS',
                'from' => config('crm.sms.remitente', 'BUllCRM'), 'flash' => false,
                'messages' => [['to' => $tel, 'text' => $log->cuerpo]],
            ]),
            CURLOPT_HTTPHEADER => ['accept: application/json', 'X-Hablame-Key: '.$token, 'Content-Type: application/json'],
        ]);
        $respuesta = curl_exec($curl);
        $err = curl_error($curl);
        $codigo = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) { throw new \RuntimeException("Hablame: {$err}"); }
        if ($codigo < 200 || $codigo >= 300) { throw new \RuntimeException("Hablame HTTP {$codigo}: ".mb_substr((string) $respuesta, 0, 300)); }
    }

    /* ------------------------------------------------------------------ */
    /*  Alerta a desarrollo                                                */
    /* ------------------------------------------------------------------ */

    public static function alertarDesarrollo(NotificacionLog $log): void
    {
        $cfg = config('crm.notificaciones.alertas', []);
        $cada = max(1, (int) ($cfg['cada_minutos'] ?? 60));
        $fallidas24h = NotificacionLog::fallidos()->ultimas24h()->count();
        $resumen = "Falló un {$log->canal} del CRM (evento {$log->evento}, ref ".($log->referencia ?: '—')."). Error: ".mb_substr((string) $log->error, 0, 300).". Fallidas en 24 h: {$fallidas24h}.";

        // Correo a desarrollo (si lo que falla es el correo, igual se intenta: puede ser un fallo puntual)
        foreach ($cfg['correos'] ?? [] as $correo) {
            if (!Cache::add("crm-alerta-correo-".md5($correo), 1, now()->addMinutes($cada))) { continue; }
            try {
                $tmp = new NotificacionLog(['canal' => 'correo', 'evento' => 'alertaDesarrollo', 'asunto' => "[BULLCRM] Notificación fallida ({$log->canal}: {$log->evento})",
                    'destinatarios' => [['email' => $correo, 'name' => 'Desarrollo']], 'copias' => [], 'adjuntos' => [],
                    'cuerpo' => '<p>'.e($resumen).'</p><p>Detalle en el CRM: menú Acciones → Notificaciones (registro #'.$log->id.').</p>']);
                self::enviarCorreo($tmp);
                $log->alertado_at = now();
            } catch (\Throwable $e) {
                Log::error("Alerta a desarrollo por correo ({$correo}) también falló: ".$e->getMessage());
            }
        }
        // SMS a desarrollo
        foreach ($cfg['telefonos'] ?? [] as $tel) {
            if (!Cache::add("crm-alerta-sms-".md5($tel), 1, now()->addMinutes($cada))) { continue; }
            try {
                $tmp = new NotificacionLog(['canal' => 'sms', 'evento' => 'alertaDesarrollo', 'destinatarios' => [self::limpiarTelefono($tel)],
                    'cuerpo' => 'BULLCRM: '.mb_substr($resumen, 0, 140)]);
                self::enviarSms($tmp);
                $log->alertado_at = now();
            } catch (\Throwable $e) {
                Log::error("Alerta a desarrollo por SMS ({$tel}) también falló: ".$e->getMessage());
            }
        }
        if ($log->alertado_at) { $log->save(); }
    }

    /* ------------------------------------------------------------------ */
    /*  Utilidades                                                         */
    /* ------------------------------------------------------------------ */

    /** Los datos históricos del CRM traen bytes latin1 en columnas utf8mb4; se convierten para que MySQL y el JSON no fallen. */
    public static function utf8(?string $s): string
    {
        $s = (string) $s;
        return mb_check_encoding($s, 'UTF-8') ? $s : mb_convert_encoding($s, 'UTF-8', 'ISO-8859-1');
    }

    private static function limpiarCorreos(array $lista): array
    {
        $out = [];
        foreach ($lista as $d) {
            $email = is_array($d) ? trim((string) ($d['email'] ?? '')) : trim((string) $d);
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { continue; }
            $out[strtolower($email)] = ['email' => $email, 'name' => self::utf8(is_array($d) ? (string) ($d['name'] ?? '') : '')];
        }
        return array_values($out);
    }

    private static function limpiarTelefono(?string $tel): ?string
    {
        $tel = preg_replace('/[^0-9+]/', '', (string) $tel);
        return strlen($tel) >= 7 ? $tel : null;
    }

    /** Resumen para pantallas: fallidas y enviadas de las últimas 24 h. */
    public static function salud(): array
    {
        return [
            'fallidas_24h' => NotificacionLog::fallidos()->ultimas24h()->count(),
            'enviadas_24h' => NotificacionLog::where('estado', 'enviado')->ultimas24h()->count(),
            'ultima_fallida' => NotificacionLog::fallidos()->latest()->first(),
        ];
    }
}
