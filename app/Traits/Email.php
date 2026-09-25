<?php

namespace App\Traits;

use App\Models\NotificacionDestinatario;
use App\Models\Permiso;
use App\Services\AprobacionPresupuesto;
use App\Services\Notificador;

/**
 * Correos del CRM. Cada función arma asunto, cuerpo y destinatarios y los
 * entrega a sendMail(), que registra el envío en notificaciones_log, lo
 * reintenta y alerta a desarrollo si falla (App\Services\Notificador).
 *
 * Los destinatarios por área salen de la tabla notificacion_destinatarios
 * (NotificacionDestinatario::area('compras'), etc.) y los aprobadores de los
 * permisos (Permiso::usuariosCon('validar-nomina'), etc.).
 */
trait Email
{
    /* ==================================================================== */
    /*  Envío                                                               */
    /* ==================================================================== */

    /**
     * @param array|string|null $attachment  ruta en storage ('public/x.pdf') o lista [['ruta' => ..., 'nombre' => ...]]
     * @param array $opciones  'html_completo' => true (el cuerpo ya es un HTML entero), 'ref' => 'oc:12'
     */
    public function sendMail($subject, $body, $altBody = null, $params = null, $recipients = [], $cc = [], $attachment = null, array $opciones = [])
    {
        $evento = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'sendMail';
        $html = !empty($opciones['html_completo'])
            ? $body
            : view('mails.presupuestos', ['body' => $body, 'recipients' => $recipients])->render();

        $adjuntos = [];
        foreach ((array) $attachment as $a) {
            if (is_array($a) && !empty($a['ruta'])) { $adjuntos[] = ['ruta' => $a['ruta'], 'nombre' => $a['nombre'] ?? basename($a['ruta'])]; }
            elseif (is_string($a) && $a !== '') { $adjuntos[] = ['ruta' => $a, 'nombre' => basename($a)]; }
        }

        return Notificador::correo($evento, (string) $subject, $html, (array) $recipients, (array) $cc, $adjuntos, $opciones['ref'] ?? null, $altBody);
    }

    /* ---- Utilidades de destinatarios y contexto ---- */

    private function destUsuario($user): array
    {
        return ($user && !empty($user->email)) ? [['name' => $user->name ?? '', 'email' => $user->email]] : [];
    }

    /** Datos legibles de una orden de compra (jurídica, natural o nómina) para los cuerpos de correo. */
    private function ctxOrden($orden): array
    {
        $presupuesto = $orden->presupuesto ?? null;
        $gestion = optional($presupuesto)->gestion;
        return [
            'proyecto' => optional($gestion)->nom_proyecto_cot ?: 'sin nombre',
            'cc' => optional($presupuesto)->cod_cc ?: '—',
            'productor' => optional(optional($presupuesto)->productor_info),
            'comercial' => optional(optional($gestion)->comercial),
            'proveedor' => optional($orden->proveedor ?? null),
            'tipo' => ((int) ($orden->tipo_oc ?? 0) === 3) ? 'orden de nómina' : 'orden de compra',
            'codigo' => $orden->cod_oc ? "OC {$orden->cod_oc}" : "orden #{$orden->id}",
        ];
    }

    /** Datos legibles de un anticipo (jurídico, con o sin orden de compra, o de productor). */
    private function ctxAnticipo($anticipo): array
    {
        $orden = $anticipo->ordenCompra ?? null;
        $presupuesto = $anticipo->presupuesto ?? ($orden->presupuesto ?? null);
        $gestion = optional($presupuesto)->gestion;
        $productor = $anticipo->productor_info ?? optional($presupuesto)->productor_info;
        $proveedor = $anticipo->proveedor ?? optional($orden)->proveedor;
        if (method_exists($anticipo, 'esJuridico') && $anticipo->esJuridico()) {
            $tipo = $orden ? 'anticipo jurídico sobre la orden de compra '.($orden->cod_oc ?: '#'.$orden->id) : 'anticipo jurídico (sin orden de compra)';
            if ($proveedor) { $tipo .= ' para '.e($proveedor->tercero); }
        } else {
            $tipo = 'anticipo de productor';
        }
        return [
            'orden' => $orden,
            'proyecto' => optional($gestion)->nom_proyecto_cot ?: 'sin nombre',
            'cc' => optional($presupuesto)->cod_cc ?: '—',
            'productor' => optional($productor),
            'comercial' => optional(optional($gestion)->comercial),
            'proveedor' => optional($proveedor),
            'total' => '$'.number_format((float) $anticipo->total_anticipo, 0, '.', ','),
            'tipo' => $tipo,
            'ref' => 'anticipo:'.$anticipo->id,
        ];
    }

    /* ==================================================================== */
    /*  PRESUPUESTOS                                                        */
    /* ==================================================================== */

