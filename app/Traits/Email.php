<?php

namespace App\Traits;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\models\User;

trait Email
{
    public $lider_comercial = [
        [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ],
    ];

//    public $controller = [
//        [
//            'name'=> 'Sebastian Beltran',
//            'email'=> 'sebastian.beltran@bullmarketing.com.co'
//        ],
//        [
//            'name'=> 'Equipo Controller',
//            'email'=> 'controller@bullmarketing.com.co'
//        ]
//    ];
    public $controller = [
        [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ],
        [
            'name'=> 'Juan Camilo Rojas Pineda',
            'email'=> 'juan.rojas@bullmarketing.com.co'
        ]
    ];

//    public $produccion = [
//        [
//            'name'=> 'Fernando Paez',
//            'email'=> 'fernando.paez@bullmarketing.com.co'
//        ],
//        [
//            'name'=> 'Geraldin Parada',
//            'email'=> 'geraldin.parada@bullmarketing.com.co'
//        ],
//        [
//            'name'=> 'Jesica Ramirez',
//            'email'=> 'jesica.ramirez@bullmarketing.com.co'
//        ]
//    ];
    public $produccion = [
        [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ],
        [
            'name'=> 'Juan Camilo Rojas Pineda',
            'email'=> 'juan.rojas@bullmarketing.com.co'
        ]
    ];

//    public $gerencia = [
//        [
//            'name'=> 'Alejandro Rodriguez',
//            'email'=> 'alejandro.rodriguez@bullmarketing.com.co'
//        ],
//        [
//            'name'=> 'Jony Ariza',
//            'email'=> 'j.ariza@bullmarketing.com.co'
//        ]
//    ];
    public $gerencia = [
        [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ],
        [
            'name'=> 'Juan Camilo Rojas Pineda',
            'email'=> 'juan.rojas@bullmarketing.com.co'
        ]
    ];

//    public $contabilidad = [
//        [
//            'name'=> 'Diana Bohorquez',
//            'email'=> 'diana.bohorquez@bullmarketing.com.co'
//        ],
//        [
//            'name'=> 'Facturación Proveedores',
//            'email'=> 'facturacion.proveedores@bullmarketing.com.co'
//        ],
//        [
//            'name'=> 'Auxiliar Contable',
//            'email'=> 'auxiliar.contable@bullmarketing.com.co'
//        ]
//    ];
    public $contabilidad = [
        [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ],
        [
            'name'=> 'Juan Camilo Rojas Pineda',
            'email'=> 'juan.rojas@bullmarketing.com.co'
        ]
    ];

//    public $tesoreria = [
//        [
//            'name'=> 'Tesorería',
//            'email'=> 'tesoreria@bullmarketing.com.co'
//        ],
//        [
//            'name'=> 'Tesorería',
//            'email'=> 'Ligia.Torres@bullmarketing.com.co'
//        ]
//    ];
    public $tesoreria = [
        [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ],
        [
            'name'=> 'Juan Camilo Rojas Pineda',
            'email'=> 'juan.rojas@bullmarketing.com.co'
        ]
    ];

