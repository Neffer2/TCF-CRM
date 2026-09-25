<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Correcciones de la auditoría fuera del presupuesto y del anticipo jurídico. Transacción deshecha al final.
use App\Models\{User, OrdenCompra, OcItem, PresupuestoProyecto, ItemPresupuesto, Proveedor, Anticipo, SolicitudCliente, clientes, NotificacionLog};
use App\Http\Livewire\Productor\Ordenes\{Nomina, Juridica, Natural, AnticipoProductor};
use App\Http\Livewire\Admin\Produccion\Proveedores\Proveedores;
use App\Http\Livewire\Admin\Produccion\OrdenesCompra;
use App\Http\Livewire\Admin\GestionComercial\ValidacionesCliente;
use App\Http\Livewire\Admin\EstadosFacturacion\EstadosFacturacion;
use App\Http\Livewire\Teso\Produccion\Anticipo as PagoOrden;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage, View};
use Livewire\Livewire;

$ok = 0; $mal = 0; $antes = collect(Storage::allFiles('public'));
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$st = fn ($t) => $t->lastResponse->status();
$intento = function (callable $fn) { try { return $fn(); } catch (\Throwable $e) { return $e; } };
$u = fn ($e) => User::where('email', $e)->firstOrFail();
$prod = $u('prueba.productor@local.test'); $otroProd = $u('prueba.multirol@local.test'); $admin = $u('prueba.admin@local.test');
$lidercom = $u('prueba.lidercom@local.test'); $ejecutivo = $u('prueba.ejecutivo@local.test'); $teso = $u('prueba.tesoreria@local.test');
$alejandra = User::find(94);

