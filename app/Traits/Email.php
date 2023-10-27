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
    public function send(){
        $this->orden_compra_pdf();
        
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions
        
        try{
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = env('MAIL_HOST');                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = env('MAIL_USERNAME');                     //SMTP username
            $mail->Password   = env('MAIL_PASSWORD');                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = env('MAIL_PORT', 587);                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(env('MAIL_USERNAME'), 'Mailer');
            // $mail->addAddress('james.vallejo@iglumarketingdigital.com', 'Joe User');     //Add a recipient
            $mail->addAddress('n3f73r@gmail.com', 'Joe User');     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            $mail->addStringAttachment($this->orden_compra_pdf(), 'my_pdf.pdf');
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = view('mails.index', ['nombre' => 'SAPA PERRA']);
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    /* Tipo: Interno, cliente */
    public function orden_compra_pdf(){
        $dompdf = new Dompdf(array('enable_remote' => true));
        $html = View::make('exports.oc')->render(); 
        $dompdf->loadHtml($html);
        $dompdf->render();
        return $dompdf->stream();
        
        // return $dompdf->output();
    }
}