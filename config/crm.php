<?php

/*
|--------------------------------------------------------------------------
| Parámetros de negocio y de notificaciones del BULLCRM
|--------------------------------------------------------------------------
| Antes estaban quemados en el código. Se cambian por .env (o aquí) sin
| tocar la lógica. Los valores por defecto son los que tenía el código.
*/
return [

    'anticipos' => [
        // Anticipos de productor por debajo de este valor no pasan por gerencia:
        // el líder de producción los aprueba y van directo a evidencias.
        'umbral_gerencia' => (float) env('CRM_ANTICIPO_UMBRAL_GERENCIA', 500000),
    ],

    'sms' => [
        // 'api' envía por Hablame; 'log' solo escribe en el log (entorno local / pruebas)
        'modo' => env('SMS_MODO', 'api'),
        'token' => env('SMS_TOKEN'),
        'remitente' => env('SMS_REMITENTE', 'BUllCRM'),
        'saludo_telefono' => env('SMS_SALUDO_TELEFONO', '3134085483'),
    ],

    'notificaciones' => [
        // 'smtp' envía de verdad; 'log' solo escribe en el log (MAIL_MAILER=log en local)
        'correo_modo' => env('MAIL_MAILER', 'smtp') === 'log' ? 'log' : 'smtp',
        // Intentos por envío (con espera creciente entre intentos)
        'reintentos' => (int) env('CRM_NOTIF_REINTENTOS', 3),
        'espera_segundos' => [2, 5],
        // Alerta a desarrollo cuando un envío falla definitivamente
        'alertas' => [
            'correos' => array_values(array_filter(array_map('trim', explode(',', env('CRM_ALERTA_CORREOS', 'data@bullmarketing.com.co'))))),
            'telefonos' => array_values(array_filter(array_map('trim', explode(',', env('CRM_ALERTA_TELEFONOS', ''))))),
            // No repetir la misma alerta (por canal) más de una vez en este lapso
            'cada_minutos' => (int) env('CRM_ALERTA_CADA_MINUTOS', 60),
        ],
    ],
];
