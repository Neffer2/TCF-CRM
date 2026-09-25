<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Prueba de punta a punta del anticipo jurídico (matriz de Gerencia). Todo dentro de una transacción
// que se deshace al final; los archivos generados se borran.
use App\Models\Anticipo;
use App\Models\User;
use App\Models\NotificacionLog;
use App\Services\AnticipoJuridicoFlujo as F;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

$ok = 0; $mal = 0; $archivos = [];
$check = function ($cond, $txt) use (&$ok, &$mal) { if ($cond) { $ok++; echo "  ✓ $txt\n"; } else { $mal++; echo "  ✗ FALLA: $txt\n"; } };
$falla = function (callable $fn, $clave, $contiene, $txt) use ($check) {
    try { $fn(); $check(false, "$txt (no lanzó error)"); }
    catch (ValidationException $e) {
        $m = $e->errors()[$clave][0] ?? null;
        $check($m !== null && str_contains($m, $contiene), "$txt → [$clave] ".($m ?? json_encode($e->errors(), JSON_UNESCAPED_UNICODE)));
    }
};
$u = fn ($email) => User::where('email', $email)->firstOrFail();
$prod = $u('prueba.productor@local.test'); $lider = $u('prueba.lider@local.test'); $ctrl = $u('prueba.controller@local.test');
$ger = $u('prueba.gerencia@local.test'); $admin = $u('prueba.admin@local.test'); $cont = $u('prueba.contabilidad@local.test');
$teso = $u('prueba.tesoreria@local.test');
$pdf = function ($nombre) { return UploadedFile::fake()->createWithContent($nombre, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF"); };
$correos = fn ($a) => NotificacionLog::where('referencia', 'anticipo:'.$a->id)->orderBy('id')->pluck('evento')->all();
$f = new F;

DB::beginTransaction();
try {
    // Los anticipos de demostración (#4, #5) son del mismo proveedor y proyecto que la OC 11426: se anulan solo dentro de la prueba
    DB::table('anticipos')->where('tipo', 'juridico')->where('presupuesto_id', 6733)->whereNotIn('estado_id', [6, 14])->update(['estado_id' => 6]);
    echo "Umbral de Gerencia: $".number_format(F::umbral(), 0, ',', '.')."\n";

    /* ================= ESCENARIO A: con orden de compra, ≤ umbral ================= */
    echo "\nESCENARIO A · con orden de compra (OC 11426 del productor de prueba), 50 % = 6.342.500\n";
    DB::table('ordenes_compra')->where('id', 11426)->update(['estado_id' => 1]); // aprobada, solo dentro de la prueba
    $disp = F::ordenesDisponibles($prod);
    $check($disp->pluck('id')->contains(11426), 'La OC aprobada aparece entre las disponibles del productor');
    $check(!F::ordenesDisponibles($lider)->count(), 'Otro usuario no ve órdenes del productor');

    $falla(fn () => $f->solicitar($lider, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 50]), 'general', 'Solo un productor', 'El líder no puede solicitar');
    $falla(fn () => $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 0]), 'porcentaje', 'entre 1 y 100', 'Porcentaje 0 rechazado');
    $falla(fn () => $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11427, 'porcentaje' => 50]), 'oc_id', 'no está disponible', 'OC no aprobada rechazada');

    $a = $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 50, 'observacion' => 'El proveedor lo exige para reservar.']);
    $check($a->estado_id == F::REV_LIDER && $a->tipo === 'juridico', "Solicitud #{$a->id} queda en Revisión líder (8)");
    $check((float) $a->valor_base == 12685000.0 && (float) $a->total_anticipo == 6342500.0, 'Base = valor de la OC (12.685.000) y total = 6.342.500');
    $check($a->proveedor_id == 285 && $a->presupuesto_id == 6733, 'Proveedor y presupuesto copiados de la OC');
    $falla(fn () => $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 30]), 'oc_id', 'ya tiene un anticipo', 'Segunda solicitud sobre la misma OC rechazada');
    $check(!F::ordenesDisponibles($prod)->pluck('id')->contains(11426), 'La OC con anticipo ya no aparece entre las disponibles');

    $falla(fn () => $f->aprobarController($a, $ctrl), 'general', 'no está en un estado', 'Controller no puede saltarse al líder');
    $falla(fn () => $f->aprobarLider($a, $prod), 'general', 'Líder de producción', 'El productor no puede aprobarse a sí mismo');
    $falla(fn () => $f->aprobarLider($a, $admin), 'general', 'Líder de producción', 'Admin sin rol de líder no puede aprobar');

    $a = $f->aprobarLider($a, $lider, 'Cuadra con la OC.');
    $check($a->estado_id == F::REV_CONTROLLER, 'Líder aprueba → Revisión controller (15)');
    $falla(fn () => $f->aprobarGerencia($a, $ger), 'general', 'no está en un estado', 'Gerencia no actúa antes del Controller');

    $a = $f->aprobarController($a, $ctrl);
    $check($a->estado_id == F::APROBADO, 'Controller aprueba ≤ umbral → Aprobado (1), sin pasar por Gerencia');
    $check($a->cod_orden_anticipo === sprintf('OA-%06d', $a->id) && $a->archivo_orden_anticipo && Storage::exists($a->archivo_orden_anticipo), "Orden de anticipo {$a->cod_orden_anticipo} generada en PDF");
    $archivos[] = $a->archivo_orden_anticipo;
    $pdfTexto = Storage::get($a->archivo_orden_anticipo);
    $check(str_starts_with($pdfTexto, '%PDF'), 'El archivo de la orden es un PDF válido ('.number_format(strlen($pdfTexto) / 1024, 1).' KB)');
    $check($a->fecha_aprobacion !== null, 'Fecha de aprobación registrada');
    $oaFecha = $a->fecha_orden_anticipo;

    $falla(fn () => $f->devolverContabilidad($a, $cont, ''), 'observacion', 'motivo', 'Contabilidad no devuelve sin motivo');
    $a = $f->devolverContabilidad($a, $cont, 'Falta el RUT del proveedor.');
    $check($a->estado_id == F::RECH_CONTABILIDAD && $a->observaciones_contabilidad === 'Falta el RUT del proveedor.', 'Contabilidad devuelve → Rechazo contabilidad (13) con motivo');
    $falla(fn () => $f->reenviar($a, $prod, ['porcentaje' => 50, 'observacion' => 'x']), 'general', 'no está en un estado', 'El productor no lo recibe: vuelve al Controller, no a él');

    $a = $f->aprobarController($a, $ctrl, 'RUT verificado en el maestro de proveedores.');
    $check($a->estado_id == F::APROBADO, 'Controller lo aprueba otra vez → vuelve a Contabilidad (1)');
    $check($a->fecha_orden_anticipo->equalTo($oaFecha), 'La orden de anticipo no se regenera (mismo documento)');

    $falla(fn () => $f->causar($a, $cont, 'abc'), 'cod_causal', 'solo dígitos', 'Causación con letras rechazada');
    $falla(fn () => $f->causar($a, $teso, '123'), 'general', 'Contabilidad', 'Tesorería no puede causar');
    $a = $f->causar($a, $cont, '20260924', 'Causado en Helisa.');
    $check($a->estado_id == F::CAUSADO && $a->cod_causal === '20260924', 'Contabilidad causa → Causado (5)');

    $falla(fn () => $f->pagar($a, $teso, null), 'comprobante', 'PDF', 'Pago sin comprobante rechazado');
    $falla(fn () => $f->pagar($a, $teso, UploadedFile::fake()->image('foto.png')), 'comprobante', 'PDF', 'Comprobante que no es PDF rechazado');
    $a = $f->pagar($a, $teso, $pdf('comprobante.pdf'), 'Transferencia Bancolombia.');
    $archivos[] = $a->comprobante_pago;
    $check($a->estado_id == F::PAGADO && Storage::exists($a->comprobante_pago), 'Tesorería paga → Pagado (14) con comprobante');
    $falla(fn () => $f->pagar($a, $teso, $pdf('otra.pdf')), 'general', 'no está en un estado', 'No se puede pagar dos veces');

    $pasos = $a->pasos()->pluck('accion')->all();
    $check($pasos === ['solicitar', 'aprobar_lider', 'aprobar_controller', 'devolver_contabilidad', 'aprobar_controller', 'causar', 'pagar'], 'Recorrido completo: '.implode(' → ', $pasos));
    $ev = $correos($a);
    $check($ev === ['ajRevisionLider', 'ajRevisionController', 'ajAprobado', 'ajRechazoContabilidad', 'ajAprobado', 'anticipoCausado', 'anticipoPagado'], 'Correos en orden: '.implode(', ', $ev));
    $aprobado = NotificacionLog::where('referencia', 'anticipo:'.$a->id)->where('evento', 'ajAprobado')->first();
    $check($aprobado && count($aprobado->adjuntos ?? []) === 1, 'El correo de aprobado lleva la orden de anticipo adjunta');
    $para = collect($aprobado->destinatarios ?? [])->pluck('email')->all();
    $check(in_array($prod->email, $para, true), 'El correo de aprobado le llega al productor ('.count($para).' destinatarios)');

    /* ================= ESCENARIO B: borrador sin orden, > umbral, con devoluciones ================= */
    echo "\nESCENARIO B · borrador sin orden de compra, 30.000.000 × 40 % = 12.000.000 (supera el umbral)\n";
    $provJ = DB::table('proveedores')->where('tipo', 'juridica')->where('anticipo', 50)->orderBy('id')->value('id');
    $provN = DB::table('proveedores')->where('tipo', 'natural')->orderBy('id')->value('id');
    $datos = ['modo' => 'borrador', 'presupuesto_id' => 6733, 'proveedor_id' => $provJ, 'valor_base' => '30.000.000', 'porcentaje' => 40];

    $falla(fn () => $f->solicitar($prod, $datos), 'soportes', 'al menos un soporte', 'Borrador sin soporte rechazado');
    $falla(fn () => $f->solicitar($prod, ['proveedor_id' => $provN] + $datos, [$pdf('cot.pdf')]), 'proveedor_id', 'NIT', 'Proveedor persona natural rechazado');
    $falla(fn () => $f->solicitar($prod, ['valor_base' => '80.000.000'] + $datos, [$pdf('cot.pdf')]), 'valor_base', 'costo total del proyecto', 'Valor base mayor al costo del proyecto rechazado');
    $falla(fn () => $f->solicitar($prod, ['presupuesto_id' => 1952] + $datos, [$pdf('cot.pdf')]), 'presupuesto_id', 'tus proyectos', 'Proyecto de otro productor rechazado');
    $falla(fn () => $f->solicitar($prod, $datos, [UploadedFile::fake()->create('virus.exe', 10)]), 'soportes', 'PDF o imagen', 'Soporte .exe rechazado');

    $b = $f->solicitar($prod, $datos, [$pdf('cotizacion-proveedor.pdf'), UploadedFile::fake()->image('remision.jpg')]);
    $archivos = array_merge($archivos, $b->evidencias()->pluck('foto_evidencia')->all());
    $check($b->estado_id == F::REV_LIDER && !$b->oc_id && (float) $b->total_anticipo == 12000000.0, "Borrador #{$b->id} en Revisión líder, total 12.000.000");
    $check($b->evidencias()->count() === 2 && $b->evidencias()->whereNull('item_id')->count() === 2, 'Dos soportes guardados (sin ítem)');
    $check(F::pesos('$ 12.500.000') == 12500000.0 && F::pesos('12,500,000') == 12500000.0 && F::pesos('1.234,50') == 1234.5, 'Lectura de pesos con separadores');

    $falla(fn () => $f->rechazarLider($b, $lider, '  '), 'observacion', 'motivo', 'Líder no devuelve sin motivo');
    $b = $f->rechazarLider($b, $lider, 'El porcentaje supera el acuerdo del 50 %? revisa la cotización.');
    $check($b->estado_id == F::RECH_LIDER, 'Líder devuelve → Rechazo líder (11)');
    $falla(fn () => $f->reenviar($b, $prod, ['porcentaje' => 35, 'valor_base' => '32.000.000', 'proveedor_id' => $provJ]), 'observacion', 'qué corregiste', 'Reenvío sin decir qué corrigió rechazado');
    try { $f->reenviar($b, $prod, ['porcentaje' => 0]); } catch (ValidationException $e) { $check(count($e->errors()) >= 3, 'Una corrección incompleta muestra todos los errores juntos: '.implode(', ', array_keys($e->errors()))); }
    $rech = NotificacionLog::where('referencia', 'anticipo:'.$b->id)->where('evento', 'ajRechazoLider')->first();
    $check($rech && str_contains($rech->cuerpo, 'El porcentaje supera el acuerdo'), 'El correo de devolución trae el motivo del líder (no la observación de la solicitud)');
    $falla(fn () => $f->reenviar($b, $u('prueba.multirol@local.test'), ['porcentaje' => 35, 'observacion' => 'x']), 'general', 'Solo el productor que lo solicitó', 'Otro productor no puede corregirlo');
    $b = $f->reenviar($b, $prod, ['porcentaje' => 35, 'valor_base' => '32.000.000', 'proveedor_id' => $provJ, 'observacion' => 'Nueva cotización: base 32 M al 35 %.']);
    $check($b->estado_id == F::REV_LIDER && (float) $b->total_anticipo == 11200000.0, 'Reenviado corregido → Revisión líder, total 11.200.000');

    $b = $f->aprobarLider($b, $lider);
    $b = $f->aprobarController($b, $ctrl, 'Soportes completos.');
    $check($b->estado_id == F::REV_GERENCIA && !$b->cod_orden_anticipo, 'Controller aprueba > umbral → Revisión gerencia (9), aún sin orden de anticipo');
    $falla(fn () => $f->aprobarGerencia($b, $admin), 'general', 'Gerencia', 'Admin sin permiso de Gerencia no puede aprobar');
    $falla(fn () => $f->rechazarController($b, $ctrl, 'x'), 'general', 'no está en un estado', 'El Controller ya no puede devolverlo desde Gerencia');

    $b = $f->rechazarGerencia($b, $ger, 'Negocien 30 % con el proveedor.');
    $check($b->estado_id == F::RECH_GERENCIA, 'Gerencia devuelve → Rechazo gerencia (12)');
    $b = $f->reenviar($b, $prod, ['porcentaje' => 30, 'valor_base' => '32.000.000', 'proveedor_id' => $provJ, 'observacion' => 'Acordado 30 % con el proveedor.']);
    $check((float) $b->total_anticipo == 9600000.0, 'Con 30 % el total baja a 9.600.000 (ya no supera el umbral)');
    $b = $f->aprobarLider($b, $lider);
    $b = $f->aprobarController($b, $ctrl);
    $check($b->estado_id == F::APROBADO, 'Ahora ≤ umbral: el Controller lo deja aprobado sin Gerencia (1)');
    $archivos[] = $b->archivo_orden_anticipo;

    // La orden de anticipo solo lista las aprobaciones de la última ronda
    $html = app(\App\Services\PdfService::class)->htmlOrdenAnticipo($b->fresh());
    $check(str_contains($html, 'Acordado 30 % con el proveedor.') && !str_contains($html, 'Nueva cotización'), 'La orden muestra la solicitud vigente, no las rondas devueltas');
    $check(substr_count($html, '<td>Líder de producción</td>') === 1 && substr_count($html, '<td>Controller</td>') === 1 && !str_contains($html, '<td>Gerencia</td>'), 'La orden lista solo las aprobaciones de la última ronda (líder y Controller, sin Gerencia)');
    $reenvio = NotificacionLog::where('referencia', 'anticipo:'.$b->id)->where('evento', 'ajRevisionLider')->orderByDesc('id')->first();
    $check($reenvio && str_contains($reenvio->cuerpo, 'Acordado 30 %'), 'El aviso de reenvío le cuenta al líder qué se corrigió');

    $check(F::acciones($b, $ctrl) === ['anular'] && F::acciones($b, $cont) === ['causar', 'devolver_contabilidad'], 'En Aprobado: Contabilidad causa o devuelve; el Controller solo puede anularlo');

    // Nadie revisa su propia solicitud (cuenta con varios roles)
    $propio = $b->replicate(); $propio->estado_id = F::REV_LIDER; $propio->productor_id = $lider->id;
    $check(str_contains((string) F::impedimento($propio, $lider, 'aprobar_lider'), 'tú mismo'), 'Un líder no puede aprobar un anticipo que él mismo solicitó');

    echo "\nCorreos del borrador: ".implode(', ', $correos($b))."\n";
    $ger9 = NotificacionLog::where('referencia', 'anticipo:'.$b->id)->where('evento', 'ajRevisionGerencia')->first();
    $check($ger9 && collect($ger9->destinatarios)->pluck('email')->contains('prueba.gerencia@local.test') && !collect($ger9->destinatarios)->pluck('email')->contains('prueba.admin@local.test'), 'El aviso a Gerencia le llega a quien tiene el permiso (no al admin sin permiso)');
} catch (\Throwable $e) {
    $mal++;
    echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
    if ($e instanceof ValidationException) { echo '    '.json_encode($e->errors(), JSON_UNESCAPED_UNICODE)."\n"; }
} finally {
    DB::rollBack();
    foreach (array_filter($archivos) as $r) { Storage::delete($r); }
}
echo "\nResultado: $ok correctas, $mal fallas. (Base de datos restaurada: transacción deshecha; archivos de prueba borrados.)\n";
