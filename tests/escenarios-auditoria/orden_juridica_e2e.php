<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// E2E de la orden de compra jurídica, rol por rol. Transacción deshecha al final; archivos borrados.
use App\Http\Livewire\Productor\Ordenes\Juridica;
use App\Http\Livewire\Productor\Remision;
use App\Models\OrdenCompra;
use App\Models\PresupuestoProyecto;
use App\Models\User;
use App\Models\NotificacionLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

$ok = 0; $mal = 0; $antes = collect(Storage::allFiles('public'));
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$errores = fn ($t) => array_keys($t->payload['serverMemo']['errors'] ?? []);
$u = fn ($e) => User::where('email', $e)->firstOrFail();
$prod = $u('prueba.productor@local.test'); $admin = $u('prueba.admin@local.test'); $otroProd = $u('prueba.multirol@local.test');
$pdf = fn ($n) => UploadedFile::fake()->createWithContent($n, "%PDF-1.4\n%%EOF");
$png = 'data:image/png;base64,'.base64_encode(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));

DB::beginTransaction();
try {
    $p = PresupuestoProyecto::find(6736);
    $provId = 1689; // proveedor asociado a los ítems del presupuesto 6736
    echo "CREAR (productor)\n";
    $t = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p]);
    $t->set('proveedor', $provId)->set('item', 63636);
    $check((float) $t->get('maxValor') == 1200000.0 && (int) $t->get('maxCant') == 40, 'Al elegir el ítem muestra el tope: 40 unidades y $1.200.000');
    $t->set('desc', 'Power bank')->set('cant', 20)->set('dias', 1)->set('otros', 1)->set('vUnit', 30000);
    $check((float) $t->get('vTotal') == 600000.0, 'Calcula 20 × $30.000 = $600.000');
    $t->call('newItem');
    $check(count($t->get('ocItems')) === 1, 'Agrega el ítem a la orden');
    $t->set('file_cot', $pdf('cotizacion.pdf'))->call('enviarAprobacion');
    $oc = OrdenCompra::where('presupuesto_id', 6736)->latest('id')->first();
    $check($oc && (int) $oc->estado_id === 2 && (int) $oc->tipo_oc === 1 && (float) $oc->ordenItems->sum('vtotal_oc') == 600000.0, 'Orden creada en Revisión (2), jurídica, por $600.000');

    echo "\nTOPES DEL PRESUPUESTO\n";
    $t2 = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p]);
    $t2->set('item', 63636);
    $check((int) $t2->get('maxCant') === 20 && (float) $t2->get('maxValor') == 600000.0, 'Una segunda orden del mismo ítem solo deja lo que queda: 20 unidades y $600.000');
    $t2->set('desc', 'Power bank')->set('cant', 25)->set('dias', 1)->set('otros', 1)->set('vUnit', 30000)->call('newItem');
    $check(in_array('cant', $errores($t2), true) || in_array('vTotal', $errores($t2), true), 'Pedir 25 cuando quedan 20 se rechaza ('.implode(',', $errores($t2)).')');
    $t2->set('cant', 20)->set('vUnit', 30000)->set('vTotal', 900000)->call('newItem');
    $check(in_array('vTotal', $errores($t2), true), 'Escribir un total mayor al disponible se rechaza');

    echo "\nAPROBACIÓN\n";
    $oc = $oc->fresh();
    $tp = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $oc]);
    $tp->set('oc_helisa', $pdf('helisa.pdf'))->set('cod_oc', 'TEST-1')->call('cambioEstado', 1);
    $check((int) $oc->fresh()->estado_id === 2, 'El productor no puede aprobar su propia orden');
    $ta = Livewire::actingAs($admin)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $oc]);
    $ta->set('oc_helisa', $pdf('helisa.pdf'))->set('cod_oc', 'TEST-1')->set('observaciones_negociacion', 'Prueba')->call('cambioEstado', 1);
    $oc = $oc->fresh();
    $check((int) $oc->estado_id === 1 && $oc->cod_oc === 'TEST-1' && $oc->archivo_orden_helisa && Storage::exists($oc->archivo_orden_helisa), 'Compras la aprueba con el código y el PDF de Helisa');
    $check(NotificacionLog::where('evento', 'mailOrdenAprobada')->where('created_at', '>=', now()->subMinute())->exists(), 'Sale el correo de orden aprobada');

    echo "\nREMISIÓN Y GR\n";
    $tr = Livewire::actingAs($otroProd)->test(Remision::class, ['orden' => $oc->id]);
    $tr->set('remision', $pdf('remision.pdf'))->call('store', $png);
    $check((int) $oc->fresh()->estado_id === 1, 'Otro productor no puede firmar la remisión de esta orden');
    $tr = Livewire::actingAs($prod)->test(Remision::class, ['orden' => $oc->id]);
    $tr->set('remision', $pdf('remision.pdf'))->set('observaciones', 'Recibido completo')->call('store', $png);
    $oc = $oc->fresh();
    $check((int) $oc->estado_id === 4 && Storage::exists($oc->archivo_remision) && Storage::exists($oc->archivo_firma), 'El productor firma la remisión: Recibido (4) con PDF y firma guardados');
    $tg = Livewire::actingAs($admin)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $oc]);
    $tg->set('gr', 'GR-TEST-9')->call('cambioEstado', 5);
    $oc = $oc->fresh();
    $check((int) $oc->estado_id === 5 && $oc->gr === 'GR-TEST-9', 'Compras registra el GR: Comprobado (5)');
    $check(NotificacionLog::where('evento', 'mailGrGenerado')->where('created_at', '>=', now()->subMinute())->exists(), 'Sale el correo de GR generado');
    $tg->call('cambioEstado', 5);
    $check((int) $oc->fresh()->estado_id === 5, 'No se puede registrar el GR dos veces (sigue en 5 sin error)');

    echo "\nANULACIÓN Y BORRADO\n";
    $tb = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p]);
    $tb->set('proveedor', $provId)->set('item', 63637)->set('desc', 'Totebag')->set('cant', 40)->set('dias', 1)->set('otros', 1)->set('vUnit', 26800)->call('newItem');
    $tb->set('file_cot', $pdf('cot2.pdf'))->call('enviarAprobacion');
    $oc2 = OrdenCompra::where('presupuesto_id', 6736)->latest('id')->first();
    $tx = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p]);
    $tx->set('item', 63637);
    $check($errores($tx) || (float) $tx->get('vTotal') == 0.0, 'Con el ítem consumido por completo no deja pedir más ('.implode(',', $errores($tx)).')');
    Livewire::actingAs($admin)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $oc2])->set('observaciones_anulacion', 'Prueba de anulación')->call('cambioEstado', 6);
    $check((int) $oc2->fresh()->estado_id === 6, 'Compras anula la orden (6) con observación');
    $ty = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p]);
    $ty->set('item', 63637);
    $check((float) $ty->get('maxValor') == 1072000.0, 'La anulación libera el presupuesto del ítem ($1.072.000 disponibles otra vez)');
    $ty->set('proveedor', $provId)->set('item', 63637)->set('desc', 'Totebag')->set('cant', 10)->set('dias', 1)->set('otros', 1)->set('vUnit', 26800)->call('newItem');
    $ty->set('file_cot', $pdf('cot3.pdf'))->call('enviarAprobacion');
    $oc3 = OrdenCompra::where('presupuesto_id', 6736)->latest('id')->first();
    Livewire::actingAs($otroProd)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $oc3])->call('deleteOrden');
    $check(OrdenCompra::find($oc3->id) !== null, 'Otro productor no puede borrar la orden');
    Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $oc->fresh()])->call('deleteOrden');
    $check(OrdenCompra::find($oc->id) !== null, 'Una orden comprobada no se puede borrar');
    Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $oc3])->call('deleteOrden');
    $check(OrdenCompra::find($oc3->id) === null, 'El dueño borra su orden en revisión');
} catch (\Throwable $e) {
    $mal++; echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
    foreach (collect(Storage::allFiles('public'))->diff($antes) as $f) { Storage::delete($f); }
}
echo "\nResultado: $ok correctas, $mal fallas.\n";