    public function presupuestoValidacionLiderComercial($presto, $user)
    {
        $subject = "NOTIFICACIÓN CRM";
        if ($presto->cod_cc) {
            $body = "El presupuesto <b>{$presto->gestion->nom_proyecto_cot}</b> con centro de costos: <b>{$presto->cod_cc}</b> de <b>{$user->name}</b> fué actualizado.";
        } else {
            $body = "<b>{$user->name}</b> ha generado el presupuesto para el proyecto: <b>{$presto->gestion->nom_proyecto_cot}</b> y solicita aprobación.";
        }
        if ($presto->justificacion) {
            $body .= "<br><b>{$user->name}</b> ha realizado las siguientes observaciones: {$presto->justificacion}.";
        }
        $recipients = NotificacionDestinatario::area('lider_comercial');
        $this->sendMail($subject, $body, $subject, null, $recipients, [], null, ['ref' => 'presupuesto:'.$presto->id]);
    }

    public function presupuestoAprobacion($presto, $user){
        $subject = "NOTIFICACIÓN CRM";
        // Decisión de negocio (11-sep-2026): TODA aprobación de margen es exclusiva de Gerencia (permiso 'aprobador-margen').
        $recipients = Permiso::usuariosCon('aprobador-margen');
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
        $this->sendMail($subject, $body, $subject, null, $recipients, [], null, ['ref' => 'presupuesto:'.$presto->id]);
    }

