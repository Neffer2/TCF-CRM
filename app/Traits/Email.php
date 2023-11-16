<?php 

namespace App\Traits;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP; 
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\models\User;

trait Email  
{       
    /* PRESUPEUSTOS */
    public function presupuestoAprobacion($rentabilidad, $name, $justificacion, $cod_cc = null){
        $subject = "NOTIFICACIÓN CRM";
        $recipients = [];
        $cc = [];

        // Adri - Alejo
        $admin_id = ($rentabilidad > 35) ? "30" : "26";        
        $recipient = User::select('name', 'email')->find($admin_id);
        array_push($recipients, [
            'name'=> $recipient->name,
            'email'=> $recipient->email
        ]);
        
        // array_push($recipients, [
        //     'name'=> 'Nefer Barragan',
        //     'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        // ]);
        
        if ($cod_cc){
            $body = "El presupuesto con centro de costos: <b>{$cod_cc}</b> de <b>{$name}</b> fué actualizado.";
        }else {
            $body = "Tienes un presupuesto de {$name} por revisar.";
        }

        if ($justificacion){
            $body .= "<br><b>{$name}</b> ha realizado las siguientes observaciones: {$justificacion}.";
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
        $cc = $user->asistente;
        
        array_push($recipients, [
            'name'=> $user->name,
            'email'=> $user->email
        ]);

        // array_push($recipients, [
        //     'name'=> 'Nefer Barragan',
        //     'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        // ]);

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function presupuestoRechazado($user, $gestion, $justificacion, $cod_cc = null){
        $subject = "PRESUPUESTO ".$gestion->nom_proyecto_cot." RECHAZADO";
        $body = "El presupuesto del proyecto: <b>".$gestion->nom_proyecto_cot."</b> ha sido <b>RECHAZADO.</b>";

        if ($justificacion){
            $body .= "<br>El equipo de compras ha realizado las siguientes observaciones: {$justificacion}.";
        }

        $altBody = "Se ha rechazado el presupuesto: ".$gestion->nom_proyecto_cot;
        $recipients = [];
        $cc = $user->asistente;
        
        array_push($recipients, [
            'name'=> $user->name,
            'email'=> $user->email
        ]);

        // array_push($recipients, [
        //     'name'=> 'Nefer Barragan',
        //     'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        // ]);

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }
    
    public function sendMail($subject, $body, $altBody = null, $params = null, $recipients, $cc = null, $attachment = null){
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
 
            $mail->setFrom(env('MAIL_USERNAME'), 'BullMarketing'); 
            
            /* Recipients */
                foreach ($recipients as $recipient) {
                    $mail->addAddress($recipient['email'], $recipient['name']);
                }

                foreach ($cc as $copiados) {
                    $mail->addCC($copiados->ejecutivo->email);        
                }
            /* *** */
            
            if ($attachment){
                // $archivo_pago = str_replace('public/', '', $orden->archivo_comprobante_pago);
                $archivo_pago = str_replace('public/', '', $attachment);
                $mail->addAttachment("storage/{$archivo_pago}", "COMPROBANTE_PAGO_ANTICIPO $orden->cod_oc".$orden->proveedor->tercero.".pdf");
            }

            //Content
            $mail->isHTML(true);
            $mail->Subject = utf8_decode($subject);
            $mail->Body    = view('mails.presupuestos', ['body' => $body, 'recipients' => $recipients]); 
            $mail->AltBody = utf8_decode($altBody);

            $mail->send();
        } catch (Exception $e) {
            return redirect()->back()->withErrors("Error: {$mail->ErrorInfo}")->withInput();
        }
    }

    /* ORDENES COMPRA */
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
                // $mail->addAddress('Adriana.Trujillo@bullmarketing.com.co', 'Adriana Trujillo');
                // $mail->addAddress('Compras@bullmarketing.com.co', 'Luz Melo');
            /* *** */

            /* LD PRODUCCION, PROVEEDOR & PRODUCTOR*/
            // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
            // $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
            // $mail->addCC('neffer.barragan@iglumarketingdigital.com');
            $mail->addCC('Neffer.Barragan@bullmarketing.com.co');
            $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
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
            return redirect()->back()->withErrors("Error: {$mail->ErrorInfo}")->withInput();
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
                // $mail->addAddress('Adriana.Trujillo@bullmarketing.com.co', 'Adriana Trujillo');
                // $mail->addAddress('Compras@bullmarketing.com.co', 'Luz Melo');
            /* *** */

            /* LD PRODUCCION & PROVEEDOR */
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('neffer.barragan@iglumarketingdigital.com');
                $mail->addCC('Neffer.Barragan@bullmarketing.com.co');
                // $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
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
            return redirect()->back()->withErrors("Error: {$mail->ErrorInfo}")->withInput();
        }
    }

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
                // $mail->addAddress('Adriana.Trujillo@bullmarketing.com.co', 'Adriana Trujillo');
                // $mail->addAddress('Compras@bullmarketing.com.co', 'Luz Melo');
            /* *** */

            /* LD PRODUCCION & PROVEEDOR */
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                $mail->addCC('Neffer.Barragan@bullmarketing.com.co');
                $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
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
            return redirect()->back()->withErrors("Error: {$mail->ErrorInfo}")->withInput();
        }
    }
}