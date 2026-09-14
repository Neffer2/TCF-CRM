<?php

namespace App\Traits;

use App\Models\NotificacionDestinatario;
use App\Models\Permiso;
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

    /** Datos legibles de un anticipo (jurídico, natural o de productor). */
    private function ctxAnticipo($anticipo): array
    {
        $orden = $anticipo->ordenCompra ?? null;
        $presupuesto = $anticipo->presupuesto ?? ($orden->presupuesto ?? null);
        $gestion = optional($presupuesto)->gestion;
        $productor = $anticipo->productor_info ?? optional($presupuesto)->productor_info;
        return [
            'orden' => $orden,
            'proyecto' => optional($gestion)->nom_proyecto_cot ?: 'sin nombre',
            'cc' => optional($presupuesto)->cod_cc ?: '—',
            'productor' => optional($productor),
            'comercial' => optional(optional($gestion)->comercial),
            'proveedor' => optional(optional($orden)->proveedor),
            'total' => '$'.number_format((float) $anticipo->total_anticipo, 0, '.', ','),
            'tipo' => $orden ? ('anticipo sobre la '.((int) $orden->tipo_oc === 2 ? 'orden natural' : 'orden de compra').' '.($orden->cod_oc ?: '#'.$orden->id)) : 'anticipo de productor',
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
        // Los aprobadores de margen (Gerencia) reciben copia cuando el margen estuvo en la frontera (<= 35 %)
        if (optional($gestion->presupuesto)->margen_proy <= 35){
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
        $tercero = $this->nombreTercero($orden);
        $productor = optional(optional($orden->naturalInfo)->productor)->name ?: 'PRODUCTOR DESCONOCIDO';
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
    public function mailAnticipoPagado($orden, $observaciones){
        $anticipo = $orden->anticipos()->latest()->first();
        if ($anticipo) { $this->anticipoPagado($anticipo, $observaciones); }
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

    /** Contabilidad causó el anticipo: tesorería debe pagarlo. */
    public function anticipoCausado($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo causado, pendiente de pago", $this->cuerpoAnticipo($anticipo, 'fue <b>causado</b> por contabilidad (código '.e($anticipo->cod_causal ?: '—').') y está pendiente de pago.'),
            null, null, NotificacionDestinatario::area('tesoreria'), array_merge(NotificacionDestinatario::area('contabilidad_pagos'), $this->destUsuario($c['productor'])), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Contabilidad rechazó la causación. */
    public function anticipoRechazoContabilidad($anticipo){
        $c = $this->ctxAnticipo($anticipo);
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo rechazado por contabilidad", $this->cuerpoAnticipo($anticipo, 'fue <b>rechazado</b> por contabilidad. Motivo: '.e($anticipo->observacion_causal ?: '—').'.'),
            null, null, array_merge($this->destUsuario($c['productor']), NotificacionDestinatario::area('compras')), NotificacionDestinatario::area('produccion'), null, ['ref' => 'anticipo:'.$anticipo->id]);
    }

    /** Tesorería subió el comprobante: el anticipo está pagado. */
    public function anticipoPagado($anticipo, $observaciones = null){
        $c = $this->ctxAnticipo($anticipo);
        $para = array_merge(NotificacionDestinatario::area('compras'), $this->destUsuario($c['productor']), $this->destUsuario($c['comercial']));
        $cc = array_merge(NotificacionDestinatario::area('compras_cc'), NotificacionDestinatario::area('contabilidad_pagos'));
        if (!empty($c['proveedor']->correo)) { $cc[] = ['name' => $c['proveedor']->contacto ?? '', 'email' => $c['proveedor']->correo]; }
        $adj = $anticipo->comprobante_pago ? [['ruta' => $anticipo->comprobante_pago, 'nombre' => "COMPROBANTE_PAGO_ANTICIPO_{$anticipo->id}.pdf"]] : [];
        $body = $this->cuerpoAnticipo($anticipo, 'fue <b>pagado</b> por tesorería'.($observaciones ? '. Observaciones: '.e($observaciones) : '').'. Se adjunta el comprobante.');
        $this->sendMail("NOTIFICACIÓN CRM - Anticipo pagado".($c['orden'] && $c['orden']->cod_oc ? " (OC {$c['orden']->cod_oc})" : ''), $body, null, null, $para, $cc, $adj, ['ref' => 'anticipo:'.$anticipo->id]);
    }
}