    public function presupuestoAprobado($user, $gestion, $justificacion, $cod_cc = null){
        $subject = "PRESUPUESTO ".$gestion->nom_proyecto_cot." APROBADO";
        $body = "El presupuesto del proyecto: <b>{$gestion->nom_proyecto_cot}</b> ha sido APROBADO con el siguiente centro de costos: <b>{$cod_cc}</b>.";
        if ($justificacion){
            $body .= "<br>El equipo de compras ha realizado las siguientes observaciones: {$justificacion}.";
        }
        $altBody = "Se ha Aprobado el presupuesto: ".$gestion->nom_proyecto_cot;
        $recipients = [];
        // Copia a los ejecutivos/asistentes del comercial
        $cc = [];
        foreach ($user->asistente as $asistenteRel) {
            if ($asistenteRel->ejecutivo) {
                $cc[] = ['name' => $asistenteRel->ejecutivo->name, 'email' => $asistenteRel->ejecutivo->email];
            }
        }
        // Los aprobadores de margen (Gerencia) reciben copia cuando el margen exigía su aprobación (menor al 30 %)
        if ($gestion->presupuesto && AprobacionPresupuesto::requiereGerencia($gestion->presupuesto)){
            array_push($recipients, ...Permiso::usuariosCon('aprobador-margen'));
        }
        array_push($recipients, ...$this->destUsuario($user));
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc, null, ['ref' => 'gestion:'.$gestion->id]);
    }

    public function presupuestoRechazado($user, $gestion, $justificacion, $cod_cc = null){
        $subject = "PRESUPUESTO ".$gestion->nom_proyecto_cot." RECHAZADO";
        $body = "El presupuesto del proyecto: <b>".$gestion->nom_proyecto_cot."</b> ha sido <b>RECHAZADO.</b>";
        $recipients = Permiso::usuariosCon('aprobador-margen');
        array_push($recipients, ...$this->destUsuario($user));
        if ($justificacion){
            $body .= "<br>El equipo de controller ha realizado las siguientes observaciones: {$justificacion}.";
        }
        $altBody = "Se ha rechazado el presupuesto: ".$gestion->nom_proyecto_cot;
        $cc = [];
        foreach ($user->asistente as $asistenteRel) {
            if ($asistenteRel->ejecutivo) {
                $cc[] = ['name' => $asistenteRel->ejecutivo->name, 'email' => $asistenteRel->ejecutivo->email];
            }
        }
        $this->sendMail($subject, $body, $altBody, null, $recipients, $cc, null, ['ref' => 'gestion:'.$gestion->id]);
    }

    public function actualizacionControllerPresupuesto($presupuesto)
    {
        $recipients = NotificacionDestinatario::area('controller');
        $subject = "NOTIFICACIÓN BULLCRM - PRESUPUESTO PROYECTO #" . $presupuesto->cod_cc . " ACTUALIZADO / MODIFICADO";
        $body = "
        <p>
            El presupuesto del proyecto <b>#" . $presupuesto->cod_cc . "</b> ha sido <b>MODIFICADO / ACTUALIZADO</b>. <br>
            Fue aprobado por: <b>" . (auth()->user()->name ?? 'Usuario del Sistema') . "</b>.<br><br>
            Por favor ingresa a la plataforma para revisar el detalle de las modificaciones.
        </p>";
        $altBody = "PRESUPUESTO #" . $presupuesto->cod_cc . " ACTUALIZADO / MODIFICADO.";
        $this->sendMail($subject, $body, $altBody, null, $recipients, [], null, ['ref' => 'presupuesto:'.$presupuesto->id]);
    }

    /* ==================================================================== */
    /*  ÓRDENES DE TRABAJO NATURALES (terceros)                             */
    /* ==================================================================== */

    private function nombreTercero($orden): string
    {
        $t = optional(optional($orden->naturalInfo)->tercero);
        return trim(($t->nombre ?? '').' '.($t->apellido ?? '')) ?: 'TERCERO DESCONOCIDO';
    }

    public function ocNaturalFirmada($orden){
        $tercero = $this->nombreTercero($orden);
        $subject = "NOTIFICACIÓN BULLCRM - ORDEN DE TRABAJO {$tercero} FIRMADA";
        $body = "<p>La orden de trabajo de <b>{$tercero}</b> ha sido <b>FIRMADA.</b> <br>Revisa el real ejecutado y confirma que la información esté correctamente diligenciada.</p>";
        $recipients = $this->destUsuario(optional($orden->naturalInfo)->productor);
        $cc = NotificacionDestinatario::area('produccion');
        $this->sendMail($subject, $body, "ORDEN DE TRABAJO {$tercero} FIRMADA.", null, $recipients, $cc, null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocNaturalEvidenciasEnviadas($orden){
        $tercero = $this->nombreTercero($orden);
        $subject = "NOTIFICACIÓN BULLCRM - EVIDENCIAS ORDEN DE TRABAJO {$tercero} ENVIADAS";
        $body = "<p>Las evidencias de la orden de trabajo de <b>{$tercero}</b> han sido <b>ENVIADAS.</b><br>Revisa y confirma que la información esté correctamente diligenciada.</p>";
        $recipients = $this->destUsuario(optional($orden->naturalInfo)->productor);
        $cc = NotificacionDestinatario::area('produccion');
        $this->sendMail($subject, $body, "EVIDENCIAS ORDEN DE TRABAJO {$tercero} ENVIADAS", null, $recipients, $cc, null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocNaturalEvidenciasRechazadas($orden){
        $tercero = $this->nombreTercero($orden);
        $subject = "NOTIFICACIÓN BULLCRM - EVIDENCIAS ORDEN DE TRABAJO {$tercero} RECHAZADAS";
        $body = "<p>Las evidencias de la orden de trabajo de <b>{$tercero}</b> han sido <b>RECHAZADAS</b> por el equipo Controller.<br>Notifica al tercero que debe adjuntar nuevamente las evidencias de la orden de trabajo.</p>";
        $recipients = $this->destUsuario(optional($orden->naturalInfo)->productor);
        $cc = NotificacionDestinatario::area('produccion');
        $this->sendMail($subject, $body, "EVIDENCIAS ORDEN DE TRABAJO {$tercero} RECHAZADAS", null, $recipients, $cc, null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocNaturalRevisionController($orden){
        $tercero = $this->nombreTercero($orden);
        $productor = optional(optional($orden->naturalInfo)->productor)->name ?: 'PRODUCTOR DESCONOCIDO';
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE {$productor} POR REVISAR";
        $body = "<p>La orden de compra del tercero <b>{$tercero}</b> ha sido validada por el productor: {$productor}<br>Revisa y confirma que la información esté correctamente diligenciada.</p>";
        $recipients = NotificacionDestinatario::area('controller');
        $this->sendMail($subject, $body, "ORDEN DE COMPRA {$tercero} POR REVISAR", null, $recipients, [], null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocNaturalRevisionContabilidad($orden){
        $tercero = $this->nombreTercero($orden);
        $productor = optional(optional($orden->naturalInfo)->productor)->name ?: 'PRODUCTOR DESCONOCIDO';
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE {$productor} POR REVISAR";
        $body = "<p>La orden de compra del tercero <b>{$tercero}</b> ha sido validada por el equipo Controller.<br>Revisa y confirma que la información esté correctamente diligenciada.</p>";
        $recipients = NotificacionDestinatario::area('contabilidad');
        $this->sendMail($subject, $body, "ORDEN DE COMPRA {$tercero} POR REVISAR", null, $recipients, [], null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocNaturalRevisionTesoreria($orden){
        // Contabilidad causa órdenes de todo tipo: en las jurídicas y de nómina el nombre es el del proveedor
        // y el productor el del proyecto (antes salía "TERCERO/PRODUCTOR DESCONOCIDO").
        $esNatural = (int) $orden->tipo_oc === 2;
        $tercero = $esNatural ? $this->nombreTercero($orden) : (optional($orden->proveedor)->tercero ?: 'PROVEEDOR DESCONOCIDO');
        $productor = ($esNatural
            ? optional(optional($orden->naturalInfo)->productor)->name
            : optional(optional($orden->presupuesto)->productor_info)->name) ?: 'PRODUCTOR DESCONOCIDO';
        $subject = "NOTIFICACIÓN BULLCRM - TIENES UNA ORDEN DE COMPRA DE {$productor} POR REVISAR";
        $body = "<p>La orden de compra del tercero <b>{$tercero}</b> ha sido causada por contabilidad.<br>Revisa y confirma que la información esté correctamente diligenciada.</p>";
        $recipients = NotificacionDestinatario::area('tesoreria');
        $this->sendMail($subject, $body, "ORDEN DE COMPRA {$tercero} POR REVISAR", null, $recipients, [], null, ['ref' => 'oc:'.$orden->id]);
    }

    /* ==================================================================== */
    /*  ÓRDENES DE COMPRA JURÍDICAS (con plantilla)                         */
    /* ==================================================================== */

    /** Destinatarios estándar de una OC jurídica: compras, comercial, productor; cc compras_cc y proveedor. */
    private function destOrdenJuridica($orden, bool $conProveedor = true): array
    {
        $c = $this->ctxOrden($orden);
        $para = NotificacionDestinatario::area('compras');
        array_push($para, ...$this->destUsuario($c['comercial']), ...$this->destUsuario($c['productor']));
        $cc = NotificacionDestinatario::area('compras_cc');
        if ($conProveedor && !empty($c['proveedor']->correo)) {
            $cc[] = ['name' => $c['proveedor']->contacto ?? '', 'email' => $c['proveedor']->correo];
        }
        return [$para, $cc];
    }

    public function mailOrdenAprobada($orden){
        [$para, $cc] = $this->destOrdenJuridica($orden);
        if ((float) optional($orden->proveedor)->anticipo > 0) {
            array_push($cc, ...NotificacionDestinatario::area('contabilidad_pagos'));
        }
        $adj = $orden->archivo_orden_helisa ? [['ruta' => $orden->archivo_orden_helisa, 'nombre' => 'OC_'.optional($orden->proveedor)->tercero.'.pdf']] : [];
        $this->sendMail("OC: {$orden->cod_oc} ".optional($orden->proveedor)->tercero, view('mails.ordenAprobada', ['orden' => $orden])->render(),
            "Se ha generado la orden de compra: {$orden->cod_oc} para el proveedor ".optional($orden->proveedor)->tercero, null, $para, $cc, $adj, ['html_completo' => true, 'ref' => 'oc:'.$orden->id]);
    }

    public function mailGrGenerado($orden){
        [$para, $cc] = $this->destOrdenJuridica($orden);
        $adj = [];
        if ($orden->archivo_orden_helisa) { $adj[] = ['ruta' => $orden->archivo_orden_helisa, 'nombre' => 'OC_'.optional($orden->proveedor)->tercero.'.pdf']; }
        if ($orden->archivo_remision) { $adj[] = ['ruta' => $orden->archivo_remision, 'nombre' => 'REMISION_'.optional($orden->proveedor)->tercero.'.pdf']; }
        $this->sendMail("OC: {$orden->cod_oc} ".optional($orden->proveedor)->tercero, view('mails.grGenerado', ['orden' => $orden])->render(),
            "Se ha asignado el GR: {$orden->gr} para la orden de compra {$orden->cod_oc}", null, $para, $cc, $adj, ['html_completo' => true, 'ref' => 'oc:'.$orden->id]);
    }

    public function mailOrdenAnulada($orden){
        [$para, $cc] = $this->destOrdenJuridica($orden, false);
        $this->sendMail("OC: {$orden->cod_oc} ".optional($orden->proveedor)->tercero, view('mails.ordenAnulada', ['orden' => $orden])->render(),
            "Se ha anulado la orden de compra {$orden->cod_oc}", null, $para, $cc, [], ['html_completo' => true, 'ref' => 'oc:'.$orden->id]);
    }

    /** Compatibilidad: antes recibía la orden; ahora el aviso de pago se hace por anticipo (anticipoPagado). */
    /**
     * Tesorería registró el pago de una orden de compra: compras, el comercial y el productor, con copia a
     * contabilidad y al proveedor (o al tercero en una orden natural), con el comprobante adjunto.
     * (Se había cambiado por el aviso del anticipo jurídico y el pago de órdenes dejó de avisar a nadie.)
     */
    public function mailAnticipoPagado($orden, $observaciones){
        $c = $this->ctxOrden($orden);
        $productor = $c['productor'];
        $destinoProveedor = [];
        if ((int) $orden->tipo_oc === 2 && $orden->naturalInfo) {
            $productor = optional($orden->naturalInfo->productor);
            $tercero = $orden->naturalInfo->tercero;
            if ($tercero && !empty($tercero->correo)) { $destinoProveedor[] = ['name' => trim($tercero->nombre.' '.$tercero->apellido), 'email' => $tercero->correo]; }
        } elseif (!empty($c['proveedor']->correo)) {
            $destinoProveedor[] = ['name' => $c['proveedor']->contacto ?? '', 'email' => $c['proveedor']->correo];
        }
        $para = array_merge(NotificacionDestinatario::area('compras'), $this->destUsuario($c['comercial']), $this->destUsuario($productor));
        $cc = array_merge(NotificacionDestinatario::area('compras_cc'), NotificacionDestinatario::area('contabilidad_pagos'), $destinoProveedor);
        $nombreProveedor = $c['proveedor']->tercero ?: 'proveedor';
        $adj = $orden->archivo_comprobante_pago
            ? [['ruta' => $orden->archivo_comprobante_pago, 'nombre' => "COMPROBANTE_PAGO {$c['codigo']} {$nombreProveedor}.pdf"]] : [];
        $html = view('mails.anticipoPagado', ['orden' => $orden, 'observaciones' => $observaciones])->render();
        $this->sendMail("OC: ".($orden->cod_oc ?: '#'.$orden->id)." ".$nombreProveedor, $html,
            "Se registró el pago de la {$c['codigo']} para {$nombreProveedor}", null, $para, $cc, $adj,
            ['ref' => 'orden:'.$orden->id, 'html_completo' => true]);
    }

    /* ==================================================================== */
    /*  ÓRDENES DE NÓMINA (flujo líder -> gerencia -> controller -> admin)    */
    /* ==================================================================== */

    private function cuerpoNomina($orden, string $frase): string
    {
        $c = $this->ctxOrden($orden);
        return "<p>La orden de nómina <b>#{$orden->id}</b>".($orden->cod_oc ? " (OC {$orden->cod_oc})" : '')." del proyecto <b>{$c['proyecto']}</b> (centro de costos <b>{$c['cc']}</b>) de <b>".($c['productor']->name ?: 'el productor')."</b> {$frase}</p>";
    }

    public function ocJuridicaRevisionLiderProd($orden){
        $this->sendMail("NOTIFICACIÓN CRM - Nómina #{$orden->id} para revisión del líder de producción", $this->cuerpoNomina($orden, 'fue enviada y espera revisión del líder de producción.'),
            null, null, NotificacionDestinatario::area('produccion'), [], null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaRevisionGerencia($orden){
        $para = Permiso::usuariosCon('validar-nomina');
        $this->sendMail("NOTIFICACIÓN CRM - Nómina #{$orden->id} para validación de gerencia", $this->cuerpoNomina($orden, 'fue aprobada por el líder de producción y espera validación de gerencia.'),
            null, null, $para, NotificacionDestinatario::area('produccion'), null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaRevisionController($orden){
        $this->sendMail("NOTIFICACIÓN CRM - Nómina #{$orden->id} para revisión de Controller", $this->cuerpoNomina($orden, 'fue validada y está lista para revisión de Controller.'),
            null, null, NotificacionDestinatario::area('controller'), [], null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaRechazoLiderProd($orden){
        $c = $this->ctxOrden($orden);
        $this->sendMail("NOTIFICACIÓN CRM - Nómina #{$orden->id} rechazada por el líder de producción", $this->cuerpoNomina($orden, 'fue <b>rechazada</b> por el líder de producción. Motivo: '.e($orden->rechazo_revision_lider ?: '—').'. Corrígela y vuelve a enviarla.'),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaRechazoGerencia($orden){
        $c = $this->ctxOrden($orden);
        $motivo = $orden->rechazo_revision_gerencia ?: ($orden->rechazo_revision_evidencias ?: '—');
        $this->sendMail("NOTIFICACIÓN CRM - Nómina #{$orden->id} rechazada", $this->cuerpoNomina($orden, 'fue <b>rechazada</b>. Motivo: '.e($motivo).'. Corrígela y vuelve a enviarla.'),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaAprobada($orden){
        $c = $this->ctxOrden($orden);
        $para = array_merge(NotificacionDestinatario::area('compras'), $this->destUsuario($c['productor']), $this->destUsuario($c['comercial']));
        $adj = $orden->archivo_orden_helisa ? [['ruta' => $orden->archivo_orden_helisa, 'nombre' => "NOMINA_{$orden->id}.pdf"]] : [];
        $this->sendMail("NOTIFICACIÓN CRM - Nómina #{$orden->id} aprobada".($orden->cod_oc ? " (OC {$orden->cod_oc})" : ''), $this->cuerpoNomina($orden, 'fue <b>aprobada</b>'.($orden->cod_oc ? " con el código {$orden->cod_oc}" : '').'. Se adjunta la orden.'),
            null, null, $para, NotificacionDestinatario::area('compras_cc'), $adj, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaRevisionRemiController($orden){
        $adj = $orden->archivo_remision ? [['ruta' => $orden->archivo_remision, 'nombre' => "REMISION_NOMINA_{$orden->id}.pdf"]] : [];
        $this->sendMail("NOTIFICACIÓN CRM - Remisión de la nómina #{$orden->id} para revisión de Controller", $this->cuerpoNomina($orden, 'tiene la remisión firmada y revisada por el líder de producción; falta la aprobación de Controller.'),
            null, null, NotificacionDestinatario::area('controller'), [], $adj, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaGrGenerado($orden){
        $c = $this->ctxOrden($orden);
        $para = array_merge(NotificacionDestinatario::area('compras'), $this->destUsuario($c['productor']), $this->destUsuario($c['comercial']));
        $this->sendMail("NOTIFICACIÓN CRM - GR {$orden->gr} generado para la nómina #{$orden->id}", $this->cuerpoNomina($orden, "quedó comprobada con el GR <b>{$orden->gr}</b>."),
            null, null, $para, NotificacionDestinatario::area('contabilidad_pagos'), null, ['ref' => 'oc:'.$orden->id]);
    }

    public function ocJuridicaAnulada($orden){
        $c = $this->ctxOrden($orden);
        $para = array_merge(NotificacionDestinatario::area('compras'), $this->destUsuario($c['productor']), $this->destUsuario($c['comercial']));
        $this->sendMail("NOTIFICACIÓN CRM - Nómina #{$orden->id} anulada", $this->cuerpoNomina($orden, 'fue <b>anulada</b>. Motivo: '.e($orden->observaciones_anulacion ?: '—').'.'),
            null, null, $para, NotificacionDestinatario::area('compras_cc'), null, ['ref' => 'oc:'.$orden->id]);
    }

    /* ==================================================================== */
    /*  ANTICIPOS                                                           */
    /* ==================================================================== */

    private function cuerpoAnticipo($anticipo, string $frase): string
    {
        $c = $this->ctxAnticipo($anticipo);
        return "<p>El {$c['tipo']} por <b>{$c['total']}</b> del proyecto <b>{$c['proyecto']}</b> (centro de costos <b>{$c['cc']}</b>) de <b>".($c['productor']->name ?: 'el productor')."</b> {$frase}</p>";
    }

    /** Productor crea o corrige su anticipo: avisa a los líderes de producción. */
    public function anticipoProdRevisionLiderProd($anticipo){
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo de productor para revisión del líder", $this->cuerpoAnticipo($anticipo, 'espera revisión del líder de producción.'),
            null, null, NotificacionDestinatario::area('produccion'), [], null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Líder aprueba y el monto exige gerencia. */
    public function anticipoProdRevisionGerencia($anticipo){
        $para = Permiso::usuariosCon('revisar-anticipos-gerencia');
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo de productor para revisión de gerencia", $this->cuerpoAnticipo($anticipo, 'fue aprobado por el líder de producción y, por su monto, espera revisión de gerencia.'),
            null, null, $para, NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Aprobado (líder o gerencia): el productor ya puede legalizar con evidencias. */
    public function anticipoProdEvidencias($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $obs = $anticipo->observaciones_revision_gerencia ?: $anticipo->observaciones_revision_lider;
        $this->sendMail("NOTIFICACIÓN CRM - Tu anticipo fue aprobado: carga las evidencias", $this->cuerpoAnticipo($anticipo, 'fue <b>aprobado</b>'.($obs ? ' con la observación: '.e($obs) : '').'. Ingresa al CRM para cargar las evidencias de cada ítem.'),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    public function anticipoProdRechazoLiderProd($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Tu anticipo fue rechazado por el líder de producción", $this->cuerpoAnticipo($anticipo, 'fue <b>rechazado</b> por el líder de producción. Motivo: '.e($anticipo->rechazo_revision_lider ?: '—').'. Corrígelo y vuelve a enviarlo.'),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    public function anticipoProdRechazoGerencia($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Tu anticipo fue rechazado por gerencia", $this->cuerpoAnticipo($anticipo, 'fue <b>rechazado</b> por gerencia. Motivo: '.e($anticipo->rechazo_revision_gerencia ?: '—').'. Corrígelo y vuelve a enviarlo.'),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** El líder aprobó las evidencias: queda aprobado y Contabilidad debe causarlo. */
    public function anticipoProdEvidenciasAprobadas($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $obs = $anticipo->observaciones_revision_evidencias;
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo de productor con evidencias aprobadas: pendiente de causar",
            $this->cuerpoAnticipo($anticipo, 'tiene las evidencias <b>aprobadas</b> por el líder de producción'.($obs ? ' (observación: '.e($obs).')' : '').' y está pendiente de causar.')
                .$this->enlacesAnticipo(['detalle-anticipo-contabilidad' => 'Contabilidad', 'anticipo-prod' => 'Productor'], $anticipo),
            null, null, NotificacionDestinatario::area('contabilidad'), array_merge($this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion')), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** El líder devolvió las evidencias: el productor debe corregirlas. */
    public function anticipoProdRechazoEvidencias($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Tus evidencias fueron devueltas por el líder de producción",
            $this->cuerpoAnticipo($anticipo, 'tiene las evidencias <b>devueltas</b> por el líder de producción. Motivo: '.e($anticipo->rechazo_revision_evidencias ?: '—').'. Corrígelas y vuelve a enviarlas.')
                .$this->enlaceAnticipo('anticipo-prod', $anticipo),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Contabilidad causó el anticipo: tesorería debe pagarlo. */
    public function anticipoCausado($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo causado, pendiente de pago", $this->cuerpoAnticipo($anticipo, 'fue <b>causado</b> por contabilidad (código '.e($anticipo->cod_causal ?: '—').') y está pendiente de pago.'),
            null, null, NotificacionDestinatario::area('tesoreria'), array_merge(NotificacionDestinatario::area('contabilidad_pagos'), $this->destUsuario($c['productor'])), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Contabilidad rechazó la causación. */
    public function anticipoRechazoContabilidad($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo rechazado por contabilidad", $this->cuerpoAnticipo($anticipo, 'fue <b>devuelto</b> por Contabilidad. Motivo: '.e($anticipo->observacion_causal ?: '—').'. Revisa las evidencias, corrige lo que haga falta y vuelve a enviarlas.').$this->enlaceAnticipo('anticipo-prod', $anticipo),
            null, null, array_merge($this->destUsuario($c['productor']), NotificacionDestinatario::area('compras')), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /* ---- Anticipo jurídico (matriz de Gerencia, sep-2026): App\Services\AnticipoJuridicoFlujo ---- */

    /** Última observación del recorrido (el motivo de una devolución, o lo que el productor corrigió). */
    private function obsAnticipo($anticipo): string
    {
        // reorder(): la relación ya viene ordenada de la más antigua a la más reciente
        $paso = $anticipo->pasos()->reorder()->orderByDesc('id')->first();
        return ($paso && $paso->observacion) ? e($paso->observacion) : '—';
    }

    private function enlaceAnticipo(string $ruta, $anticipo): string
    {
        return '<p><a href="'.route($ruta, ['anticipo_id' => $anticipo->id]).'">Abrir el anticipo en el CRM</a></p>';
    }

    /** Un enlace por destinatario: cada rol solo abre el anticipo por su propia ruta. */
    private function enlacesAnticipo(array $rutas, $anticipo): string
    {
        $links = [];
        foreach ($rutas as $ruta => $quien) {
            $links[] = '<a href="'.route($ruta, ['anticipo_id' => $anticipo->id]).'">'.$quien.'</a>';
        }
        return '<p>Abrir el anticipo en el CRM: '.implode(' · ', $links).'</p>';
    }

    /** Productor lo solicita o lo reenvía corregido: revisa el líder de producción. */
    public function ajRevisionLider($anticipo){
        $reenvio = $anticipo->pasos()->where('accion', 'reenviar')->exists();
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo jurídico para revisión del líder de producción",
            $this->cuerpoAnticipo($anticipo, $reenvio ? 'fue corregido y reenviado. Lo que corrigió: '.$this->obsAnticipo($anticipo).'. Espera revisión del líder de producción.' : 'espera revisión del líder de producción.')
            .$this->enlaceAnticipo('anticipo-lid', $anticipo),
            null, null, NotificacionDestinatario::area('produccion'), [], null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Líder aprobó: revisa el Controller. */
    public function ajRevisionController($anticipo){
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo jurídico para revisión del Controller",
            $this->cuerpoAnticipo($anticipo, 'fue aprobado por el líder de producción y espera revisión del Controller.').$this->enlaceAnticipo('anticipo-controller', $anticipo),
            null, null, NotificacionDestinatario::area('controller'), [], null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Controller aprobó y el valor supera el umbral: aprueba Gerencia. */
    public function ajRevisionGerencia($anticipo){
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo jurídico para aprobación de Gerencia",
            $this->cuerpoAnticipo($anticipo, 'fue aprobado por el líder de producción y el Controller y, por superar $'.number_format(\App\Services\AnticipoJuridicoFlujo::umbral(), 0, '.', ',').', espera aprobación de Gerencia.').$this->enlaceAnticipo('anticipo-admin', $anticipo),
            null, null, Permiso::usuariosCon('revisar-anticipos-gerencia'), NotificacionDestinatario::area('controller'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Aprobado: Contabilidad lo causa con la orden de anticipo adjunta; el productor queda enterado. */
    public function ajAprobado($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $adj = $anticipo->archivo_orden_anticipo ? [['ruta' => $anticipo->archivo_orden_anticipo, 'nombre' => "ORDEN_DE_ANTICIPO_{$anticipo->cod_orden_anticipo}.pdf"]] : [];
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo jurídico aprobado".($anticipo->cod_orden_anticipo ? " ({$anticipo->cod_orden_anticipo})" : '').": pendiente de causar",
            $this->cuerpoAnticipo($anticipo, 'fue <b>aprobado</b> y está pendiente de causar. Se adjunta la orden de anticipo.')
                .$this->enlacesAnticipo(['detalle-anticipo-contabilidad' => 'Contabilidad', 'anticipo-prod' => 'Productor', 'anticipo-controller' => 'Controller'], $anticipo),
            null, null, array_merge(NotificacionDestinatario::area('contabilidad'), $this->destUsuario($c['productor'])), NotificacionDestinatario::area('controller'), $adj, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    public function ajRechazoLider($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Tu anticipo jurídico fue devuelto por el líder de producción",
            $this->cuerpoAnticipo($anticipo, 'fue <b>devuelto</b> por el líder de producción. Motivo: '.$this->obsAnticipo($anticipo).'. Corrígelo y vuelve a enviarlo.').$this->enlaceAnticipo('anticipo-prod', $anticipo),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    public function ajRechazoController($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Tu anticipo jurídico fue devuelto por el Controller",
            $this->cuerpoAnticipo($anticipo, 'fue <b>devuelto</b> por el Controller. Motivo: '.$this->obsAnticipo($anticipo).'. Corrígelo y vuelve a enviarlo.').$this->enlaceAnticipo('anticipo-prod', $anticipo),
            null, null, $this->destUsuario($c['productor']), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    public function ajRechazoGerencia($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Tu anticipo jurídico fue devuelto por Gerencia",
            $this->cuerpoAnticipo($anticipo, 'fue <b>devuelto</b> por Gerencia. Motivo: '.$this->obsAnticipo($anticipo).'. Corrígelo y vuelve a enviarlo.').$this->enlaceAnticipo('anticipo-prod', $anticipo),
            null, null, $this->destUsuario($c['productor']), array_merge(NotificacionDestinatario::area('produccion'), NotificacionDestinatario::area('controller')), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Contabilidad lo devuelve: lo revisa de nuevo el Controller (no el productor). */
    public function ajRechazoContabilidad($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo jurídico devuelto por Contabilidad",
            $this->cuerpoAnticipo($anticipo, 'fue <b>devuelto</b> por Contabilidad al Controller. Motivo: '.e($anticipo->observaciones_contabilidad ?: '—').'.')
                .$this->enlacesAnticipo(['anticipo-controller' => 'Controller', 'anticipo-prod' => 'Productor'], $anticipo),
            null, null, NotificacionDestinatario::area('controller'), $this->destUsuario($c['productor']), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Anulado por el Controller, retirado por el productor o porque se anuló su orden de compra. */
    public function ajAnulado($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $cc = NotificacionDestinatario::area('produccion');
        // Si ya estaba aprobado, Contabilidad lo tenía en su bandeja
        if ($anticipo->fecha_aprobacion) { $cc = array_merge($cc, NotificacionDestinatario::area('contabilidad')); }
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo jurídico anulado".($anticipo->cod_orden_anticipo ? " ({$anticipo->cod_orden_anticipo})" : ''),
            $this->cuerpoAnticipo($anticipo, 'fue <b>anulado</b>. Motivo: '.$this->obsAnticipo($anticipo).'.')
                .$this->enlacesAnticipo(['anticipo-prod' => 'Productor', 'anticipo-controller' => 'Controller'], $anticipo),
            null, null, array_merge($this->destUsuario($c['productor']), NotificacionDestinatario::area('controller')), $cc, null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Se anuló la orden de compra de un anticipo ya causado o pagado: Contabilidad y Tesorería deben revisarlo. */
    public function ajOrdenAnuladaConAnticipo($anticipo){
        $this->sendMail("NOTIFICACIÓN CRM - Se anuló la orden de compra de un anticipo ya ".((int) $anticipo->estado_id === 14 ? 'pagado' : 'causado'),
            $this->cuerpoAnticipo($anticipo, 'ya estaba <b>'.((int) $anticipo->estado_id === 14 ? 'pagado' : 'causado').'</b> y su orden de compra fue anulada. Revisen si hay que reversar la causación o recuperar el pago.'),
            null, null, array_merge(NotificacionDestinatario::area('contabilidad'), NotificacionDestinatario::area('tesoreria')), NotificacionDestinatario::area('controller'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Tesorería subió el comprobante: el anticipo está pagado. */
    public function anticipoPagado($anticipo, $observaciones = null){
        $c = $this->ctxAnticipo($anticipo);
        $para = array_merge(NotificacionDestinatario::area('compras'), $this->destUsuario($c['productor']), $this->destUsuario($c['comercial']));
        $cc = array_merge(NotificacionDestinatario::area('compras_cc'), NotificacionDestinatario::area('contabilidad_pagos'));
        if (!empty($c['proveedor']->correo)) { $cc[] = ['name' => $c['proveedor']->contacto ?? '', 'email' => $c['proveedor']->correo]; }
        $adj = $anticipo->comprobante_pago ? [['ruta' => $anticipo->comprobante_pago, 'nombre' => "COMPROBANTE_PAGO_ANTICIPO_{$anticipo->id}.pdf"]] : [];
        $body = $this->cuerpoAnticipo($anticipo, 'fue <b>pagado</b> por tesorería'.($observaciones ? '. Observaciones: '.e($observaciones) : '').'. Se adjunta el comprobante.');
        $ref = ($c['orden'] && $c['orden']->cod_oc) ? " (OC {$c['orden']->cod_oc})" : ($anticipo->cod_orden_anticipo ? " ({$anticipo->cod_orden_anticipo})" : '');
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo pagado".$ref, $body, null, null, $para, $cc, $adj, ['ref' => 'anticipo:'.$anticipo->id]);
    }
}
