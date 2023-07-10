<?php 

namespace App\Traits;
use App\models\User;
 
trait SMS  
{
    public function presupuestoAprobacion($rentabilidad, $name){    
        
        if ($rentabilidad > 35){
            $admin_id = "2";
        }else{
            $admin_id = '26';
        }
        
        $admin = User::select('telefono')->find($admin_id);
        $tel = $admin->telefono;
        $body = "BULLCRM - ".date('d/m/Y - h:i a', time()).": Tienes un presupuesto de ".$name." por revisar.";

        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api103.hablame.co/api/sms/v3/send/priority",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'toNumber' => trim($tel),
            'sms' => $body,
            'flash' => '0',
            'sc' => '890202',
            'request_dlvr_rcpt' => '0'
        ]),
        CURLOPT_HTTPHEADER => [
            "Account: 10025540",
            "ApiKey: AXx6DeXD0QO4agaXZIboUnxK4a5Q0I",
            "Content-Type: application/json",
            "Token: e37adba1e64fdec84d283fe694926455"
        ],
        ]); 
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }
}