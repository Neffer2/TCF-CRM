<?php
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Anticipo de productor de punta a punta: solicitud → líder → Gerencia → evidencias → revisión de evidencias →
// Contabilidad (devuelve y luego causa) → Tesorería paga. Transacción deshecha; archivos borrados.
use App\Http\Livewire\Productor\Ordenes\AnticipoProductor;
use App\Http\Livewire\Contabilidad\Produccion\Anticipo as AnticipoCont;
use App\Http\Livewire\Tesoreria\Produccion\Anticipo as AnticipoTeso;
use App\Http\Livewire\LiderProduccion\Anticipos as BandejaLider;
use App\Models\{User, Anticipo, NotificacionLog, EvidenciaAnticipo};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage};
use Livewire\Livewire;

$ok = 0; $mal = 0; $antes = collect(Storage::allFiles('public'));
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$st = fn ($t) => $t->lastResponse->status();
$err = fn ($t) => array_keys($t->payload['serverMemo']['errors'] ?? []);
$correo = fn ($evento, $desde) => NotificacionLog::where('evento', $evento)->where('id', '>', $desde)->count();
$u = fn ($e) => User::where('email', $e)->firstOrFail();
$prod = $u('prueba.productor@local.test'); $lider = $u('prueba.lider@local.test'); $ger = $u('prueba.gerencia@local.test');
$admin = $u('prueba.admin@local.test'); $cont = $u('prueba.contabilidad@local.test'); $teso = $u('prueba.tesoreria@local.test'); $otroProd = $u('prueba.multirol@local.test');
$pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
$firma = 'data:image/png;base64,'.base64_encode($pngBytes);
$foto = fn () => UploadedFile::fake()->createWithContent('evidencia.png', $pngBytes);
$pdf = fn () => UploadedFile::fake()->createWithContent('comprobante.pdf', "%PDF-1.4\n%%EOF");
$ficha = fn ($user, $id) => Livewire::actingAs($user)->test(AnticipoProductor::class, ['anticipo_id' => $id]);

