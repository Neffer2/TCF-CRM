<?php
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Anticipo jurídico: la misma compra no se anticipa dos veces (borrador + orden), y un borrador se vincula a su orden.
use App\Models\{User, Anticipo, OrdenCompra};
use App\Services\AnticipoJuridicoFlujo as F;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Validation\ValidationException;

$ok = 0; $mal = 0; $antes = collect(Storage::allFiles('public'));
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$falla = function (callable $fn, $clave, $contiene, $t) use ($check) {
    try { $fn(); $check(false, "$t (no lanzó error)"); }
    catch (ValidationException $e) { $m = $e->errors()[$clave][0] ?? json_encode($e->errors(), JSON_UNESCAPED_UNICODE); $check(str_contains((string) $m, $contiene), "$t → $m"); }
};
$u = fn ($e) => User::where('email', $e)->firstOrFail();
$prod = $u('prueba.productor@local.test'); $lider = $u('prueba.lider@local.test');
$pdf = fn ($n) => UploadedFile::fake()->createWithContent($n, "%PDF-1.4\n%%EOF");
$f = new F;

DB::beginTransaction();
try {
    // Los anticipos de demostración (#4, #5) son del mismo proveedor y proyecto que la OC 11426: se anulan solo dentro de la prueba
    DB::table('anticipos')->where('tipo', 'juridico')->where('presupuesto_id', 6733)->whereNotIn('estado_id', [6, 14])->update(['estado_id' => 6]);
    DB::table('ordenes_compra')->where('id', 11426)->update(['estado_id' => 1]); // OC jurídica aprobada del productor (proveedor 285, presupuesto 6733)
    $oc = OrdenCompra::find(11426);
    DB::table('proveedores')->where('id', $oc->proveedor_id)->update(['tipo' => 'juridica']);
    $base = ['modo' => 'borrador', 'presupuesto_id' => 6733, 'proveedor_id' => $oc->proveedor_id, 'valor_base' => '10.000.000', 'porcentaje' => 30];

    echo "BORRADOR Y ORDEN DE LA MISMA COMPRA\n";
    // (los anticipos de demostración #4 y #5 ya son de este proveedor en este proyecto: se explica por qué)
    $b = $f->solicitar($prod, $base + ['observacion' => 'Montaje del evento, aparte de la compra anterior.'], [$pdf('cot.pdf')]);
    $check((int) $b->estado_id === F::REV_LIDER && !$b->oc_id, "Borrador #{$b->id} sin orden de compra");
    $falla(fn () => $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 50]), 'oc_id', 'sin orden de compra (#'.$b->id.')', 'Pedir otro anticipo sobre la orden de esa misma compra se rechaza');
    $falla(fn () => $f->solicitar($prod, $base, [$pdf('cot2.pdf')]), 'observacion', 'Explica por qué', 'Un segundo borrador al mismo proveedor exige explicar por qué');
    $b2 = $f->solicitar($prod, $base + ['observacion' => 'Es otra compra: el sonido, aparte del montaje.'], [$pdf('cot2.pdf')]);
    $check((int) $b2->estado_id === F::REV_LIDER, 'Con la explicación sí se crea (#'.$b2->id.')');
    $b2 = $f->retirar($b2, $prod, 'Prueba');

    echo "\nVINCULAR EL BORRADOR A SU ORDEN\n";
    $check(F::ordenesVinculables($b, $prod)->pluck('id')->contains(11426), 'La orden aparece como vinculable al corregir el borrador');
    $b = $f->rechazarLider($b, $lider, 'Vincúlalo a la orden de compra');
    $falla(fn () => $f->reenviar($b, $prod, ['porcentaje' => 30, 'observacion' => 'Vinculado', 'oc_vincular' => 11427]), 'oc_vincular', 'no es de este proveedor', 'No se vincula a una orden de otro proveedor');
    $b = $f->reenviar($b, $prod, ['porcentaje' => 30, 'observacion' => 'Vinculado a la orden', 'oc_vincular' => 11426]);
    $check((int) $b->oc_id === 11426 && (float) $b->valor_base == 12685000.0 && (float) $b->total_anticipo == 3805500.0, 'Queda vinculado: base = valor de la orden (12.685.000), 30 % = 3.805.500');
    $check(!F::ordenesDisponibles($prod)->pluck('id')->contains(11426), 'La orden ya no está disponible para otro anticipo');
    $falla(fn () => $f->solicitar($prod, ['modo' => 'oc', 'oc_id' => 11426, 'porcentaje' => 10]), 'oc_id', 'ya tiene un anticipo', 'Ni se puede pedir otro sobre ella');
} catch (\Throwable $e) {
    $mal++; echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage().($e instanceof ValidationException ? ' '.json_encode($e->errors(), JSON_UNESCAPED_UNICODE) : '')."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
    foreach (collect(Storage::allFiles('public'))->diff($antes) as $f2) { Storage::delete($f2); }
}
echo "\nResultado: $ok correctas, $mal fallas.\n";
