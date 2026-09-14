<?php

namespace App\Traits;

use App\Services\Notificador;
use Illuminate\Support\Facades\URL;

/**
 * SMS del CRM (API de Hablame). Cada mensaje pasa por Notificador::sms, que
 * lo registra en notificaciones_log, lo reintenta y alerta a desarrollo si
 * falla. Con SMS_MODO=log no se envía: se escribe en el log (pruebas).
 */
trait SMS
{
    /** Mensaje de bienvenida de prueba al teléfono configurado (config crm.sms.saludo_telefono). */
    public function saludo_(){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": Bienvenido a Bull Marketing S.A.S! si tienes alguna duda o sugerencia, no dudes en contactarnos.";
        $this->sendAction(config('crm.sms.saludo_telefono'), $body);
    }

    /** Al crear una orden natural: enlace firmado del portal para completar datos y aceptar términos. */
    public function oc_natura_creada($tercero, $orden_id){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time())." \nHola $tercero->nombre ¡Bienvenido a Bull Marketing! \nCon este enlace: \n\n".
        URL::signedRoute('consulta-terceros', ['orden' => $orden_id])
        ."\n \nPuedes completar tu información y aceptar los términos de tu contratación. \n \nBull Marketing la agencia del ¡Siempre se puede!";
        $this->sendAction($tercero->telefono, $body, 'oc:'.$orden_id);
    }

    /** La orden pasó a "Evidencias": enlace para adjuntarlas. */
    public function oc_evidencias($tercero, $orden_id){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time())." \nHola $tercero->nombre. \nUtiliza este enlace: \n\n".
        URL::signedRoute('consulta-terceros', ['orden' => $orden_id])
        ."\n \nPara adjuntar las evidencias del trabajo que realizaste. \n \nBull Marketing la agencia del ¡Siempre se puede!";
        $this->sendAction($tercero->telefono, $body, 'oc:'.$orden_id);
    }

    /** Evidencias rechazadas: enlace para revisar comentarios y volver a cargarlas. */
    public function oc_evidencias_rechazadas($orden){
        $tercero = optional(optional($orden->naturalInfo)->tercero);
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time())." \nHola ".$tercero->nombre.". \nTus evidencias fueron rechazadas. Utiliza este enlace: \n\n".
        URL::signedRoute('consulta-terceros', ['orden' => $orden->id])
        ."\n \nPara revisar los comentarios de tus evidencias anteriores y adjuntar las nuevas evidencias del trabajo que realizaste. \n \nBull Marketing la agencia del ¡Siempre se puede!";
        $this->sendAction($tercero->telefono, $body, 'oc:'.$orden->id);
    }

    public function sendAction($tel, $body, $referencia = null){
        $evento = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'sendAction';
        return Notificador::sms($evento, $tel, $body, $referencia);
    }
}