DB::beginTransaction();
try {
    // El ítem 63636 (1.200.000) queda disponible para cuenta de cobro (proveedor 3) solo dentro de la prueba
    DB::table('item_presupuesto_proveedor')->insert(['item_presupuesto_id' => 63636, 'proveedor_id' => 3]);
    echo "SOLICITUD (productor)\n";
    $t = Livewire::actingAs($prod)->test(AnticipoProductor::class);
    $t->set('centro_costo', 6736)->set('item_presupuesto', 63636);
    $check((float) $t->get('limiteValorTotal') == 1200000.0, 'Tope del ítem = $1.200.000 (sin órdenes ni anticipos previos)');
    $t->set('valor_anticipo', 2000000)->call('newItem');
    $check(in_array('valor_anticipo', $err($t), true), 'Un anticipo mayor al valor del ítem se rechaza');
    $t->set('valor_anticipo', 800000)->call('newItem');
    $check(count($t->get('items')) === 1 && (float) $t->get('total_anticipo') == 800000.0, 'Agrega el ítem: total $800.000');
    $desde = (int) NotificacionLog::max('id');
    $t->call('nuevoAnticipoProductor', $firma);
    $a = Anticipo::where('productor_id', $prod->id)->where('tipo', '!=', 'juridico')->latest('id')->first();
    $check($a && (int) $a->estado_id === 8 && (float) $a->total_anticipo == 800000.0, 'Anticipo creado en Revisión líder (8) con el total de los ítems');
    $check($a->firma_productor && Storage::exists($a->firma_productor) && str_contains($a->firma_productor, '_'), 'La firma queda en un archivo propio del anticipo');
    $check($correo('anticipoProdRevisionLiderProd', $desde) === 1, 'Sale el aviso al líder de producción');
    $check($st($ficha($otroProd, $a->id)) === 403, 'Otro productor no abre el anticipo');

    echo "\nLÍDER Y GERENCIA\n";
    $check($st($ficha($prod, $a->id)->call('revisionAnticipoProductor', 9)) === 200 && (int) $a->fresh()->estado_id === 8, 'El productor no se aprueba a sí mismo');
    $ficha($lider, $a->id)->set('observaciones_revision_lider', 'Cuadra con el presupuesto')->call('revisionAnticipoProductor', 9);
    $check((int) $a->fresh()->estado_id === 9, 'El líder aprueba: por $800.000 (≥ $500.000) pasa a Gerencia (9)');
    $ficha($admin, $a->id)->set('observaciones_revision_gerencia', 'x')->call('revisionAnticipoProductor', 7);
    $check((int) $a->fresh()->estado_id === 9, 'Un admin sin el permiso de Gerencia no aprueba');
    $desde = (int) NotificacionLog::max('id');
    $ficha($ger, $a->id)->set('observaciones_revision_gerencia', 'Aprobado por Gerencia')->call('revisionAnticipoProductor', 7);
    $check((int) $a->fresh()->estado_id === 7 && $correo('anticipoProdEvidencias', $desde) === 1, 'Gerencia aprueba: Cargue de evidencias (7) y aviso al productor');

    echo "\nEVIDENCIAS\n";
    $t = $ficha($prod, $a->id)->call('enviarEvidencias');
    $check((int) $a->fresh()->estado_id === 7, 'Sin evidencias no se envía');
    $t = $ficha($prod, $a->id)->set('item_evidencia', 99999999)->set('fecha_evidencia', '2026-09-25')->set('foto_evidencia', $foto())->set('observacion_evidencia', 'Montaje')->call('newEvidencia');
    $check(in_array('item_evidencia', $err($t), true), 'Una evidencia de un ítem ajeno se rechaza');
    $t = $ficha($prod, $a->id)->set('item_evidencia', 63636)->set('fecha_evidencia', '2026-09-25')->set('foto_evidencia', $foto())->set('observacion_evidencia', 'Montaje del evento')->call('newEvidencia');
    $check(EvidenciaAnticipo::where('anticipo_id', $a->id)->count() === 1, 'Carga la evidencia del ítem');
    $ficha($prod, $a->id)->call('enviarEvidencias');
    $check((int) $a->fresh()->estado_id === 10, 'Envía las evidencias: Revisión de evidencias (10)');
    $bandeja = Livewire::actingAs($lider)->test(BandejaLider::class);
    $check(str_contains($bandeja->payload['effects']['html'] ?? '', 'Anticipo #'.$a->id), 'El líder ve el anticipo en su bandeja (antes solo veía los de estado 8)');

    echo "\nREVISIÓN DE EVIDENCIAS (líder) — paso que no existía\n";
    $t = $ficha($lider, $a->id)->call('revisionEvidencias', 0);
    $check(in_array('rechazo_revision_evidencias', $err($t), true) && (int) $a->fresh()->estado_id === 10, 'Devolver exige motivo');
    $desde = (int) NotificacionLog::max('id');
    $ficha($lider, $a->id)->set('rechazo_revision_evidencias', 'Falta la foto del cierre')->call('revisionEvidencias', 0);
    $check((int) $a->fresh()->estado_id === 7 && $correo('anticipoProdRechazoEvidencias', $desde) === 1, 'Las devuelve: Cargue de evidencias (7) y aviso al productor con el motivo');
    $ficha($prod, $a->id)->call('enviarEvidencias');
    $check((int) $a->fresh()->estado_id === 10, 'El productor las reenvía (10)');
    $check($st($ficha($prod, $a->id)->call('revisionEvidencias', 1)) === 200 && (int) $a->fresh()->estado_id === 10, 'El productor no aprueba sus propias evidencias');
    $desde = (int) NotificacionLog::max('id');
    $ficha($lider, $a->id)->set('observaciones_revision_evidencias', 'Completo')->call('revisionEvidencias', 1);
    $a = $a->fresh();
    $check((int) $a->estado_id === 1 && $a->fecha_aprobacion && $correo('anticipoProdEvidenciasAprobadas', $desde) === 1, 'Aprueba las evidencias: Aprobado, pendiente de causar (1) y aviso a Contabilidad');

    echo "\nCONTABILIDAD\n";
    $tc = Livewire::actingAs($cont)->test(AnticipoCont::class, ['anticipo_id' => $a->id]);
    $check($st($tc) === 200 && str_contains($tc->payload['effects']['html'] ?? '', 'tems del anticipo'), 'La pantalla de causación muestra el anticipo (antes error 500)');
    $desde = (int) NotificacionLog::max('id');
    $tc->set('observacion_causacion', 'Falta el soporte del pago al proveedor')->call('rechazar');
    $check((int) $a->fresh()->estado_id === 13 && $correo('anticipoRechazoContabilidad', $desde) === 1, 'Contabilidad lo devuelve (13) y el productor recibe el motivo');
    $t = $ficha($prod, $a->id);
    $check(str_contains($t->payload['effects']['html'] ?? '', 'Contabilidad devolvió el anticipo'), 'El productor ve el motivo de Contabilidad y puede corregir las evidencias');
    $t->call('enviarEvidencias');
    $check((int) $a->fresh()->estado_id === 10, 'Reenvía las evidencias: vuelven al líder (10)');
    $ficha($lider, $a->id)->call('revisionEvidencias', 1);
    $check((int) $a->fresh()->estado_id === 1, 'El líder las aprueba de nuevo (1)');
    $desde = (int) NotificacionLog::max('id');
    $tc = Livewire::actingAs($cont)->test(AnticipoCont::class, ['anticipo_id' => $a->id]);
    $tc->set('causa_cod', '778899')->set('observacion_causacion', 'Causado')->call('store');
    $a = $a->fresh();
    $check((int) $a->estado_id === 5 && $a->cod_causal === '778899' && $a->fecha_causal && $correo('anticipoCausado', $desde) === 1, 'Contabilidad lo causa (5) y Tesorería recibe el aviso');

    echo "\nTESORERÍA\n";
    $tt = Livewire::actingAs($teso)->test(AnticipoTeso::class, ['anticipo_id' => $a->id]);
    $check($st($tt) === 200 && str_contains($tt->payload['effects']['html'] ?? '', 'Marcar como pagado'), 'La pantalla de pago muestra el anticipo (antes error 500)');
    $desde = (int) NotificacionLog::max('id');
    $tt->set('comprobante', $pdf())->call('store');
    $a = $a->fresh();
    $check((int) $a->estado_id === 14 && $a->comprobante_pago && Storage::exists($a->comprobante_pago) && $correo('anticipoPagado', $desde) === 1, 'Tesorería registra el pago: Pagado (14) con comprobante y aviso');
    $comprobante = $a->comprobante_pago;
    $tt2 = Livewire::actingAs($teso)->test(AnticipoTeso::class, ['anticipo_id' => $a->id])->set('comprobante', $pdf())->call('store');
    $check($a->fresh()->comprobante_pago === $comprobante && (int) $a->fresh()->estado_id === 14, 'No se puede pagar dos veces (el comprobante no cambia)');
    $check(str_contains($ficha($prod, $a->id)->payload['effects']['html'] ?? '', 'Pagado el'), 'El productor ve su anticipo como pagado');
} catch (\Throwable $e) {
    $mal++; echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
    foreach (collect(Storage::allFiles('public'))->diff($antes) as $f2) { Storage::delete($f2); }
}
echo "\nResultado: $ok correctas, $mal fallas.\n";
