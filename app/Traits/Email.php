<?php

namespace App\Traits;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\User;

trait Email
{
    // El directorio de destinatarios por área vive ahora en la tabla
    // notificacion_destinatarios (App\Models\NotificacionDestinatario::area()).

    /* PRESUPEUSTOS */
    public function presupuestoValidacionLiderComercial($presto, $user)
    {
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN CRM";

        if ($presto->cod_cc) {
            $body = "El presupuesto <b>{$presto->gestion->nom_proyecto_cot}</b> con centro de costos: <b>{$presto->cod_cc}</b> de <b>{$user->name}</b> fué actualizado.";
        } else {
            $body = "<b>{$user->name}</b> ha generado el presupuesto para el proyecto: <b>{$presto->gestion->nom_proyecto_cot}</b> y solicita aprobación.";
        }

        if ($presto->justificacion) {
            $body .= "<br><b>{$user->name}</b> ha realizado las siguientes observaciones: {$presto->justificacion}.";
        }

        array_push($recipients, ...\App\Models\NotificacionDestinatario::area('lider_comercial'));

        $altBody = "NOTIFICACIÓN CRM";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }
    
    public function presupuestoAprobacion($presto, $user){
        $subject = "NOTIFICACIÓN CRM";
        $recipients = [];
        $cc = [];

        // Decisión de negocio (11-sep-2026): TODA aprobación de margen es
        // exclusiva del rol Gerencia. Los aprobadores salen del permiso
        // 'aprobador-margen' (BD), no de IDs quemados por margen/cliente.
        $recipients = \App\Models\Permiso::usuariosCon('aprobador-margen');
        if (empty($recipients)) {
            \Log::error('presupuestoAprobacion: no hay usuarios con el permiso aprobador-margen; el correo de aprobación no tiene destinatarios.');
        }

        if ($presto->cod_cc){
            $body = "El presupuesto <b>{$presto->gestion->nom_proyecto_cot}</b> con centro de costos: <b>{$presto->cod_cc}</b> de <b>{$user->name}</b> fué actualizado.";
        }else {
            $body = "<b>{$user->name}</b> ha generado el presupuesto para el proyecto: <b>{$presto->gestion->nom_proyecto_cot}</b> y solicita aprobación.";
        }

        if ($presto->justificacion){
            $body .= "<br><b>{$user->name}</b> ha realizado las siguientes observaciones: {$presto->justificacion}.";
        }

        $altBody = "NOTIFICACIÓN CRM";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function presupuestoAprobado($user, $gestion, $justificacion, $cod_cc = null){
        $subject = "PRESUPUESTO ".$gestion->nom_proyecto_cot." APROBADO";
        $body = "El presupuesto del proyecto: <b>{$gestion->nom_proyecto_cot}</b> ha sido APROBADO con el siguiente centro de costos: <b>{$cod_cc}</b>.";

        if ($justificacion){
            $body .= "<br>El equipo de compras ha realizado las siguientes observaciones: {$justificacion}.";
        }

        $altBody = "Se ha Aprobado el presupuesto: ".$gestion->nom_proyecto_cot;
        $recipients = [];
        // Copia a los ejecutivos/asistentes del comercial. La relación
        // devuelve modelos Eloquent (y el email vive en el usuario ejecutivo),
        // por eso se transforma al formato de arrays que espera sendMail:
        // antes se pasaban los modelos y el CC se descartaba en silencio.
        $cc = [];
        foreach ($user->asistente as $asistenteRel) {
            if ($asistenteRel->ejecutivo) {
                $cc[] = ['name' => $asistenteRel->ejecutivo->name, 'email' => $asistenteRel->ejecutivo->email];
            }
        }

        // Los aprobadores de margen (Gerencia) reciben copia del resultado
        // cuando el margen estuvo en la frontera de aprobación (<= 35%).
        if ($gestion->presupuesto->margen_proy <= 35){
            array_push($recipients, ...\App\Models\Permiso::usuariosCon('aprobador-margen'));
        }

        array_push($recipients, [
            'name'=> $user->name,
            'email'=> $user->email
        ]);

        // array_push($recipients, [
        //     'name'=> 'Líder producción',
        //     'email'=> 'Armando.Espinosa@bullmarketing.com.co'
        // ]);

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function presupuestoRechazado($user, $gestion, $justificacion, $cod_cc = null){
        $recipients = [];
        $subject = "PRESUPUESTO ".$gestion->nom_proyecto_cot." RECHAZADO";
        $body = "El presupuesto del proyecto: <b>".$gestion->nom_proyecto_cot."</b> ha sido <b>RECHAZADO.</b>";

        // Decisión de negocio (11-sep-2026): las aprobaciones/rechazos de
        // margen son exclusivos de Gerencia (permiso 'aprobador-margen').
        $recipients = \App\Models\Permiso::usuariosCon('aprobador-margen');

        array_push($recipients, [
            'name'=> $user->name,
            'email'=> $user->email
        ]);

        if ($justificacion){
            $body .= "<br>El equipo de controller ha realizado las siguientes observaciones: {$justificacion}.";
        }

        $altBody = "Se ha rechazado el presupuesto: ".$gestion->nom_proyecto_cot;
        // Copia a los ejecutivos/asistentes del comercial. La relación
        // devuelve modelos Eloquent (y el email vive en el usuario ejecutivo),
        // por eso se transforma al formato de arrays que espera sendMail:
        // antes se pasaban los modelos y el CC se descartaba en silencio.
        $cc = [];
        foreach ($user->asistente as $asistenteRel) {
            if ($asistenteRel->ejecutivo) {
                $cc[] = ['name' => $asistenteRel->ejecutivo->name, 'email' => $asistenteRel->ejecutivo->email];
            }
        }

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    /*
        * NOTIFICACIONES ORDENES DE TRABAJO NATURALES
    */
    public function ocNaturalFirmada($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." FIRMADA";
        $body =
        "<p>
            La orden de trabajo de <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> ha sido <b>FIRMADA.</b> <br>
            Revisa el real ejecutado y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, [
            'name'=> $orden->naturalInfo->productor->name,
            'email'=> $orden->naturalInfo->productor->email
        ]);

        array_push($cc, ...\App\Models\NotificacionDestinatario::area('produccion'));

        $altBody = "ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." FIRMADA.";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalEvidenciasEnviadas($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - EVIDENCIAS ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." ENVIADAS";
        $body =
        "<p>
            Las evidencias de la orden de trabajo de <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> han sido <b>ENVIADAS.</b><br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, [
            'name'=> $orden->naturalInfo->productor->name,
            'email'=> $orden->naturalInfo->productor->email
        ]);

        array_push($cc, ...\App\Models\NotificacionDestinatario::area('produccion'));

        $altBody = "EVIDENCIAS ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." ENVIADAS";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function actualizacionControllerPresupuesto($presupuesto)
    {
        $recipients = \App\Models\NotificacionDestinatario::area('controller');
        $cc = [];

        $subject = "NOTIFICACIÓN BULLCRM - PRESUPUESTO PROYECTO #" . $presupuesto->cod_cc . " ACTUALIZADO / MODIFICADO";

        $body = "
        <p>
            El presupuesto del proyecto <b>#" . $presupuesto->cod_cc . "</b> ha sido <b>MODIFICADO / ACTUALIZADO</b>. <br>
            Fue aprobado por: <b>" . (auth()->user()->name ?? 'Usuario del Sistema') . "</b>.<br><br>
            Por favor ingresa a la plataforma para revisar el detalle de las modificaciones.
        </p>";

        $altBody = "PRESUPUESTO #" . $presupuesto->cod_cc . " ACTUALIZADO / MODIFICADO.";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalEvidenciasRechazadas($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - EVIDENCIAS ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." RECHAZADAS";
        $body =
        "<p>
            Las evidencias de la orden de trabajo de <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> han sido <b>RECHAZADAS.</b> por el equipo Controller<br>
            Notifica al tercero que debe adjuntar nuevamente las evidencias de la orden de trabajo.
        </p>";

        array_push($recipients, [
            'name'=> $orden->naturalInfo->productor->name,
            'email'=> $orden->naturalInfo->productor->email
        ]);

        array_push($cc, ...\App\Models\NotificacionDestinatario::area('produccion'));

        $altBody = "EVIDENCIAS ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." RECHAZADAS";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionController($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
        "<p>
            La orden de compra del tercero <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> ha sido validada por el productor: ".$orden->naturalInfo->productor->name."<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";


        array_push($recipients, ...\App\Models\NotificacionDestinatario::area('controller'));

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." POR REVISAR";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionContabilidad($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
        "<p>
            La orden de compra del tercero <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> ha sido validada por el equipo Controller.<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...\App\Models\NotificacionDestinatario::area('contabilidad'));

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." POR REVISAR";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionTesoreria($orden){
        $recipients = [];
        $cc = [];
        
        // Obtener los datos de forma segura
        $productorNombre = $orden->naturalInfo?->productor?->name ?? 'PRODUCTOR DESCONOCIDO';
        $terceroNombre = $orden->naturalInfo?->tercero?->nombre ?? '';
        $terceroApellido = $orden->naturalInfo?->tercero?->apellido ?? '';
        $terceroCompleto = trim("$terceroNombre $terceroApellido") ?: 'TERCERO DESCONOCIDO';

        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$productorNombre." POR REVISAR";
        $body =
        "<p>
            La orden de compra del tercero <b>".$terceroCompleto."</b> ha sido causada por contabilidad.<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...\App\Models\NotificacionDestinatario::area('tesoreria'));

        $altBody = "ORDEN DE COMPRA ".$terceroCompleto." POR REVISAR";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    /* **** */
    /*
    public function sendMail($subject, $body, $altBody = null, $params = null, $recipients = [], $cc = [], $attachment = null)
    {
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->Port       = env('MAIL_PORT', 1025);
            $mail->SMTPAuth   = false;          // Mailpit no requiere auth
            $mail->SMTPSecure = false;          // sin TLS/SSL en local
            $mail->SMTPAutoTLS = false;

            $from = env('MAIL_FROM_ADDRESS', 'no-reply@bullmarketing.local');
            $mail->setFrom($from, env('MAIL_FROM_NAME', 'BullMarketing'));

            /* Destinatarios principales *//*
            if (is_iterable($recipients)) {
                foreach ($recipients as $recipient) {
                    if (is_array($recipient) && isset($recipient['email'])) {
                        $mail->addAddress($recipient['email'], $recipient['name'] ?? '');
                    }
                }
            }

            /* Copias (CC) *//*
            if (is_iterable($cc)) {
                foreach ($cc as $copiados) {
                    if (is_array($copiados) && isset($copiados['email'])) {
                        $mail->addCC($copiados['email'], $copiados['name'] ?? '');
                    }
                }
            }

            /* Archivos adjuntos *//*
            if ($attachment) {
                $archivo_pago = str_replace('public/', '', $attachment);
                $mail->addAttachment("storage/{$archivo_pago}");
            }

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = view('mails.presupuestos', ['body' => $body, 'recipients' => $recipients])->render();
            $mail->AltBody = $altBody;

            $mail->send();
        } catch (Exception $e) {
            \Log::error('Error enviando mail: ' . $e->getMessage());
            // Ver nota abajo sobre este return
        }
    }
    */


    public function sendMail($subject, $body, $altBody = null, $params = null, $recipients = [], $cc = [], $attachment = null)
    {
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->Port       = env('MAIL_PORT', 587);
            $mail->SMTPAuth   = filter_var(env('MAIL_SMTP_AUTH', true), FILTER_VALIDATE_BOOLEAN);

            if ($mail->SMTPAuth) {
                $mail->Username = env('MAIL_USERNAME');
                $mail->Password = env('MAIL_PASSWORD');
            }

            $encryption = env('MAIL_ENCRYPTION'); // 'tls', 'ssl', o null
            if ($encryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }

            $from = env('MAIL_FROM_ADDRESS');
            $mail->setFrom($from, env('MAIL_FROM_NAME', 'BullMarketing'));

            /* Destinatarios principales */
            if (is_iterable($recipients)) {
                foreach ($recipients as $recipient) {
                    if (is_array($recipient) && isset($recipient['email'])) {
                        $mail->addAddress($recipient['email'], $recipient['name'] ?? '');
                    }
                }
            }

            /* Copias (CC) */
            if (is_iterable($cc)) {
                foreach ($cc as $copiados) {
                    if (is_array($copiados) && isset($copiados['email'])) {
                        $mail->addCC($copiados['email'], $copiados['name'] ?? '');
                    }
                }
            }

            /* Archivos adjuntos */
            if ($attachment) {
                $archivo_pago = str_replace('public/', '', $attachment);
                $mail->addAttachment("storage/{$archivo_pago}");
            }

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = view('mails.presupuestos', ['body' => $body, 'recipients' => $recipients])->render();
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            \Log::error("Error al enviar correo: {$mail->ErrorInfo}");
            return false;
        }
    }
    /* *** */

    /* ORDENES COMPRA */
    public function mailOrdenAprobada($orden){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try{
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = env('MAIL_PORT', 587);

            //Recipients
            $mail->setFrom(env('MAIL_USERNAME'), 'BullMarketing');
            /* COMPRAS */
                foreach (\App\Models\NotificacionDestinatario::area('compras') as $d) { $mail->addAddress($d['email'], $d['name']); }
            /* *** */

            /* LD PRODUCCION, PROVEEDOR, COMERCIAL */
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                foreach (\App\Models\NotificacionDestinatario::area('compras_cc') as $d) { $mail->addCC($d['email'], $d['name']); }
                $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
            /* *** */

            /* CONTABILIDAD */
                if ($orden->proveedor->anticipo > 0){
                    foreach (\App\Models\NotificacionDestinatario::area('contabilidad_pagos') as $d) { $mail->addCC($d['email'], $d['name']); }
                    // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                    foreach (\App\Models\NotificacionDestinatario::area('compras_cc') as $d) { $mail->addCC($d['email'], $d['name']); }
                }
            /* *** */

            $archivo_orden_helisa = str_replace('public/', '', $orden->archivo_orden_helisa);
            $mail->addAttachment("storage/{$archivo_orden_helisa}", "OC_".$orden->proveedor->tercero.".pdf");

            //Content
            $mail->isHTML(true);
            // $mail->Subject = "IGNORAR, PRUEBAS CRM";
            $mail->Subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;
            $mail->Body    = view('mails.ordenAprobada', ['orden' => $orden]);
            $mail->AltBody = "Se ha generado la orden de compra: {$orden->cod_oc} para el proveedor {$orden->proveedor->tercero}";

            $mail->send();
        } catch (Exception $e) {
            // El valor de retorno de estos metodos no lo usa ningun caller:
            // el redirect se descartaba y el fallo quedaba invisible.
            \Log::error("Fallo el envio de correo ({$mail->Subject}): {$mail->ErrorInfo}");
            return false;
        }
    }

    public function mailGrGenerado($orden){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try{
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = env('MAIL_PORT', 587);

            //Recipients
            $mail->setFrom(env('MAIL_USERNAME'), 'BullMarketing');
            /* COMPRAS */
                foreach (\App\Models\NotificacionDestinatario::area('compras') as $d) { $mail->addAddress($d['email'], $d['name']); }
            /* *** */

            /* LD PRODUCCION & PROVEEDOR */
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                foreach (\App\Models\NotificacionDestinatario::area('compras_cc') as $d) { $mail->addCC($d['email'], $d['name']); }

                $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
            /* *** */

            $archivo_orden_helisa = str_replace('public/', '', $orden->archivo_orden_helisa);
            $archivo_remision = str_replace('public/', '', $orden->archivo_remision);
            $mail->addAttachment("storage/{$archivo_orden_helisa}", "OC_".$orden->proveedor->tercero.".pdf");
            $mail->addAttachment("storage/{$archivo_remision}", "REMISION_".$orden->proveedor->tercero.".pdf");

            //Content
            $mail->isHTML(true);
            // $mail->Subject = "IGNORAR, PRUEBAS CRM";
            $mail->Subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;
            $mail->Body    = view('mails.grGenerado', ['orden' => $orden]);
            $mail->AltBody = "Se ha asignado el GR: {$orden->gr} para la orden de compra {$orden->cod_oc}";

            $mail->send();
        } catch (Exception $e) {
            // El valor de retorno de estos metodos no lo usa ningun caller:
            // el redirect se descartaba y el fallo quedaba invisible.
            \Log::error("Fallo el envio de correo ({$mail->Subject}): {$mail->ErrorInfo}");
            return false;
        }
    }

    public function mailOrdenAnulada($orden){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try{
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = env('MAIL_PORT', 587);

            //Recipients
            $mail->setFrom(env('MAIL_USERNAME'), 'BullMarketing');
            /* COMPRAS */
                foreach (\App\Models\NotificacionDestinatario::area('compras') as $d) { $mail->addAddress($d['email'], $d['name']); }
            /* *** */

            /* LD PRODUCCION & PROVEEDOR */
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                foreach (\App\Models\NotificacionDestinatario::area('compras_cc') as $d) { $mail->addCC($d['email'], $d['name']); }
            /* *** */

            $archivo_orden_helisa = str_replace('public/', '', $orden->archivo_orden_helisa);
            $archivo_remision = str_replace('public/', '', $orden->archivo_remision);
            $mail->addAttachment("storage/{$archivo_orden_helisa}", "OC_".$orden->proveedor->tercero.".pdf");
            $mail->addAttachment("storage/{$archivo_remision}", "REMISION_".$orden->proveedor->tercero.".pdf");

            //Content
            $mail->isHTML(true);
            // $mail->Subject = "IGNORAR, PRUEBAS CRM";
            $mail->Subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;
            $mail->Body    = view('mails.ordenAnulada', ['orden' => $orden]);
            $mail->AltBody = "Se ha anulado la roden de compra {$orden->cod_oc}";

            $mail->send();
        } catch (Exception $e) {
            // El valor de retorno de estos metodos no lo usa ningun caller:
            // el redirect se descartaba y el fallo quedaba invisible.
            \Log::error("Fallo el envio de correo ({$mail->Subject}): {$mail->ErrorInfo}");
            return false;
        }
    }

    public function mailAnticipoPagado($orden, $observaciones){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try{
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = env('MAIL_PORT', 587);

            //Recipients
            $mail->setFrom(env('MAIL_USERNAME'), 'BullMarketing');
            /* COMPRAS */
                foreach (\App\Models\NotificacionDestinatario::area('compras') as $d) { $mail->addAddress($d['email'], $d['name']); }
            /* *** */

            /* LD PRODUCCION, PROVEEDOR & PRODUCTOR*/
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                foreach (\App\Models\NotificacionDestinatario::area('compras_cc') as $d) { $mail->addCC($d['email'], $d['name']); }
                $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
            /* *** */

            /* CONTABILIDAD */
                foreach (\App\Models\NotificacionDestinatario::area('contabilidad_pagos') as $d) { $mail->addCC($d['email'], $d['name']); }
            /* *** */

            $archivo_pago = str_replace('public/', '', $orden->archivo_comprobante_pago);
            $mail->addAttachment("storage/{$archivo_pago}", "COMPROBANTE_PAGO_ANTICIPO $orden->cod_oc".$orden->proveedor->tercero.".pdf");

            //Content
            $mail->isHTML(true);
            // $mail->Subject = "IGNORAR, PRUEBAS CRM";
            $mail->Subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;
            $mail->Body    = view('mails.anticipoPagado', ['orden' => $orden, 'observaciones' => $observaciones]);
            $mail->AltBody = "Se ha generado el pago del anticipo de la orden: {$orden->cod_oc} para el proveedor {$orden->proveedor->tercero}";

            $mail->send();
        } catch (Exception $e) {
            // El valor de retorno de estos metodos no lo usa ningun caller:
            // el redirect se descartaba y el fallo quedaba invisible.
            \Log::error("Fallo el envio de correo ({$mail->Subject}): {$mail->ErrorInfo}");
            return false;
        }
    }
}
