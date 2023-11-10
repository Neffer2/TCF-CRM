<?php 

namespace App\Traits;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP; 
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

trait Email 
{
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
                $mail->addCC('neffer.barragan@iglumarketingdigital.com');
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
            dd("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
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
                $mail->addCC('neffer.barragan@iglumarketingdigital.com');
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
            dd("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}