<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Prueba de la ficha (Livewire) del anticipo jurídico, rol por rol. Transacción deshecha al final.
use App\Http\Livewire\Produccion\AnticipoJuridico;
use App\Http\Livewire\Produccion\AnticiposJuridicos;
use App\Models\Anticipo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

$ok = 0; $mal = 0; $archivos = [];
$check = function ($cond, $txt) use (&$ok, &$mal) { if ($cond) { $ok++; echo "  ✓ $txt\n"; } else { $mal++; echo "  ✗ FALLA: $txt\n"; } };
$u = fn ($email) => User::where('email', $email)->firstOrFail();
$prod = $u('prueba.productor@local.test'); $lider = $u('prueba.lider@local.test'); $ctrl = $u('prueba.controller@local.test');
$ger = $u('prueba.gerencia@local.test'); $admin = $u('prueba.admin@local.test'); $cont = $u('prueba.contabilidad@local.test');
$teso = $u('prueba.tesoreria@local.test'); $otro = $u('prueba.multirol@local.test');
$pdf = fn ($n) => UploadedFile::fake()->createWithContent($n, "%PDF-1.4\n%%EOF");
$html = fn ($t) => html_entity_decode(strip_tags($t->payload['effects']['html'] ?? ''), ENT_QUOTES);
$ver = function ($user, $id) { return Livewire::actingAs($user)->test(AnticipoJuridico::class, ['anticipo_id' => $id]); };
$estado = fn ($id) => (int) Anticipo::find($id)->estado_id;
// Livewire guarda las listas de archivos temporales como 'livewire-files:[...]'
$nArchivos = function ($v) { if (is_string($v) && str_starts_with($v, 'livewire-files:')) { return count(json_decode(substr($v, 15), true)); } return is_array($v) ? count($v) : -1; };

