<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Anticipo jurídico: anular, retirar, orden de compra anulada y anticipos ajenos. Transacción deshecha al final.
use App\Models\{Anticipo, User, NotificacionLog, OrdenCompra};
use App\Services\AnticipoJuridicoFlujo as F;
use App\Http\Livewire\Produccion\AnticipoJuridico;
use App\Http\Livewire\Productor\Ordenes\Juridica;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

$ok = 0; $mal = 0; $antes = collect(Storage::allFiles('public'));
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$falla = function (callable $fn, $contiene, $t) use ($check) {
    try { $fn(); $check(false, "$t (no lanzó error)"); }
    catch (ValidationException $e) { $m = collect($e->errors())->flatten()->first(); $check(str_contains((string) $m, $contiene), "$t → $m"); }
};
$u = fn ($e) => User::where('email', $e)->firstOrFail();
$prod = $u('prueba.productor@local.test'); $lider = $u('prueba.lider@local.test'); $ctrl = $u('prueba.controller@local.test');
$cont = $u('prueba.contabilidad@local.test'); $teso = $u('prueba.tesoreria@local.test'); $admin = $u('prueba.admin@local.test');
$otroProd = $u('prueba.multirol@local.test');
$f = new F;

DB::beginTransaction();
try {
    // Los anticipos de demostración (#4, #5) son del mismo proveedor y proyecto que la OC 11426: se anulan solo dentro de la prueba
    DB::table('anticipos')->where('tipo', 'juridico')->where('presupuesto_id', 6733)->whereNotIn('estado_id', [6, 14])->update(['estado_id' => 6]);
    DB::table('ordenes_compra')->whereIn('id', [11426, 11427])->update(['estado_id' => 1]);

    echo "RETIRAR (productor)\n";
    $a = $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 40]);
    $falla(fn () => $f->retirar($a, $prod, ''), 'motivo', 'Retirar exige motivo');
    $falla(fn () => $f->retirar($a, $otroProd, 'x'), 'Solo el productor', 'Otro productor no puede retirarlo');
    $a = $f->retirar($a, $prod, 'El proveedor ya no lo pide.');
    $check((int) $a->estado_id === 6, 'El productor retira su solicitud: queda Anulado (6)');
    $check(F::ordenesDisponibles($prod)->pluck('id')->contains(11426), 'La orden queda libre para otro anticipo');
    $check(NotificacionLog::where('referencia', 'anticipo:'.$a->id)->where('evento', 'ajAnulado')->exists(), 'Sale el aviso de anulación');

    echo "\nANULAR (Controller)\n";
    $b = $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 30]);
    $b = $f->aprobarLider($b, $lider);
    $falla(fn () => $f->anular($b, $prod, 'x'), 'Controller', 'El productor no usa la acción del Controller');
    $b = $f->anular($b, $ctrl, 'Se negoció sin anticipo.');
    $check((int) $b->estado_id === 6 && $b->pasos()->where('accion', 'anular')->exists(), 'El Controller lo anula desde su revisión y queda en el recorrido');
    $falla(fn () => $f->aprobarController($b, $ctrl), 'estado', 'Un anulado ya no se aprueba');

    echo "\nORDEN DE COMPRA ANULADA\n";
    $c = $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 20]);
    $c = $f->aprobarLider($c, $lider);
    $c = $f->aprobarController($c, $ctrl);
    $check((int) $c->estado_id === F::APROBADO, 'Anticipo aprobado, pendiente de causar');
    DB::table('ordenes_compra')->where('id', 11426)->update(['estado_id' => 6]);
    $c = $c->fresh();
    $falla(fn () => $f->causar($c, $cont, '777'), 'fue anulada', 'Con la orden anulada Contabilidad ya no lo causa');
    $check(F::acciones($c, $ctrl) === ['anular'], 'Al Controller solo le queda anularlo');
    DB::table('ordenes_compra')->where('id', 11426)->update(['estado_id' => 1]);

    $oc = OrdenCompra::find(11426);
    $desde = (int) NotificacionLog::max('id');
    Livewire::actingAs($admin)->test(Juridica::class, ['presupuesto' => $oc->presupuesto, 'orden_compra' => $oc])
        ->set('observaciones_anulacion', 'Prueba: el proveedor canceló')->call('cambioEstado', 6);
    $check((int) $oc->fresh()->estado_id === 6, 'Compras anula la orden de compra');
    $check((int) $c->fresh()->estado_id === 6 && $c->fresh()->pasos()->where('accion', 'anular_por_oc')->exists(), 'Su anticipo aprobado queda anulado con ella (antes se podía causar y pagar)');

    DB::table('ordenes_compra')->where('id', 11427)->update(['estado_id' => 1]);
    $d = $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11427, 'porcentaje' => 10]);
    $d = $f->aprobarController($f->aprobarLider($d, $lider), $ctrl);
    $d = $f->causar($d, $cont, '888');
    $oc2 = OrdenCompra::find(11427);
    $desde = (int) NotificacionLog::max('id');
    Livewire::actingAs($admin)->test(Juridica::class, ['presupuesto' => $oc2->presupuesto, 'orden_compra' => $oc2])
        ->set('observaciones_anulacion', 'Prueba')->call('cambioEstado', 6);
    $check((int) $d->fresh()->estado_id === F::CAUSADO, 'Un anticipo ya causado no se toca al anular la orden');
    $check(NotificacionLog::where('id', '>', $desde)->where('evento', 'ajOrdenAnuladaConAnticipo')->exists(), 'Contabilidad y Tesorería reciben el aviso para revisarlo');

    echo "\nANTICIPOS AJENOS EN LA FICHA\n";
    $t = Livewire::actingAs($prod)->test(AnticipoJuridico::class, ['anticipo_id' => $a->id]);
    $t->set('anticipo_id', $d->id);
    $check($t->lastResponse->status() === 403, 'Cambiar el anticipo desde el navegador se rechaza (403)');
    $t2 = Livewire::actingAs($otroProd)->test(AnticipoJuridico::class, ['anticipo_id' => $a->id]);
    $check($t2->lastResponse->status() === 403, 'Otro productor no abre el anticipo');
} catch (\Throwable $e) {
    $mal++; echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
    foreach (collect(Storage::allFiles('public'))->diff($antes) as $f2) { Storage::delete($f2); }
}
echo "\nResultado: $ok correctas, $mal fallas.\n";