    /* PRESUPEUSTOS */
    public function presupuestoValidacionLiderComercial($presto, $user) {
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN CRM";

        if ($presto->cod_cc) {
            $body = "El presupuesto <b>{$presto->gestion->nom_proyecto_cot}</b> con centro de costos: <b>{$presto->cod_cc}</b> de <b>{$user->name}</b> fué actualizado.";
        }
        else {
            $body = "<b>{$user->name}</b> ha generado el presupuesto para el proyecto: <b>{$presto->gestion->nom_proyecto_cot}</b> y solicita aprobaci&oacute;n.";
        }

        if ($presto->justificacion) {
            $body .= "<br><b>{$user->name}</b> ha realizado las siguientes observaciones: {$presto->justificacion}.";
        }

        array_push($recipients, ...$this->lider_comercial);

        $altBody = "NOTIFICACIÓN CRM";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function presupuestoAprobacion($presto, $user){
        $subject = "NOTIFICACIÓN CRM";
        $recipients = [];
        $cc = [];

        // COMPRAS - Alejo / CLARO JHONY
        if ($presto->margen_proy > 35 && (!$presto->gestion->claro)){
            $admin_id = 30;
        }elseif ($presto->margen_proy > 35 && $presto->gestion->claro){
            $admin_id = 36;
        }elseif ($presto->margen_proy < 35 && (!$presto->gestion->claro)){
            $admin_id = 8;

            // array_push($recipients, [
            //     'name'=> 'Adriana Trujillo',
            //     'email'=> 'adriana.trujillo@bullmarketing.com.co',
            // ]);

            // array_push($recipients, [
            //     'name'=> 'Cristian Rodriguez',
            //     'email'=> 'cristhian.rodriguez@bullmarketing.com.co'
            // ]);
        }elseif ($presto->margen_proy < 35 && $presto->gestion->claro){
            $admin_id = 10;

            array_push($recipients, [
                'name'=> 'Sebastian Beltran',
                'email'=> 'sebastian.beltran@bullmarketing.com.co'
            ]);
        }

        $recipient = User::select('name', 'email')->find($admin_id);
        array_push($recipients, [
            'name'=> $recipient->name,
            'email'=> $recipient->email
        ]);

        // array_push($recipients, [
        //     'name'=> 'Nefer Barragan',
        //     'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        // ]);

        if ($presto->cod_cc){
            $body = "El presupuesto <b>{$presto->gestion->nom_proyecto_cot}</b> con centro de costos: <b>{$presto->cod_cc}</b> de <b>{$user->name}</b> fué actualizado.";
        }else {
            $body = "<b>{$user->name}</b> ha generado el presupuesto para el proyecto: <b>{$presto->gestion->nom_proyecto_cot}</b> y solicita aprobaci&oacute;n.";
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
        $cc = $user->asistente;

        if ($gestion->presupuesto->margen_proy < 35 && (!$gestion->claro)){
            $admin_id = 8;

            // array_push($recipients, [
            //     'name'=> 'Adriana Trujillo',
            //     'email'=> 'adriana.trujillo@bullmarketing.com.co',
            // ]);

            // array_push($recipients, [
            //     'name'=> 'Cristian Rodriguez',
            //     'email'=> 'cristhian.rodriguez@bullmarketing.com.co'
            // ]);
        }elseif ($gestion->presupuesto->margen_proy < 35 && $gestion->claro){
            $admin_id = 10;

            array_push($recipients, [
                'name'=> 'Sebastian Beltran',
                'email'=> 'sebastian.beltran@bullmarketing.com.co'
            ]);
        }

        if ($gestion->presupuesto->margen_proy < 35){
            $recipient = User::select('name', 'email')->find($admin_id);
            array_push($recipients, [
                'name'=> $recipient->name,
                'email'=> $recipient->email
            ]);
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

        // COMPRAS - Alejo / CLARO JHONY
        if ($gestion->presupuesto->margen_proy > 35 && (!$gestion->claro)){
            $admin_id = 30;
        }elseif ($gestion->presupuesto->margen_proy > 35 && $gestion->claro){
            $admin_id = 36;
        }elseif ($gestion->presupuesto->margen_proy < 35 && !($gestion->claro)){
            $admin_id = 8;

            // array_push($recipients, [
            //     'name'=> 'Adriana Trujillo',
            //     'email'=> 'adriana.trujillo@bullmarketing.com.co'
            // ]);

            // array_push($recipients, [
            //     'name'=> 'Cristian Rodriguez',
            //     'email'=> 'cristhian.rodriguez@bullmarketing.com.co'
            // ]);
        }elseif ($gestion->presupuesto->margen_proy < 35 && $gestion->claro){
            $admin_id = 10;

            array_push($recipients, [
                'name'=> 'Sebastian Beltran',
                'email'=> 'sebastian.beltran@bullmarketing.com.co'
            ]);
        }

        $recipient = User::select('name', 'email')->find($admin_id);
        array_push($recipients, [
            'name'=> $recipient->name,
            'email'=> $recipient->email
        ]);

        array_push($recipients, [
            'name'=> $user->name,
            'email'=> $user->email
        ]);

        if ($justificacion){
            $body .= "<br>El equipo de controller ha realizado las siguientes observaciones: {$justificacion}.";
        }

        $altBody = "Se ha rechazado el presupuesto: ".$gestion->nom_proyecto_cot;
        $cc = $user->asistente;

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

//        array_push($recipients, [
//            'name'=> $orden->naturalInfo->productor->name,
//            'email'=> $orden->naturalInfo->productor->email
//        ]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

//        array_push($cc, ...$this->produccion);

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

//        array_push($recipients, [
//            'name'=> $orden->naturalInfo->productor->name,
//            'email'=> $orden->naturalInfo->productor->email
//        ]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

//        array_push($cc, ...$this->produccion);

        $altBody = "EVIDENCIAS ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." ENVIADAS";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalEvidenciasRechazadas($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - EVIDENCIAS ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." RECHAZADAS";
        $body =
        "<p>
            Las evidencias de la orden de trabajo de <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> han sido <b>RECHAZADAS.</b>
            Notifica al tercero que debe adjuntar nuevamente las evidencias de la orden de trabajo.
        </p>";

//        array_push($recipients, [
//            'name'=> $orden->naturalInfo->productor->name,
//            'email'=> $orden->naturalInfo->productor->email
//        ]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

//        array_push($cc, ...$this->produccion);

        $altBody = "EVIDENCIAS ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." RECHAZADAS";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
        "<p>
            La orden de compra del tercero <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> ha sido validada por el productor: ".$orden->naturalInfo->productor->name."<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

//        array_push($recipients, ...$this->produccion);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $altBody = "ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." POR REVISAR.";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRechazoLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." RECHAZADA";
        $body =
            "<p>
            La orden de compra del tercero <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> ha sido <b>RECHAZADA</b> por el lider de producción.<br>
            Revisa las observaciones y corrige la información de la orden de compra.
        </p>";

//        array_push($recipients, ['email' => $orden->naturalInfo->productor->email, 'name' => $orden->naturalInfo->productor->name]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $altBody = "ORDEN DE TRABAJO ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido;
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionGerencia($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $total = 0;

        foreach ($orden->ordenItems as $item) {
            $total += $item->vtotal_oc;
        }

        $body =
        "<p>
            La orden de compra del tercero <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> con un monto de: <b>".number_format($total)."</b> ha sido validada por producción.<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->gerencia);

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRechazoGerencia($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
            "<p>
            La orden de compra del tercero <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> ha sido <b>RECHAZADA</b> por gerencia.<br>
            Revisa las observaciones y corrige la información de la orden de compra.
        </p>";

//        array_push($recipients, ['email' => $orden->naturalInfo->productor->email, 'name' => $orden->naturalInfo->productor->name]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido;
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionEvidenciasLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
        "<p>
            Las evidencias de la orden de trabajo de <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> han sido <b>ENVIADAS.</b><br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->produccion);

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." POR REVISAR.";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRechazoEvidenciasLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
        "<p>
            Las evidencias de la orden de trabajo de <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> han sido <b>RECHAZADAS</b> por el lider de producción<br>
            Notifica al tercero que debe adjuntar nuevamente las evidencias de la orden de trabajo.
        </p>";

//        array_push($recipients, ['email' => $orden->naturalInfo->productor->email, 'name' => $orden->naturalInfo->productor->name]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido;
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionController($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
        "<p>
            Las evidencias de la orden de trabajo de: <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> han sido validadas.<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->controller);

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

        array_push($recipients, ...$this->contabilidad);

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." POR REVISAR";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalRevisionTesoreria($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->naturalInfo->productor->name." POR REVISAR";
        $body =
        "<p>
            La orden de compra del tercero <b>".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido."</b> ha sido causada por contabilidad.<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->tesoreria);

        $altBody = "ORDEN DE COMPRA ".$orden->naturalInfo->tercero->nombre." ".$orden->naturalInfo->tercero->apellido." POR REVISAR";

        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocNaturalEnvioCuentaCobro($orden, $file_path){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN CUENTA DE COBRO - BULL MARKETING S.A.S";
        $body =
        "<p>
            Se te ha generado una cuenta de cobro. Por favor revisar.
        </p>";

        array_push($recipients, [
            'name' => $orden->naturalInfo->tercero->nombre . " " . $orden->naturalInfo->tercero->apellido,
            'email' => $orden->naturalInfo->tercero->correo
        ]);

        $altBody = "CUENTA DE COBRO - BULL MARKETING S.A.S";

        $this->sendMail($subject, $body, $altBody, $recipients, $cc, $file_path);
    }

    /*
        * NOTIFICACIONES ORDENES DE COMPRA JURIDICAS
    */
    public function ocJuridicaRevisionLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $body =
        "<p>
            Tienes una solicitud de orden de compra registrada por el productor ".$orden->presupuesto->productor_info->name." pendiente por revisar.
            Confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->produccion);

        $altBody = "ORDEN DE COMPRA ".$orden->proveedor->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaRechazoLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $body =
            "<p>
            La orden de compra del proveedor <b>".$orden->presupuesto->tercero."</b> ha sido <b>RECHAZADA</b> por el lider de producción.<br>
            Revisa las observaciones y corrige la información de la orden de compra.
        </p>";

//        array_push($recipients, ['email' => $orden->presupuesto->productor_info->email, 'name' => $orden->presupuesto->productor_info->name]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $altBody = "ORDEN DE COMPRA ".$orden->proveedor->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaRevisionGerencia($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $total = 0;

        foreach ($orden->ordenItems as $item) {
            $total += $item->vtotal_oc;
        }

        $body =
            "<p>
            La orden de compra del proveedor <b>".$orden->presupuesto->tercero."</b> con un monto de: <b>".number_format($total)."</b> ha sido validada por producción.<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->gerencia);

        $altBody = "ORDEN DE COMPRA ".$orden->proveedor->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaRechazoGerencia($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $body =
            "<p>
            La orden de compra del proveedor <b>".$orden->presupuesto->tercero."</b> ha sido <b>RECHAZADA</b> por gerencia.<br>
            Revisa las observaciones y corrige la información de la orden de compra.
        </p>";

//        array_push($recipients, ['email' => $orden->presupuesto->productor_info->email, 'name' => $orden->presupuesto->productor_info->name]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $altBody = "ORDEN DE COMPRA ".$orden->proveedor->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaRevisionController($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $body =
        "<p>
            Tienes una solicitud de orden de compra registrada por el productor ".$orden->presupuesto->productor_info->name." pendiente por revisar.
            Confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->controller);

        $altBody = "ORDEN DE COMPRA ".$orden->presupuesto->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaRevisionRemiLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $body =
            "<p>
            La orden de compra del proveedor <b>".$orden->presupuesto->tercero."</b> ha sido validada por el productor: ".$orden->presupuesto->productor_info->name." y se ha firmado la remisión.<br>
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->produccion);

        $altBody = "ORDEN DE COMPRA ".$orden->presupuesto->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaRevisionRemiController($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $body =
        "<p>
            La orden de compra del proveedor <b>".$orden->presupuesto->tercero."</b> ha sido validada por producción.
            Revisa y confirma que la información esté correctamente diligenciada.
        </p>";

        array_push($recipients, ...$this->controller);

        $altBody = "ORDEN DE COMPRA ".$orden->presupuesto->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaRechazoRemisionLiderProd($orden){
        $recipients = [];
        $cc = [];
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE ".$orden->presupuesto->productor_info->name." POR REVISAR";
        $body =
            "<p>
            La remisión de laorden de compra del tercero <b>".$orden->presupuesto->tercero."</b> ha sido <b>RECHAZADA</b> por el lider de producción.<br>
            Revisa las observaciones y corrige la remisión de la orden de compra.
        </p>";

//        array_push($recipients, ['email' => $orden->presupuesto->productor_info->email, 'name' => $orden->presupuesto->productor_info->name]);

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $altBody = "ORDEN DE COMPRA ".$orden->presupuesto->tercero." POR REVISAR";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc);
    }

    public function ocJuridicaAprobada($orden){
        $recipients = [];
        $cc = [];
        $subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;;
        $body =
        "<p>
            De acuerdo con tu solicitud se gener&oacute; la <b>Orden de compra: ". $orden->cod_oc."</b> para el proveedor <b>".$orden->proveedor->tercero."</b>.<br>
            No olvides subir la remisión para generar el Good Receive respectivo.

            Cordialmente,<br>
            Saludos.
        </p>";

//        array_push($recipients, ['email' => 'Compras@bullmarketing.com.co', 'name' => 'Sebastian Beltran'] );
//        array_push($recipients, ['email' => $orden->presupuesto->gestion->comercial->email, 'name' => $orden->presupuesto->gestion->comercial->name] );
//        array_push($recipients, ['email' => $orden->presupuesto->productor_info->email, 'name' => $orden->presupuesto->productor_info->name] );
//        array_push($recipients, ['email' => 'Compras@bullmarketing.com.co', 'name' => 'Sebastian Beltran'] );
//
//        array_push($cc, ['email' => 'nicol.riano@bullmarketing.com.co', 'name' => 'Nicol Riaño'] );
//        array_push($cc, ['email' => 'katherine.galvis@bullmarketing.com.co', 'name' => 'Katherine Galvis'] );
//        array_push($cc, ['email' => $orden->proveedor->correo, 'name' => $orden->proveedor->contacto] );
//
//        /* CONTABILIDAD */
//        if ($orden->proveedor->anticipo > 0) {
//            array_push( $cc, ['email' => 'ontadores@bullmarketing.com.co', 'name' => 'Contadores'] );
//            array_push( $cc, ['email' => 'tesoreria@bullmarketing.com.co', 'name' => 'Tesoreria'] );
//        }

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $files = [
            [
                'name' => "OC_".$orden->proveedor->tercero.".pdf",
                'path' => $orden->archivo_orden_helisa
            ]
        ];

        $altBody = "Se ha generado la orden de compra: ".$orden->cod_oc." para el proveedor ".$orden->proveedor->tercero;
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc, $files);
    }

