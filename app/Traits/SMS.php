<?php

namespace App\Traits;
use App\models\User;

trait SMS
{
    public function presupuestoAprobacion($rentabilidad, $name, $cod_cc = null){
        // Adri - Alejo
        $admin_id = ($rentabilidad > 35) ? "30" : "26";
        $tel = User::select('telefono')->find($admin_id)->telefono;

        if ($cod_cc){
            $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": El presupuesto con centro de costos: ".$cod_cc." de ".$name." fué actualizado.";
        }else {
            $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": Tienes un presupuesto de ".$name." por revisar.";
        }

        $this->sendAction($tel,$body);
    }

    public function presupuestoAprobado($user, $gestion, $cod_cc){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": El presupuesto del proyecto: ".$gestion->nom_proyecto_cot." ha sido APROBADO con el siguiente centro de costos: ".$cod_cc.".";
        $this->sendAction($user->telefono, $body);

        // Mensajes ejectuvios.
        foreach($user->asistente as $asistentes){
            $this->sendAction($asistentes->ejecutivo->telefono, $body);
        }
    }

    public function presupuestoRechazado($user, $gestion, $cod_cc){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": El presupuesto del proyecto: ".$gestion->nom_proyecto_cot." ha sido RECHAZADO.";
        $this->sendAction($user->telefono, $body);

        // Mensajes ejectuvios.
        foreach($user->asistente as $asistentes){
            $this->sendAction($asistentes->ejecutivo->telefono, $body);
        }
    }

    public function saludo(){
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": Bienvenido a BULLCRM, si tienes alguna duda o sugerencia, no dudes en contactarnos.";
        $this->sendAction("3134085483", $body);
    }

    public function sendAction($tel, $body){
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.hablame.co/api/utilities/v5/ping",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => json_encode([
            'toNumber' => trim($tel),
            'sms' => $body,
            'flash' => '0',
            'sc' => '890202',
            'request_dlvr_rcpt' => '0'
        ]),
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            "X-Hablame-Key: 86raBKZddxjN3Gqsm0aQEDqwrAjczJNW6Glo7UJbAx46IfGTOnhoRmtwQbWz4epUu3kX9HQkgP307jgMMMCNKO1HDehTnVv0iD9FJyWtT12ugn5RLEAStdXqwYZam9Bz",
            "Content-Type: application/json",
            // "Token: e37adba1e64fdec84d283fe694926455"
        ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        echo $response;
    }
}