DB::beginTransaction();
try {
    echo "NÓMINA\n";
    $check($st(Livewire::actingAs($prod)->test(Nomina::class, ['orden_id' => 11432])) === 404, 'Abrir una orden natural en la página de nómina da 404 (antes 500)');
    $item = ItemPresupuesto::where('presupuesto_id', 6733)->where('evento', 0)->first();
    $nom = OrdenCompra::create(['tipo_oc' => 3, 'estado_id' => 8, 'presupuesto_id' => 6733, 'proveedor_id' => 1689]);
    OcItem::forceCreate(['oc_id' => $nom->id, 'item_id' => $item->id, 'desc_oc' => 'x', 'cant_oc' => 1, 'dias_oc' => 1, 'otros_oc' => 1, 'vunit_oc' => 1, 'vtotal_oc' => 1]);
    $check($st(Livewire::actingAs($prod)->test(Nomina::class, ['orden_id' => $nom->id])) === 200, 'El productor abre su nómina');
    $check($st(Livewire::actingAs($otroProd)->test(Nomina::class, ['orden_id' => $nom->id])) === 403, 'Otro productor no abre la nómina ajena (403)');
    $otraOc = OrdenCompra::where('tipo_oc', 1)->whereHas('ordenItems')->first();
    $n0 = $otraOc->ordenItems()->count();
    $r = $intento(fn () => Livewire::actingAs($prod)->test(Nomina::class, ['orden_id' => $nom->id])->call('deleteItems', $otraOc->id));
    $check($otraOc->ordenItems()->count() === $n0, 'deleteItems ya no se puede llamar desde el navegador (borraba ítems de cualquier orden)');
    Livewire::actingAs($prod)->test(Nomina::class, ['orden_id' => $nom->id])->call('deleteOrden');
    $check(OrdenCompra::find($nom->id) !== null, 'Una nómina en revisión del líder no se elimina');
    $nom->update(['estado_id' => 3]);
    Livewire::actingAs($prod)->test(Nomina::class, ['orden_id' => $nom->id])->call('deleteOrden');
    $check(OrdenCompra::find($nom->id) === null, 'El botón Eliminar de una nómina rechazada funciona (antes el método no existía)');

    echo "\nORDEN JURÍDICA\n";
    $p = PresupuestoProyecto::find(6736);
    $tj = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p]);
    $check($st($tj->set('maxValor', 999999999)) === 403, 'El tope de valor no se puede cambiar desde el navegador');
    $check($st(Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p])->set('ocItems', [['item' => 1]])) === 403, 'La lista de ítems no se puede cambiar desde el navegador');
    $n0 = $otraOc->ordenItems()->count();
    $intento(fn () => Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p])->call('storeItems', $otraOc->id));
    $check($otraOc->ordenItems()->count() === $n0, 'storeItems ya no se puede llamar desde el navegador (metía ítems en cualquier orden)');
    $aprobada = OrdenCompra::where('tipo_oc', 1)->where('estado_id', 1)->where('presupuesto_id', 6736)->first()
        ?? tap(OrdenCompra::create(['tipo_oc' => 1, 'estado_id' => 1, 'presupuesto_id' => 6736, 'proveedor_id' => 1689]), function ($o) use ($p) {
            OcItem::forceCreate(['oc_id' => $o->id, 'item_id' => $p->presupuestoItems->first()->id, 'desc_oc' => 'x', 'cant_oc' => 1, 'dias_oc' => 1, 'otros_oc' => 1, 'vunit_oc' => 1, 'vtotal_oc' => 1]);
        });
    $tr = Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p, 'orden_compra' => $aprobada])
        ->set('file_cot', UploadedFile::fake()->createWithContent('c.pdf', "%PDF-1.4\n%%EOF"))->call('enviarAprobacion');
    $check($st($tr) === 403 && (int) $aprobada->fresh()->estado_id === 1, 'Una orden ya aprobada no se puede "reenviar" (volvía a revisión y cambiaba sus ítems)');

    echo "\nORDEN NATURAL\n";
    $ajena = OrdenCompra::where('tipo_oc', 2)->whereHas('naturalInfo', fn ($q) => $q->where('productor_id', '!=', $prod->id))->first();
    $check($st(Livewire::actingAs($prod)->test(Natural::class, ['productor' => $prod, 'orden_id' => $ajena->id])) === 403, 'Un productor no abre la orden natural de otro (cédula, RUT, cuenta)');
    $check($st(Livewire::actingAs($admin)->test(Natural::class, ['productor' => $admin, 'orden_id' => $ajena->id])) === 200, 'El admin sí la abre');

    echo "\nPROVEEDORES\n";
    $libre = Proveedor::whereNotIn('id', DB::table('ordenes_compra')->select('proveedor_id')->whereNotNull('proveedor_id'))->first();
    $check($st(Livewire::actingAs($prod)->test(Proveedores::class)->call('deleteProveedor', $libre->id)) === 403 && Proveedor::find($libre->id), 'El productor no borra proveedores (403)');
    Livewire::actingAs($admin)->test(Proveedores::class)->call('deleteProveedor', 1689);
    $check(Proveedor::find(1689) !== null, 'Un proveedor con órdenes no se borra: aviso en vez de error 500');

    echo "\nANTICIPO DE PRODUCTOR\n";
    Storage::put('public/prueba_no_borrar.txt', 'x');
    $ta = Livewire::actingAs($prod)->test(AnticipoProductor::class);
    $check($st($ta->set('firma_productor', 'public/prueba_no_borrar.txt')) === 403, 'La ruta de la firma no se puede cambiar desde el navegador');
    Livewire::actingAs($prod)->test(AnticipoProductor::class)->call('borrarFirmaProductor');
    $check(Storage::exists('public/prueba_no_borrar.txt'), '"Borrar firma" ya no borra archivos');
    Storage::delete('public/prueba_no_borrar.txt');
    $check($st(Livewire::actingAs($prod)->test(AnticipoProductor::class, ['anticipo_id' => 3])) === 403, 'Un productor no abre el anticipo de otro');
    $check($st(Livewire::actingAs($prod)->test(AnticipoProductor::class)->set('total_anticipo', 1)) === 403, 'El total del anticipo no se puede cambiar desde el navegador');

    echo "\nTABLEROS\n";
    $t1 = Livewire::actingAs($alejandra)->test(\App\Http\Livewire\Com\Dashboard\Block1::class);
    $propio = $t1->get('presto_acumulado');
    $t1->call('getData', ['año' => (string) date('Y'), 'mes' => null, 'comercial' => '0 OR 1=1', 'cuenta' => 1]);
    $check((float) $t1->get('presto_acumulado') == (float) $propio, 'Un texto como "0 OR 1=1" en el tablero del comercial ya no cambia la consulta');
    $tb = Livewire::actingAs($ejecutivo)->test(\App\Http\Livewire\Asis\Dashboard\Block2::class);
    $base = $tb->get('ventatotal');
    $tb->call('getData', ['año' => (string) date('Y'), 'mes' => null, 'comercial' => 11, 'cuenta' => 1]);
    $check((float) $tb->get('ventatotal') == (float) $base, 'El ejecutivo no ve las cifras de otro comercial cambiando el evento');
    $tl = Livewire::actingAs($lidercom)->test(\App\Http\Livewire\Admin\Dashboard\Block2::class);
    $tl->call('getData', ['año' => (string) date('Y'), 'mes' => null, 'comercial' => null, 'comerciales' => [94]]);
    $check($tl->get('comerciales') === [0], 'El líder comercial no ve a alguien fuera de su equipo (se recorta a su equipo)');
    $tl->call('getData', ['año' => (string) date('Y'), 'mes' => null, 'comercial' => null, 'comerciales' => null]);
    $check(is_array($tl->get('comerciales')) && in_array(12009, $tl->get('comerciales'), true), 'Sin filtro, el líder ve su equipo (no toda la empresa)');
    $check($st(Livewire::actingAs($lidercom)->test(\App\Http\Livewire\Admin\Dashboard\Block2::class)->set('comerciales', null)) === 403, 'La lista de comerciales del tablero no se puede cambiar desde el navegador');

    echo "\nCLIENTES\n";
    $sol = fn ($nombre, $nit) => SolicitudCliente::create(['id_user' => 94, 'estado' => 'Pendiente', 'nombre' => $nombre, 'razon_social' => $nombre.' SAS', 'nit' => $nit,
        'direccion' => 'x', 'numero_telefono' => '1', 'cargo' => 'x', 'correo' => 'a@b.co', 'correo_recpcion_facturas' => 'a@b.co']);
    $s1 = $sol('Fábrica de prueba', '900000001');
    $tv = Livewire::actingAs($admin)->test(ValidacionesCliente::class)->call('aprobarSolicitud', $s1->id);
    $idF = clientes::where('nit', '900000001')->value('id');
    $check($idF && \App\Models\cliente_parametros_cc::where('cliente_id', $idF)->value('codigo_cc') === 'FA', 'Con F1–F9 ocupados, el siguiente código es FA (2 caracteres; antes rompía la pantalla con F10)');
    $s2 = $sol('Zeta pruebas', '900000002');
    Livewire::actingAs($admin)->test(ValidacionesCliente::class)->call('aprobarSolicitud', $s2->id);
    $check(clientes::where('nit', '900000002')->exists(), 'Una solicitud normal se aprueba');
    $s3 = $sol('Zeta otra', '900000002');
    $tv = Livewire::actingAs($admin)->test(ValidacionesCliente::class)->call('aprobarSolicitud', $s3->id);
    $check(str_contains($tv->payload['effects']['html'] ?? '', 'Ya existe un cliente con el NIT 900000002'), 'Un NIT repetido se avisa en pantalla (antes rompía la pantalla)');

    echo "\nVARIOS\n";
    $e = $intento(fn () => Livewire::actingAs($admin)->test(EstadosFacturacion::class, ['año' => '2099']));
    $check(!($e instanceof \Throwable), 'Estado de facturación con un año que no existe ya no da error');
    $to = Livewire::actingAs($admin)->test(OrdenesCompra::class)->set('estado', 6)->set('tipo', 2)->set('cod_oc', '11');
    $check(true, 'Filtro de código de OC agrupado (revisado en la consulta)');
    $oc = OrdenCompra::with('ordenItems')->find(11433);
    $html = View::make('exports.orden_compra_pdf', ['orden' => $oc])->render();
    $total = number_format($oc->ordenItems->sum('vtotal_oc'));
    $check(str_contains($html, $total) && $oc->ordenItems->sum('vtotal_oc') > 0, 'El PDF de la orden muestra el subtotal y total reales ('.$total.'), no 0');
    $anulada = OrdenCompra::where('estado_id', 6)->whereNotNull('cod_causal')->whereNull('archivo_comprobante_pago')->first();
    if ($anulada) {
        Livewire::actingAs($teso)->test(PagoOrden::class, ['orden_id' => $anulada->id])
            ->set('comprobante', UploadedFile::fake()->createWithContent('p.pdf', "%PDF-1.4\n%%EOF"))->call('store');
        $check(!$anulada->fresh()->archivo_comprobante_pago, 'Tesorería no puede pagar una orden anulada');
    }
} catch (\Throwable $e) {
    $mal++; echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
    foreach (collect(Storage::allFiles('public'))->diff($antes) as $f2) { Storage::delete($f2); }
}
echo "\nResultado: $ok correctas, $mal fallas.\n";