    public function ocJuridicaGrGenerado($orden){
        $recipients = [];
        $cc = [];
        $subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;
        $body =
        "<p>
            <b>Sr. Proveedor</b><br>
            A continuaci&oacute;n, se relaciona el GR correspondiente para el proceso de radicaci&oacute;n de factura. <br>
            <ul>
                <li>OC: ".$orden->cod_oc."</li>
                <li>GR: ".$orden->gr."</li>
            </ul>

            Por favor radicar en <a href='mailto:Facturacion.proveedores@bullmarketing.com.co'>Facturacion.proveedores@bullmarketing.com.co</a>
            con copia a <a href='mailto:compras@bullmarketing.com.co'>compras@bullmarketing.com.co</a>. <br>
        </p>";

//        array_push($recipients, ['email' => 'Compras@bullmarketing.com.co', 'name' => 'Sebastian Beltran'] );
//        array_push($recipients, ['email' => $orden->presupuesto->gestion->comercial->email, 'name' => $orden->presupuesto->gestion->comercial->name] );
//        array_push($recipients, ['email' => $orden->presupuesto->productor_info->email, 'name' => $orden->presupuesto->productor_info->name] );
//
//        array_push($cc, ['email' => 'nicol.riano@bullmarketing.com.co', 'name' => 'Nicol Riaño'] );
//        array_push($cc, ['email' => 'katherine.galvis@bullmarketing.com.co', 'name' => 'Katherine Galvis'] );
//        array_push($cc, ['email' => $orden->proveedor->correo, 'name' =>$orden->proveedor->contacto] );

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $files = [
            [
                'name' => "OC_".$orden->proveedor->tercero.".pdf",
                'path' => $orden->archivo_orden_helisa
            ],
            [
                'name' => "REMISION_".$orden->proveedor->tercero.".pdf",
                'path' => $orden->archivo_remision
            ]
        ];

        $altBody = "Se ha asignado el GR: ".$orden->gr." para la orden de compra ".$orden->cod_oc;
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc, $files);
    }

    public function ocJuridicaAnulada($orden){
        $recipients = [];
        $cc = [];
        $subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;
        $body =
        "<p>
            Hola <b>".$orden->presupuesto->productor_info->name."</b>,<br><br>

            Debido a inconsitencias en la <b>Orden de compra: ".$orden->cod_oc." </b> para el proveedor <b>".$orden->proveedor->tercero."</b>,<br>
            El equipo de compras ha decidido anularla con las siguientes obervaciones: <br><br>";

        if ( $orden->observaciones_anulacion ) {
            $body .= "<b>Observaciones:</b> ".$orden->observaciones_anulacion."<br><br>";
        }

        $body .= "
            Cordialmente,<br>
            Saludos.
        </p>";

//        array_push($recipients, ['email' => 'Compras@bullmarketing.com.co', 'name' => 'Sebastian Beltran'] );
//        array_push($recipients, ['email' => $orden->presupuesto->gestion->comercial->email, 'name' => $orden->presupuesto->gestion->comercial->name] );
//        array_push($recipients, ['email' => $orden->presupuesto->productor_info->email, 'name' => $orden->presupuesto->productor_info->name] );
//
//        array_push($cc, ['email' => 'nicol.riano@bullmarketing.com.co', 'name' => 'Nicol Riaño'] );
//        array_push($cc, ['email' => 'katherine.galvis@bullmarketing.com.co', 'name' => 'Katherine Galvis'] );

        array_push($recipients, [
            'name'=> 'Nefer Barragan',
            'email'=> 'Neffer.Barragan@bullmarketing.com.co'
        ]);

        $files = [
            [
                'name' => "OC_".$orden->proveedor->tercero.".pdf",
                'path' => $orden->archivo_orden_helisa
            ],
            [
                'name' => "REMISION_".$orden->proveedor->tercero.".pdf",
                'path' => $orden->archivo_remision
            ]
        ];

        $altBody = "Se ha anulado la roden de compra {$orden->cod_oc}";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc, $files);
    }

    public function ocJuridicaAnticipoPagado($orden, $observaciones){
        $recipients = [];
        $cc = [];
        $subject = "OC: ".$orden->cod_oc." ".$orden->proveedor->tercero;
        $body =
        "<p>
            <b>Sr. Proveedor</b><br>
            Se relaciona el comprobante de pago del ".$orden->proveedor->anticipo ."% correspondiente al anticipo acordado en la negociaci&oacute;n realizada.
            <ul>
                <li>OC: ".$orden->cod_oc."</li>
            </ul>
        </p>";

        if ( $observaciones ) {
            $body .=
            "<p>
                <b>Observaciones:</b> ".$observaciones."
            </p>";
        }

        $body .=
        "<p>
            Cordialmente,<br>
            Saludos.
        </p>";

        array_push($recipients, ['email' => 'Compras@bullmarketing.com.co', 'name' => 'Sebastian Beltran'] );
        array_push($recipients, ['email' => $orden->presupuesto->gestion->comercial->email, 'name' => $orden->presupuesto->gestion->comercial->name] );
        array_push($recipients, ['email' => $orden->presupuesto->productor_info->email, 'name' => $orden->presupuesto->productor_info->name] );

        array_push($cc, ['email' => 'nicol.riano@bullmarketing.com.co', 'name' => 'Nicol Riaño'] );
        array_push($cc, ['email' => 'katherine.galvis@bullmarketing.com.co', 'name' => 'Katherine Galvis'] );
        array_push($cc, ['email' => $orden->proveedor->correo, 'name' => $orden->proveedor->contacto] );

        $files = [
            [
                'name' => "COMPROBANTE_PAGO_ANTICIPO $orden->cod_oc".$orden->proveedor->tercero.".pdf",
                'path' => $orden->archivo_comprobante_pago
            ]
        ];

        $altBody = "Se ha generado el pago del anticipo de la orden: {$orden->cod_oc} para el proveedor {$orden->proveedor->tercero}";
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc, $files);
    }

    /**
     * Función principal para envío de notificaciones por correo
     * @param $subject
     * @param $body
     * @param $altBody
     * @param $params
     * @param $recipients
     * @param $cc
     * @param $attachment
     * @return \Illuminate\Http\RedirectResponse|void
     */
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

            $mail->setFrom(env('MAIL_USERNAME'), 'BULLMARKETING');

            /* Recipients */
                foreach ($recipients as $recipient) {
                    $mail->addAddress($recipient['email'], $recipient['name']);
                }

                foreach ($cc as $copiados) {
                    $mail->addCC($copiados['email'], $copiados['name']);
                }
            /* *** */

            if ($attachment){
                foreach ($attachment as $file) {
                    $path = str_replace('public/', '', $file['path']);
                    $mail->addAttachment("storage/{$path}", $file['name']);
                }
                // $archivo_pago = str_replace('public/', '', $orden->archivo_comprobante_pago);
                // $archivo_pago = str_replace('public/', '', $attachment);
                // $mail->addAttachment("storage/{$archivo_pago}", "COMPROBANTE_PAGO_ANTICIPO $orden->cod_oc".$orden->proveedor->tercero.".pdf");
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
                $mail->addAddress('Compras@bullmarketing.com.co', 'Sebastian Beltran');
            /* *** */

            /* LD PRODUCCION, PROVEEDOR, COMERCIAL */
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                $mail->addCC('nicol.riano@bullmarketing.com.co');
                $mail->addCC('katherine.galvis@bullmarketing.com.co');
                $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
            /* *** */

            /* CONTABILIDAD */
                if ($orden->proveedor->anticipo > 0){
                    $mail->addCC('contadores@bullmarketing.com.co');
                    $mail->addCC('tesoreria@bullmarketing.com.co');
                    // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                    $mail->addCC('nicol.riano@bullmarketing.com.co');
                    $mail->addCC('katherine.galvis@bullmarketing.com.co');
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
                $mail->addAddress('Compras@bullmarketing.com.co', 'Sebastian Beltran');
            /* *** */

            /* LD PRODUCCION & PROVEEDOR */
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                $mail->addCC('nicol.riano@bullmarketing.com.co');
                $mail->addCC('katherine.galvis@bullmarketing.com.co');

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
            return redirect()->back()->withErrors("Error: {$mail->ErrorInfo}")->withInput();
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
                $mail->addAddress('Compras@bullmarketing.com.co', 'Sebastian Beltran');
            /* *** */

            /* LD PRODUCCION & PROVEEDOR */
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                $mail->addCC('nicol.riano@bullmarketing.com.co');
                $mail->addCC('katherine.galvis@bullmarketing.com.co');
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
            return redirect()->back()->withErrors("Error: {$mail->ErrorInfo}")->withInput();
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
                $mail->addAddress('Compras@bullmarketing.com.co', 'Compras');
            /* *** */

            /* LD PRODUCCION, PROVEEDOR & PRODUCTOR*/
                $mail->addAddress($orden->presupuesto->gestion->comercial->email, $orden->presupuesto->gestion->comercial->name);
                $mail->addAddress($orden->presupuesto->productor_info->email, $orden->presupuesto->productor_info->name);
                // $mail->addCC('Armando.Espinosa@bullmarketing.com.co');
                // $mail->addCC('cristhian.rodriguez@bullmarketing.com.co');
                $mail->addCC('nicol.riano@bullmarketing.com.co');
                $mail->addCC('katherine.galvis@bullmarketing.com.co');
                $mail->addCC($orden->proveedor->correo, $orden->proveedor->contacto);
            /* *** */

            /* CONTABILIDAD */
                $mail->addCC('contadores@bullmarketing.com.co');
                $mail->addCC('tesoreria@bullmarketing.com.co');
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
}
