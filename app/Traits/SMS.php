<?php

namespace App\Traits;
use App\models\User;

trait SMS
{
    // public function presupuestoAprobacion($rentabilidad, $name, $cod_cc = null){
    //     // Adri - Alejo
    //     $admin_id = ($rentabilidad > 35) ? "30" : "26";
    //     $tel = User::select('telefono')->find($admin_id)->telefono;

    //     if ($cod_cc){
    //         $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": El presupuesto con centro de costos: ".$cod_cc." de ".$name." fué actualizado.";
    //     }else {
    //         $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": Tienes un presupuesto de ".$name." por revisar.";
    //     }

    //     $this->sendAction($tel,$body);
    // }

    // public function presupuestoAprobado($user, $gestion, $cod_cc){
    //     $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": El presupuesto del proyecto: ".$gestion->nom_proyecto_cot." ha sido APROBADO con el siguiente centro de costos: ".$cod_cc.".";
    //     $this->sendAction($user->telefono, $body);

    //     // Mensajes ejectuvios.
    //     foreach($user->asistente as $asistentes){
    //         $this->sendAction($asistentes->ejecutivo->telefono, $body);
    //     }
    // }

    // public function presupuestoRechazado($user, $gestion, $cod_cc){
    //     $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": El presupuesto del proyecto: ".$gestion->nom_proyecto_cot." ha sido RECHAZADO.";
    //     $this->sendAction($user->telefono, $body);

    //     // Mensajes ejectuvios.
    //     foreach($user->asistente as $asistentes){
    //         $this->sendAction($asistentes->ejecutivo->telefono, $body);
    //     }
    // }

    public function saludo_(){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": Bienvenido a Bull Marketing S.A.S! si tienes alguna duda o sugerencia, no dudes en contactarnos.";
        $this->sendAction("3134085483", $body);
    }

    public function oc_natura_creada($tercero, $orden_id){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time())." \nHola $tercero->nombre ¡Bienvenido a Bull Marketing! \nCon este enlace: \n\n".
        route('consulta-terceros')."?orden=".$orden_id

        ."\n \nPuedes completar tu información y aceptar los términos de tu contratación. \n \nBull Marketing la agencia del ¡Siempre se puede!";

        $this->sendAction($tercero->telefono, $body);
    }

    public function oc_evidencias($tercero, $orden_id){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time())." \nHola $tercero->nombre. \nUtiliza este enlace: \n\n".
        route('consulta-terceros')."?orden=".$orden_id

        ."\n \nPara adjuntar las evidencias del trabajo que realizaste. \n \nBull Marketing la agencia del ¡Siempre se puede!";

        $this->sendAction($tercero->telefono, $body);
    }

    public function oc_evidencias_rechazadas($orden){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time())." \nHola ".$orden->naturalInfo->tercero->nombre.". \nTus evidencias fueron rechazadas. Utiliza este enlace: \n\n".
        route('consulta-terceros')."?orden=".$orden->id

        ."\n \nPara revisar los comentarios de tus evidencias anteriores y adjuntar las nuevas evidencias del trabajo que realizaste. \n \nBull Marketing la agencia del ¡Siempre se puede!";

        $this->sendAction($orden->naturalInfo->tercero->telefono, $body);
    }

    public function sendAction($tel, $body){
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.hablame.co/api/sms/v5/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            "priority" => true,
            "certificate" => true,
            "campaignName" => "BullMarketing SAS",
            "from" => "BUllCRM",
            "flash" => false,
            'flash' => '0',
            'messages' => [
                [
                    'to' => trim($tel),
                    'text' => $body,
                ]
            ],
        ]),
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            "X-Hablame-Key: ".env('SMS_TOKEN'),
            "Content-Type: application/json",
        ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }
}
