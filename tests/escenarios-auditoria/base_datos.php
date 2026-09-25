<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Mejoras de base de datos del 25-sep-2026: roles, código puro del centro de costos, meses y ciudades por catálogo,
// historial de estados y papelera (borrado lógico). Transacción deshecha al final.
use App\Models\{User, Rol, GestionComercial, PresupuestoProyecto, Base_comercial, OrdenCompra, OcItem, ItemPresupuesto, Proveedor, Tercero, Ciudad, HistorialEstado};
use App\Http\Livewire\Admin\Papelera;
use App\Http\Livewire\Admin\Produccion\Proveedores\NuevoProveedor;
use App\Http\Livewire\Productor\Ordenes\Juridica;
use App\Rules\CentroCostos;
use Illuminate\Support\Facades\{Auth, DB, Schema};
use Livewire\Livewire;

$ok = 0; $mal = 0;
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$st = fn ($t) => $t->lastResponse->status();
$u = fn ($e) => User::where('email', $e)->firstOrFail();
$admin = $u('prueba.admin@local.test'); $comercial = $u('prueba.comercial@local.test'); $prod = $u('prueba.productor@local.test');

DB::beginTransaction();
try {
    echo "ESTRUCTURA\n";
    $check(Schema::hasTable('roles') && !Schema::hasTable('roles_user') && Rol::count() >= 10, 'El catálogo de roles se llama roles y el modelo Rol lo lee');
    $check($admin->puedeUsarRol(1) && $admin->roles()->count() >= 1, 'Los roles de la cuenta se leen con la tabla renombrada');
    $check(Schema::hasColumn('items_presupuesto', 'mes_id') && !Schema::hasColumn('items_presupuesto', 'mes'), 'items_presupuesto.mes (texto) pasó a mes_id con llave al catálogo meses');
    $check(Schema::hasTable('ciudades') && Schema::hasTable('historial_estados') && Schema::hasColumn('presupuesto_proyecto', 'centro_costos') && Schema::hasColumn('gestion_comercial', 'deleted_at'), 'Existen ciudades, historial_estados, centro_costos y deleted_at');
    $lista = app('ciudades');
    $check(is_array($lista) && count($lista) > 50 && $lista[0] === 'NACIONAL' && in_array('BOGOTA D.C', $lista, true), 'La lista de ciudades de los formularios sale de la tabla, en el orden oficial');
    $tipo = DB::selectOne("SELECT column_type t FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'base_comerciales' AND column_name = 'porcentaje'")->t;
    $check(str_starts_with(strtolower($tipo), 'decimal'), 'base_comerciales.porcentaje es DECIMAL (era texto)');
    $check((int) DB::selectOne("SELECT COUNT(*) n FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND constraint_type = 'CHECK' AND constraint_name LIKE 'chk_%'")->n >= 3, 'Los dominios cortos (tipo de anticipo, estado de notificación, tipo de actividad) tienen CHECK');
    $check(!Schema::hasColumn('presupuesto_proyecto', 'comercial_id'), 'La columna muerta presupuesto_proyecto.comercial_id ya no existe');

    echo "\nCENTRO DE COSTOS PURO\n";
    $check(PresupuestoProyecto::codigoPuro('08230801-TROPAS TAKIS') === '08230801' && PresupuestoProyecto::codigoPuro('74230102 - Proyecto X') === '74230102'
        && PresupuestoProyecto::codigoPuro('NOMBRE 44231013 PH') === '44231013' && PresupuestoProyecto::codigoPuro('PRUEBA') === null && PresupuestoProyecto::codigoPuro('ACTIVADORES 1-7 SEPTIEMBRE') === null,
        'codigoPuro saca el código de lo digitado (con guion, tabulación o palabra delante) y no toma palabras como código');
    $p = PresupuestoProyecto::findOrFail(5227);
    $check($p->centro_costos === 'C3251066' && str_starts_with($p->cod_cc, 'C3251066'), 'El presupuesto 5227 tiene su código puro (C3251066) además de lo digitado');
    $p->cod_cc = '99260901 - Proyecto de prueba';
    $p->save();
    $check(PresupuestoProyecto::find(5227)->centro_costos === '99260901', 'Al guardar cod_cc se llena centro_costos solo');
    $regla = new CentroCostos;
    $check(!$regla->passes('centroCostos', '99260901 OTRO NOMBRE') && !$regla->passes('centroCostos', '99260901'), 'La regla rechaza el mismo código aunque el nombre pegado sea otro (antes se colaba)');
    $check($regla->passes('centroCostos', '99260902 - libre'), 'La regla acepta un código libre');
    $b = Base_comercial::where('id_gestion', 6733)->firstOrFail();
    $b->cod_cc = 'C3260199 Proyecto base';
    $b->save();
    $check(Base_comercial::find($b->id)->centro_costos === 'C3260199', 'La base comercial también guarda el código puro');

    echo "\nMESES Y CIUDADES EN LOS ÍTEMS\n";
    $item = ItemPresupuesto::where('presupuesto_id', 5227)->where('evento', 0)->firstOrFail();
    $item->mes_id = 48;
    $item->ciudad = 'Bogotá, D.C.';
    $item->save();
    $item = ItemPresupuesto::find($item->id);
    $bogota = Ciudad::where('clave', 'BOGOTA DC')->first();
    $check($bogota && (int) $item->ciudad_id === (int) $bogota->id && $item->ciudad === 'Bogotá, D.C.', 'Una ciudad escrita distinto ("Bogotá, D.C.") enlaza con BOGOTA D.C del catálogo y el texto se conserva');
    $check(optional($item->mesDescription)->description === 'Diciembre', 'El mes del ítem se lee del catálogo por mes_id');
    $item->ciudad = 'Villa Nueva De Prueba';
    $item->save();
    $nuevaCiudad = Ciudad::where('clave', 'VILLA NUEVA DE PRUEBA')->first();
    $check($nuevaCiudad && !$nuevaCiudad->activa && (int) ItemPresupuesto::find($item->id)->ciudad_id === (int) $nuevaCiudad->id, 'Una ciudad desconocida se crea en el catálogo como inactiva (no sale en desplegables) y el dato no se pierde');
    $check(Ciudad::clave("BOGOT\xC1 D.C.") === 'BOGOTA DC' && Ciudad::clave('  Medellín ') === 'MEDELLIN', 'La clave de ciudad tolera bytes latin1 viejos, tildes, puntos y espacios');

    echo "\nHISTORIAL DE ESTADOS\n";
    Auth::setUser($admin);
    $oc = OrdenCompra::where('tipo_oc', 1)->where('estado_id', 1)->orderByDesc('id')->firstOrFail();
    $antesN = HistorialEstado::where('tabla', 'ordenes_compra')->where('registro_id', $oc->id)->count();
    $check($antesN >= 1, 'Toda orden trae al menos su estado de partida en el historial (carga inicial)');
    $oc->estado_id = 4;
    $oc->observacion_remision = 'Prueba del historial';
    $oc->save();
    $h = HistorialEstado::where('tabla', 'ordenes_compra')->where('registro_id', $oc->id)->orderByDesc('id')->first();
    $check($h && (int) $h->estado_anterior === 1 && (int) $h->estado_nuevo === 4 && (int) $h->user_id === (int) $admin->id && $h->observacion === 'Prueba del historial', 'Cambiar el estado escribe una fila: de 1 a 4, quién lo hizo y la observación de ese guardado');
    $check($oc->historialEstados()->count() === $antesN + 1 && $h->nombreEstado(4) !== '' && $h->nombreEstado(4) !== '4', 'La relación historialEstados devuelve las filas y traduce el estado a su nombre');
    $nuevaOc = OrdenCompra::create(['tipo_oc' => 1, 'estado_id' => 3, 'presupuesto_id' => 6733, 'proveedor_id' => $oc->proveedor_id]);
    $hn = HistorialEstado::where('tabla', 'ordenes_compra')->where('registro_id', $nuevaOc->id)->first();
    $check($hn && $hn->estado_anterior === null && (int) $hn->estado_nuevo === 3, 'Crear una orden deja su primera fila (sin estado anterior)');
    app('auth')->forgetGuards();

    echo "\nPAPELERA\n";
    $nBase = Base_comercial::where('id_gestion', 6733)->count();
    GestionComercial::findOrFail(6733)->delete();
    $check(GestionComercial::find(6733) === null && GestionComercial::onlyTrashed()->find(6733) !== null, 'Eliminar un proyecto lo manda a la papelera (sigue en la base)');
    $check(PresupuestoProyecto::find(5227) === null && PresupuestoProyecto::onlyTrashed()->find(5227) !== null, 'Su presupuesto se va con él');
    $check(Base_comercial::where('id_gestion', 6733)->count() === 0 && Base_comercial::onlyTrashed()->where('id_gestion', 6733)->count() === $nBase, 'Su registro de base comercial también');
    $check(!$regla->passes('centroCostos', '99260901 - de nuevo'), 'El código de un presupuesto en la papelera sigue ocupado (un centro de costos de Helisa no se reutiliza)');
    $check($st(Livewire::actingAs($comercial)->test(Papelera::class)) === 403, 'Un comercial no abre la papelera (403)');
    $tp = Livewire::actingAs($admin)->test(Papelera::class)->set('tipo', 'gestiones');
    $check($st($tp) === 200 && str_contains((string) $tp->lastRenderedDom, 'Restaurar'), 'Administración ve la papelera de proyectos con el botón Restaurar');
    $tp->call('restaurar', 6733);
    $check(GestionComercial::find(6733) !== null && PresupuestoProyecto::find(5227) !== null && Base_comercial::where('id_gestion', 6733)->count() === $nBase, 'Restaurar desde la papelera devuelve el proyecto con su presupuesto y su base comercial');

    $p6733 = PresupuestoProyecto::findOrFail(6733);
    $itemPresto = ItemPresupuesto::where('presupuesto_id', 6733)->where('evento', 0)->firstOrFail();
    $consumidoAntes = $itemPresto->consumidos()->count();
    $borrador = OrdenCompra::create(['tipo_oc' => 1, 'estado_id' => 3, 'presupuesto_id' => 6733, 'proveedor_id' => $oc->proveedor_id]);
    OcItem::forceCreate(['oc_id' => $borrador->id, 'item_id' => $itemPresto->id, 'desc_oc' => 'x', 'cant_oc' => 1, 'dias_oc' => 1, 'otros_oc' => 1, 'vunit_oc' => 1000, 'vtotal_oc' => 1000]);
    $check($itemPresto->consumidos()->count() === $consumidoAntes + 1, 'La línea del borrador consume el ítem mientras la orden está viva');
    Livewire::actingAs($prod)->test(Juridica::class, ['presupuesto' => $p6733, 'orden_compra' => $borrador])->call('deleteOrden');
    $check(OrdenCompra::find($borrador->id) === null && OrdenCompra::onlyTrashed()->find($borrador->id) !== null && OcItem::where('oc_id', $borrador->id)->count() === 1, 'Eliminar un borrador de orden lo manda a la papelera con sus líneas (antes se borraban)');
    $check($itemPresto->consumidos()->count() === $consumidoAntes, 'Las líneas de una orden en la papelera no consumen el ítem');
    Livewire::actingAs($admin)->test(Papelera::class)->set('tipo', 'ordenes')->call('restaurar', $borrador->id);
    $check(OrdenCompra::find($borrador->id) !== null && $itemPresto->consumidos()->count() === $consumidoAntes + 1, 'Restaurada la orden, sus líneas vuelven a consumir');

    $pv = Proveedor::whereNotNull('documento')->where('documento', '<>', '')->orderByDesc('id')->firstOrFail();
    $pv->delete();
    $check(Proveedor::find($pv->id) === null && Proveedor::onlyTrashed()->find($pv->id) !== null, 'Un proveedor eliminado va a la papelera');
    $tn = Livewire::actingAs($admin)->test(NuevoProveedor::class)->set('documento', $pv->documento)->call('store');
    $check(str_contains(implode(' ', $tn->instance()->getErrorBag()->get('documento')), 'papelera'), 'Crear otro proveedor con el mismo documento avisa que está en la papelera');
    Livewire::actingAs($admin)->test(Papelera::class)->set('tipo', 'proveedores')->call('restaurar', $pv->id);
    $check(Proveedor::find($pv->id) !== null, 'El proveedor se restaura desde la papelera');
    $t = Tercero::orderByDesc('id')->firstOrFail();
    $t->delete();
    $check(Tercero::find($t->id) === null && Tercero::onlyTrashed()->find($t->id) !== null, 'Una persona (tercero) eliminada va a la papelera');
    $t->restore();
    $check(Tercero::find($t->id) !== null, 'y se restaura');
} finally {
    DB::rollBack();
}
echo "\nResultado: $ok correctas, $mal fallas\n";
