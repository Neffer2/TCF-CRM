<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Permisos y flujo del presupuesto después del endurecimiento. Transacción deshecha al final.
use App\Http\Livewire\Com\Presupuesto\Presupuesto;
use App\Models\{User, ItemPresupuesto, PresupuestoProyecto, GestionComercial, NotificacionLog, OcItem};
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

$ok = 0; $mal = 0;
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$st = fn ($t) => $t->lastResponse->status();
$err = fn ($t) => array_keys($t->payload['serverMemo']['errors'] ?? []);
$correo = fn ($evento, $desde) => NotificacionLog::where('evento', $evento)->where('id', '>', $desde)->count();
$u = fn ($id) => User::findOrFail($id);
$alejandra = $u(94); $camilo = $u(132); $otroCom = $u(12009); $ejecutivo = $u(12010); $lidercom = $u(12017);
$admin = $u(12008); $gerencia = $u(12007); $controller = $u(12016);
$abrir = fn ($user, $g) => Livewire::actingAs($user)->test(Presupuesto::class, ['id_gestion' => $g]);

DB::beginTransaction();
try {
    $ultimoLog = (int) NotificacionLog::max('id');
    echo "ACCESO A LA PÁGINA\n";
    $check($st($abrir($alejandra, 6733)) === 200, 'La dueña abre su presupuesto');
    $check($st($abrir($otroCom, 6733)) === 403, 'Otro comercial no abre un presupuesto ajeno (403)');
    $check($st($abrir($ejecutivo, 6733)) === 200, 'El ejecutivo abre el presupuesto de su comercial');
    $check($st($abrir($ejecutivo, 8575)) === 403, 'El ejecutivo no abre el de otro comercial');
    $check($st($abrir($lidercom, 8575)) === 200, 'El líder comercial abre el de alguien de su equipo');
    $check($st($abrir($lidercom, 6733)) === 403, 'El líder comercial no abre el de alguien fuera de su equipo');
    $check($st($abrir($admin, 6733)) === 200 && $st($abrir($controller, 6733)) === 200, 'Admin y Controller abren cualquiera');
    $antes = PresupuestoProyecto::count();
    $check($st($abrir($admin, 99999999)) === 404 && PresupuestoProyecto::count() === $antes, 'Una gestión que no existe da 404 y no crea presupuesto');

    echo "\nPROPIEDADES BLOQUEADAS\n";
    foreach (['presupuesto_id' => 5227, 'id_gestion' => 6733, 'estadoValidator' => 1, 'margenProyecto' => 99] as $prop => $v) {
        $t = $abrir($camilo, 8575)->set($prop, $v);
        $check($st($t) === 403, "Cambiar $prop desde el navegador se rechaza (403)");
    }

    echo "\nEDICIÓN DEL COMERCIAL (presupuesto 6737, Editable)\n";
    $pid = 6737; $n0 = ItemPresupuesto::where('presupuesto_id', $pid)->count();
    $t = $abrir($camilo, 8575);
    $t->set('cod', 0)->set('cantidad', 2)->set('dia', 1)->set('otros', 1)->set('descripcion', 'Ítem de prueba auditoría')
      ->set('valor_unitario', '100000')->set('proveedor', [1689])->set('mes', 1)->set('dias', 1)->set('ciudad', 1)->set('utilidad', '0,7');
    $check(abs((float) $t->get('utilidad') - 0.7) < 1e-9, 'Escribir 0,7 en utilidad queda 0.7 (antes quedaba 7)');
    $check(round((float) $t->get('valor_unitario_cliente')) == round(100000 / 0.7), 'El precio al cliente sale de costo ÷ 0,7');
    $t->call('new_item');
    $nuevo = ItemPresupuesto::where('presupuesto_id', $pid)->latest('id')->first();
    $check(ItemPresupuesto::where('presupuesto_id', $pid)->count() === $n0 + 1 && abs($nuevo->margen_utilidad - 0.7) < 1e-9, 'El comercial agrega un ítem con utilidad 0,7');
    $t2 = $abrir($camilo, 8575)->set('utilidad', '70');
    $check(in_array('utilidad', $err($t2), true), 'Utilidad 70 se rechaza con mensaje');
    $t->call('changeDisponibilidad', $nuevo->id);
    $check((bool) $nuevo->fresh()->disponible !== (bool) $nuevo->disponible, 'Cambia la disponibilidad de su ítem');
    $check($st($abrir($camilo, 8575)->call('changeDisponibilidad', ItemPresupuesto::where('presupuesto_id', 5227)->value('id'))) === 404, 'No puede tocar un ítem de otro presupuesto (404)');
    $t->call('getDataEdit', $nuevo->id)->set('descripcion', 'Ítem editado')->call('actionEdit');
    $check($nuevo->fresh()->descripcion === 'Ítem editado', 'Edita su ítem');
    $ajeno = ItemPresupuesto::where('presupuesto_id', 5227)->orderBy('orden')->first();
    $ordenAjeno = $ajeno->orden;
    $abrir($camilo, 8575)->call('updateOrden', array_merge([$ajeno->id], ItemPresupuesto::where('presupuesto_id', $pid)->pluck('id')->reverse()->values()->all()));
    $check((int) $ajeno->fresh()->orden === (int) $ordenAjeno, 'Reordenar no mueve ítems de otro presupuesto');
    OcItem::forceCreate(['item_id' => $nuevo->id, 'oc_id' => OcItem::value('oc_id'), 'cant_oc' => 1, 'vunit_oc' => 1, 'vtotal_oc' => 1, 'desc_oc' => 'x', 'dias_oc' => 1, 'otros_oc' => 1]);
    $abrir($camilo, 8575)->call('deleteItem', $nuevo->id);
    $check(ItemPresupuesto::find($nuevo->id) !== null, 'Un ítem con órdenes de compra no se borra');
    OcItem::where('item_id', $nuevo->id)->delete();
    $abrir($camilo, 8575)->call('deleteItem', $nuevo->id);
    $check(ItemPresupuesto::find($nuevo->id) === null, 'Borra su ítem sin órdenes');
    $ev0 = ItemPresupuesto::where('presupuesto_id', 5227)->where('evento', 1)->count();
    $te = $abrir($ejecutivo, 6733)->set('descripcion', 'Evento de prueba')->call('new_event');
    $check($st($te) === 200 && ItemPresupuesto::where('presupuesto_id', 5227)->where('evento', 1)->count() === $ev0 + 1, 'El ejecutivo agrega un evento al presupuesto de su comercial');

    echo "\nAPROBACIONES: quién puede\n";
    $check($st($abrir($camilo, 8575)->call('validacionGerencia')) === 403, 'El comercial no puede aprobar como Gerencia');
    $check($st($abrir($camilo, 8575)->call('validacionLiderComercial')) === 403, 'El comercial no puede validar como líder');
    $check($st($abrir($camilo, 8575)->call('updateCentro')) === 403, 'El comercial no puede asignar centro de costos');
    $check($st($abrir($camilo, 8575)->call('rechazar')) === 403, 'El comercial no puede rechazar');
    $check($st($abrir($camilo, 8575)->call('marcarComoVisto', 6733)) === 403, 'El comercial no puede marcar actualizaciones como vistas');

    echo "\nFLUJO ≥ 30 %: comercial → líder → aprobado (presupuesto 6737)\n";
    $abrir($camilo, 8575)->call('aprobacion');
    $check((int) PresupuestoProyecto::find($pid)->estado_id === 4, 'El comercial lo envía: queda en Validación líder (4)');
    $check($correo('presupuestoValidacionLiderComercial', $ultimoLog) >= 1, 'Sale el correo al líder comercial');
    $check($st($abrir($camilo, 8575)->call('new_event')) === 403, 'Mientras está con el líder, el comercial no lo edita');
    $check($st($abrir($ejecutivo, 6733)->call('validacionLiderComercial')) === 403, 'El ejecutivo no puede validar como líder');
    $check($st($abrir($admin, 8575)->call('validacionGerencia')) === 403, 'Nadie aprueba como Gerencia si no está en estado 5');
    $tl = $abrir($lidercom, 8575);
    $tl->set('clienteSeleccionado', \App\Models\cliente_parametros_cc::value('id'));
    if (!$tl->get('centroCostos')) { $tl->set('centroCostos', 'ZZ9999 - PRUEBA AUDITORIA'); }
    $tl->call('updateCentro');
    $p = PresupuestoProyecto::find($pid);
    $check((int) $p->estado_id === 1 && $p->cod_cc && $p->fecha_cc, 'El líder de su equipo lo aprueba con centro de costos (1)');
    $check(round((float) $p->gestion->fresh()->presto_cot) == round((float) $p->venta_proy), 'El valor aprobado llega a la gestión');

    echo "\nFLUJO < 30 %: comercial → líder → Gerencia → aprobado (presupuesto 6728)\n";
    $pid2 = 6728;
    $abrir($camilo, 8563)->call('aprobacion');
    $desde = (int) NotificacionLog::max('id');
    $tl = $abrir($lidercom, 8563)->set('clienteSeleccionado', \App\Models\cliente_parametros_cc::value('id'));
    $check(!array_key_exists('centroCostos', $tl->payload['serverMemo']['errors'] ?? []) && $tl->get('centroCostos'), 'El centro de costos se calcula al elegir el cliente: '.$tl->get('centroCostos'));
    $tl->call('updateCentro');
    $p2 = PresupuestoProyecto::find($pid2);
    $check((int) $p2->estado_id === 5, 'Con margen de '.round($p2->margen_proy, 1).' % pasa a Validación Gerencia (5)');
    $check($correo('presupuestoAprobacion', $desde) === 1, 'Sale el correo a Gerencia avisando que tiene uno por validar');
    $check($st($abrir($admin, 8563)->call('validacionGerencia')) === 403, 'Un admin sin el permiso aprobar-presupuestos no aprueba (403)');
    $check($st($abrir($lidercom, 8563)->call('validacionGerencia')) === 403, 'El líder comercial sin el permiso no aprueba como Gerencia');
    $desde = (int) NotificacionLog::max('id');
    $tg = $abrir($gerencia, 8563); $tg->call('validacionGerencia');
    $p2 = $p2->fresh();
    $check((int) $p2->estado_id === 1 && $p2->fecha_cc, 'Gerencia lo aprueba (1) con fecha de centro de costos');
    $check(round((float) $p2->gestion->fresh()->presto_cot) == round((float) $p2->venta_proy), 'El valor aprobado por Gerencia llega a la gestión (antes no)');
    $check($correo('presupuestoAprobado', $desde) === 1 && $correo('presupuestoAprobacion', $desde) === 0, 'Sale "aprobado" al comercial y ya no el de "solicita aprobación"');

    echo "\nRECHAZOS\n";
    $abrir($camilo, 8563)->set('justificacion', 'Cambio de cantidades')->call('aprobacion');
    $check((int) PresupuestoProyecto::find($pid2)->estado_id === 4, 'Vuelve a validación del líder');
    $tr = $abrir($lidercom, 8563)->set('justificacion_lider_comercial', 'Revisar proveedor');
    $tr->call('rechazar');
    $check((int) PresupuestoProyecto::find($pid2)->estado_id === 3, 'El líder lo rechaza: vuelve a Editable (3)');
    DB::table('presupuesto_proyecto')->where('id', $pid2)->update(['estado_id' => 5]);
    $check($st($abrir($lidercom, 8563)->set('justificacion_gerencia', 'x')->call('rechazar')) === 403, 'En validación de Gerencia, un líder con equipo no rechaza');
    $abrir($gerencia, 8563)->set('justificacion_gerencia', 'Margen muy bajo')->call('rechazar');
    $check((int) PresupuestoProyecto::find($pid2)->estado_id === 3, 'Gerencia (sin ser el usuario 8 o 10) rechaza');
} catch (\Throwable $e) {
    $mal++; echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
}
echo "\nResultado: $ok correctas, $mal fallas.\n";
