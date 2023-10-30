<?php 

namespace App\Traits;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP; 
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

trait Email 
{
    public function send($orden){
        $archivo_orden_helisa = str_replace('public/', '', $orden->archivo_orden_helisa); 
        dd(file_exists(asset("storage/$archivo_orden_helisa")));

        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions
        
        try{
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = env('MAIL_HOST');                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = env('MAIL_USERNAME');                     //SMTP username
            $mail->Password   = env('MAIL_PASSWORD');                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = env('MAIL_PORT', 587);                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(env('MAIL_USERNAME'), 'BullMarketing');
            // $mail->addAddress('james.vallejo@iglumarketingdigital.com', 'Joe User');     //Add a recipient
            $mail->addAddress($orden->email_prov, $orden->contacto_prov);     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addStringAttachment($orden->archivo_cot_helisa, "OC_".$orden->proveedor.".pdf");
            $archivo_orden_helisa = str_replace('public/', '', $orden->archivo_orden_helisa); 
            $mail->addAttachment(asset("storage/$archivo_orden_helisa"), "OC_".$orden->proveedor.".pdf");
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');
 
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = "OC ".$orden->proveedor." "."OC: ".$orden->cod_oc;
            $mail->Body    = view('mails.index', ['cod_oc' => $orden->cod_oc, 'gr' => $orden->gr]);
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
        } catch (Exception $e) {
            dd("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}