DB::beginTransaction();
try {
    // Los anticipos de demostración (#4, #5) son del mismo proveedor y proyecto: se anulan solo dentro de la prueba
    DB::table('anticipos')->where('tipo', 'juridico')->where('presupuesto_id', 6733)->whereNotIn('estado_id', [6, 14])->update(['estado_id' => 6]);
    echo "SOLICITUD (productor, borrador sin orden)\n";
    $t = Livewire::actingAs($prod)->test(AnticipoJuridico::class);
    $check(str_contains($html($t), 'Solicitar un anticipo jurídico') && str_contains($html($t), 'Tengo la orden de compra'), 'La pantalla nueva explica el flujo y ofrece los dos caminos');
    $t->set('modo', 'borrador')->set('presupuesto_id', 6733)->set('buscarProveedor', 'IGLU MARKETING');
    $check(str_contains($html($t), 'IGLU MARKETING DIGITAL SAS'), 'El buscador encuentra al proveedor jurídico');
    $t->call('elegirProveedor', 285)->set('valor_base', '12.000.000')->set('porcentaje', 50);
    $txt = $html($t);
    $check(str_contains($txt, '$6.000.000') && str_contains($txt, 'no pasa por Gerencia'), 'Calcula 12.000.000 × 50 % = $6.000.000 y avisa que no pasa por Gerencia');
    $check(str_contains($txt, 'El acuerdo registrado con este proveedor es de 0 %'), 'Advierte que pide más que el acuerdo con el proveedor (0 %)');
    $t->call('enviar');
    $check(isset($t->payload['serverMemo']['errors']['soportes']), 'Sin soporte no deja enviar el borrador');
    $t->set('nuevos', [$pdf('cotizacion.pdf')]);
    $t->set('nuevos', [UploadedFile::fake()->image('remision.jpg')]);
    $check($nArchivos($t->get('soportes')) === 2, 'Dos cargas de archivos se suman (no se reemplazan)');
    $t->call('quitarSoporteNuevo', 1);
    $check($nArchivos($t->get('soportes')) === 1, 'Se puede quitar un soporte antes de enviar');
    $t->set('observacion', 'Reserva de fecha del evento.')->call('enviar');
    $id = Anticipo::max('id');
    $archivos = array_merge($archivos, Anticipo::find($id)->evidencias()->pluck('foto_evidencia')->all());
    $t->assertRedirect(route('anticipo-prod', ['anticipo_id' => $id]));
    $check($estado($id) === 8, "Anticipo #$id creado desde la pantalla, en Revisión líder");

    echo "\nACCESO\n";
    $check($ver($otro, $id)->lastResponse->status() === 403, 'Otro productor recibe 403 al abrirlo');
    $check($ver($prod, 2)->lastResponse->status() === 403, 'El productor no abre anticipos ajenos (403)');
    $check($ver($admin, 99999)->lastResponse->status() === 404, 'Un anticipo que no existe da 404');
    $p = $ver($prod, $id);
    $check(str_contains($html($p), 'Lo está revisando el líder de producción') && !str_contains($html($p), 'Te toca'), 'El productor ve en qué va, sin botones de revisión');
    $a = $ver($admin, $id);
    $check(!str_contains($html($a), 'Te toca') && str_contains($html($a), 'Le toca al líder de producción'), 'Admin solo consulta y ve a quién le toca');

    echo "\nLÍDER: devuelve y luego aprueba\n";
    $l = $ver($lider, $id);
    $check(str_contains($html($l), 'Te toca · líder de producción') && str_contains($html($l), 'Aprobar y pasar al Controller'), 'El líder ve su paso y qué pasa si aprueba');
    $l->call('decidir', 'rechazar_lider');
    $check(isset($l->payload['serverMemo']['errors']['observacion']), 'Devolver sin motivo muestra error en la observación');
    $l->set('obs_decision', 'Adjunta la cotización firmada.')->call('decidir', 'rechazar_lider');
    $check($estado($id) === 11 && str_contains((string) $l->get('hecho'), 'Devuelto al productor'), 'Devuelto al productor (11) con mensaje de confirmación');

    $p = $ver($prod, $id);
    $txt = $html($p);
    $check(str_contains($txt, 'te lo devolvió') && str_contains($txt, 'Adjunta la cotización firmada.'), 'El productor ve quién lo devolvió y por qué');
    $check($p->get('porcentaje') === '50' && $p->get('proveedor_id') === '285', 'El formulario de corrección llega lleno con lo que había pedido');
    $p->set('porcentaje', 40)->call('enviar');
    $check(isset($p->payload['serverMemo']['errors']['observacion']), 'La corrección exige decir qué se corrigió');
    $p->set('nuevos', [$pdf('cotizacion-firmada.pdf')])->set('observacion', 'Adjunto la cotización firmada y bajo al 40 %.')->call('enviar');
    $archivos = array_merge($archivos, Anticipo::find($id)->evidencias()->pluck('foto_evidencia')->all());
    $check($estado($id) === 8 && (float) Anticipo::find($id)->total_anticipo === 4800000.0, 'Reenviado: vuelve al líder con total 4.800.000');

    $ver($lider, $id)->set('obs_decision', 'Ok.')->call('decidir', 'aprobar_lider');
    $check($estado($id) === 15, 'Líder aprueba → Controller (15)');

    echo "\nCONTROLLER, CONTABILIDAD Y TESORERÍA\n";
    $c = $ver($ctrl, $id);
    $check(str_contains($html($c), 'queda aprobado: se genera la orden de anticipo'), 'El Controller ve que al aprobar queda aprobado (≤ $10 M)');
    $c->call('decidir', 'aprobar_controller');
    $an = Anticipo::find($id); $archivos[] = $an->archivo_orden_anticipo;
    $check($an->estado_id == 1 && str_contains((string) $c->get('hecho'), $an->cod_orden_anticipo), "Aprobado con la orden {$an->cod_orden_anticipo} y el mensaje la nombra");

    $k = $ver($cont, $id);
    $check(str_contains($html($k), 'Causar el anticipo') && str_contains($html($k), 'Orden de anticipo '.$an->cod_orden_anticipo), 'Contabilidad ve su paso y el PDF de la orden');
    $k->set('cod_causal', 'ABC')->call('decidir', 'causar');
    $check(isset($k->payload['serverMemo']['errors']['cod_causal']), 'Causación con letras muestra el error en su campo');
    $k->set('cod_causal', '4455')->call('decidir', 'causar');
    $check($estado($id) === 5, 'Causado (5)');

    $s = $ver($teso, $id);
    $s->call('decidir', 'pagar');
    $check(isset($s->payload['serverMemo']['errors']['comprobante']), 'Pagar sin comprobante muestra el error');
    $s->set('comprobante', $pdf('pago.pdf'))->call('decidir', 'pagar');
    $an = Anticipo::find($id); $archivos[] = $an->comprobante_pago;
    $check($an->estado_id == 14, 'Pagado (14) desde la pantalla de Tesorería');
    $fin = $ver($prod, $id);
    $txt = $html($fin);
    $check(str_contains($txt, 'Flujo terminado') && str_contains($txt, 'Comprobante de pago') && substr_count($txt, 'Aprobó') >= 2, 'Al final el productor ve el flujo terminado, los documentos y el recorrido');

    echo "\nGERENCIA (> $10 M) y bandejas\n";
    DB::table('ordenes_compra')->where('id', 11428)->update(['estado_id' => 1]); // 36.000.000, solo dentro de la prueba
    $g = Livewire::actingAs($prod)->test(AnticipoJuridico::class)->set('oc_id', 11428)->set('porcentaje', 30);
    $check(str_contains($html($g), 'lo aprueba Gerencia'), 'Con OC de 36 M al 30 % (10,8 M) avisa que lo aprueba Gerencia');
    $g->call('enviar');
    $id2 = Anticipo::max('id');
    $ver($lider, $id2)->call('decidir', 'aprobar_lider');
    $ver($ctrl, $id2)->call('decidir', 'aprobar_controller');
    $check($estado($id2) === 9, 'Tras líder y Controller queda en Revisión gerencia (9)');
    $check(!str_contains($html($ver($admin, $id2)), 'Te toca'), 'Admin sin permiso de Gerencia no ve botones');
    $gg = $ver($ger, $id2);
    $check(str_contains($html($gg), 'Te toca · Gerencia'), 'Gerencia (con el permiso) ve su paso');
    $gg->call('decidir', 'aprobar_gerencia');
    $archivos[] = Anticipo::find($id2)->archivo_orden_anticipo;
    $check($estado($id2) === 1, 'Gerencia aprueba → Aprobado (1)');

    // Bandejas de cada rol con anticipos jurídicos (incluido uno sin orden de compra)
    DB::table('anticipos')->where('id', $id)->update(['estado_id' => 15]);
    $lista = Livewire::actingAs($ctrl)->test(AnticiposJuridicos::class);
    $check(str_contains($html($lista), 'Anticipo #'.$id) && str_contains($html($lista), 'Sin orden de compra'), 'Bandeja del Controller: muestra el borrador por revisar');
    $lista->set('vista', 'todos');
    $check(str_contains($html($lista), 'Anticipo #'.$id2), 'Bandeja del Controller, pestaña Todos');
    DB::table('anticipos')->where('id', $id)->update(['estado_id' => 8]);
    $check(str_contains($html(Livewire::actingAs($lider)->test(\App\Http\Livewire\LiderProduccion\Anticipos::class)), 'Anticipo #'.$id), 'Bandeja del líder: ahora sí ve los anticipos jurídicos');
    DB::table('anticipos')->where('id', $id)->update(['estado_id' => 1]);
    $check(str_contains($html(Livewire::actingAs($cont)->test(\App\Http\Livewire\Contabilidad\Produccion\Anticipos::class)), 'Anticipo #'.$id), 'Bandeja de Contabilidad: borrador sin orden no rompe la lista');
    DB::table('anticipos')->where('id', $id)->update(['estado_id' => 5, 'comprobante_pago' => null]);
    $check(str_contains($html(Livewire::actingAs($teso)->test(\App\Http\Livewire\Tesoreria\Produccion\Anticipos::class)), 'Anticipo #'.$id), 'Bandeja de Tesorería: borrador sin orden no rompe la lista');
    DB::table('anticipos')->where('id', $id2)->update(['estado_id' => 9]);
    $check(str_contains($html(Livewire::actingAs($ger)->test(\App\Http\Livewire\Admin\Produccion\Anticipos::class)), 'Anticipo #'.$id2), 'Bandeja de Gerencia (admin): ve el que supera el umbral');
    $check(str_contains($html(Livewire::actingAs($prod)->test(\App\Http\Livewire\Productor\Anticipos\Anticipos::class)), 'Anticipo #'.$id), 'Lista del productor: ve sus anticipos jurídicos con botón');

    // Anticipos existentes (flujo anterior) abren sin errores en cada estado
    foreach ([1, 2, 3] as $viejo) {
        $txt = $html($ver($admin, $viejo));
        $check(str_contains($txt, 'Anticipo jurídico #'.$viejo), "El anticipo existente #$viejo abre sin errores");
    }
} catch (\Throwable $e) {
    $mal++;
    echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
    foreach (array_filter($archivos) as $r) { Storage::delete($r); }
}
echo "\nResultado: $ok correctas, $mal fallas. (Transacción deshecha; archivos de prueba borrados.)\n";
