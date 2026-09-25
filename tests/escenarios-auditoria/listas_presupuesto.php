<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Cambios de estado desde las listas de presupuestos. Transacción deshecha al final.
use App\Http\Livewire\Admin\GestionComercial\{ValidacionesPresto, ActualizacionesPresto, GestionPresupuestos};
use App\Models\{User, PresupuestoProyecto, NotificacionLog};
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

$ok = 0; $mal = 0;
$check = function ($c, $t) use (&$ok, &$mal) { if ($c) { $ok++; echo "  ✓ $t\n"; } else { $mal++; echo "  ✗ FALLA: $t\n"; } };
$st = fn ($t) => $t->lastResponse->status();
$u = fn ($id) => User::findOrFail($id);
$correo = fn ($evento, $desde) => NotificacionLog::where('evento', $evento)->where('id', '>', $desde)->count();
$estado = fn ($id) => (int) PresupuestoProyecto::find($id)->estado_id;
$comercial = $u(11); $lidercom = $u(12017); $gerencia = $u(12007); $controller = $u(12016); $admin = $u(12008); $alejandra = $u(94);

DB::beginTransaction();
try {
    echo "QUIÉN PUEDE\n";
    $t = Livewire::actingAs($comercial)->test(ActualizacionesPresto::class, ['rol' => 2]);
    $check($st($t->call('cambioEstado', 5386, 1)) === 403 && $estado(5386) === 4, 'Un comercial no aprueba desde su lista de presupuestos (antes sí)');
    $t = Livewire::actingAs($comercial)->test(ActualizacionesPresto::class, ['rol' => 2])->set('rol', 1);
    $check((int) $t->get('rol') === 2, 'Cambiar el rol de la lista desde el navegador no le muestra la lista de admin');
    $check($st(Livewire::actingAs($lidercom)->test(ValidacionesPresto::class)->call('cambioEstado', 150, 3)) === 403 && $estado(150) === 4, 'El líder no cambia un presupuesto fuera de su equipo');
    $check($st(Livewire::actingAs($lidercom)->test(ValidacionesPresto::class)->call('cambioEstado', 5386, 7)) === 422, 'Un estado que no existe se rechaza');
    $check($st(Livewire::actingAs($admin)->test(GestionPresupuestos::class, ['rol' => 1])->call('cambioEstado', 5227, 3)) === 422 && $estado(5227) === 1, 'Un presupuesto aprobado no se cambia desde la lista');

    echo "\nMARGEN BAJO: solo Gerencia aprueba\n";
    DB::table('presupuesto_proyecto')->where('id', 5386)->update(['margen_proy' => 25]);
    $desde = (int) NotificacionLog::max('id');
    Livewire::actingAs($lidercom)->test(ValidacionesPresto::class)->call('cambioEstado', 5386, 1);
    $check($estado(5386) === 5, 'El líder elige "Aprobado" con margen 25 %: queda en Validación Gerencia (regla: menor al 30 %)');
    $check($correo('presupuestoAprobacion', $desde) === 1, 'Gerencia recibe el aviso');
    $check($st(Livewire::actingAs($controller)->test(ActualizacionesPresto::class, ['rol' => 11])->call('cambioEstado', 5386, 1)) === 403 && $estado(5386) === 5, 'Controller no aprueba lo que está en validación de Gerencia');
    $desde = (int) NotificacionLog::max('id');
    $ventaAntes = (float) PresupuestoProyecto::find(5386)->venta_proy;
    Livewire::actingAs($gerencia)->test(ActualizacionesPresto::class, ['rol' => 1])->call('cambioEstado', 5386, 1);
    $p = PresupuestoProyecto::find(5386);
    $check((int) $p->estado_id === 1, 'Gerencia lo aprueba desde Actualizaciones');
    $check((int) $p->gestion->id_estado === 5, 'La gestión sigue en Venta (antes volvía a Propuesta)');
    $check(round((float) $p->gestion->presto_cot) == round($ventaAntes), 'El valor aprobado llega a la gestión');
    $check($correo('presupuestoAprobado', $desde) === 1, 'Sale el correo de aprobado');

    echo "\nMARGEN SUFICIENTE\n";
    DB::table('presupuesto_proyecto')->where('id', 5386)->update(['estado_id' => 4, 'margen_proy' => 30]);
    Livewire::actingAs($controller)->test(GestionPresupuestos::class, ['rol' => 11])->call('cambioEstado', 5386, 1);
    $check($estado(5386) === 1, 'Con margen de 30 % exacto no hace falta Gerencia (la regla es menor al 30 %)');
    DB::table('presupuesto_proyecto')->where('id', 5386)->update(['estado_id' => 4, 'margen_proy' => 33]);
    Livewire::actingAs($controller)->test(GestionPresupuestos::class, ['rol' => 11])->call('cambioEstado', 5386, 1);
    $check($estado(5386) === 1, 'Controller aprueba desde Gestión de presupuestos con margen 33 % sin pasar por Gerencia');
    DB::table('presupuesto_proyecto')->where('id', 5386)->update(['estado_id' => 4, 'cod_cc' => null]);
    $check($st(Livewire::actingAs($lidercom)->test(ValidacionesPresto::class)->call('cambioEstado', 5386, 1)) === 422 && $estado(5386) === 4, 'Sin centro de costos no se aprueba');
    $desde = (int) NotificacionLog::max('id');
    Livewire::actingAs($lidercom)->test(ValidacionesPresto::class)->call('cambioEstado', 5386, 3);
    $check($estado(5386) === 3 && $correo('presupuestoRechazado', $desde) === 1, 'El líder lo rechaza y sale el correo');
} catch (\Throwable $e) {
    $mal++; echo "\n  ✗ ERROR INESPERADO: ".get_class($e).': '.$e->getMessage()."\n    en ".$e->getFile().':'.$e->getLine()."\n";
} finally {
    DB::rollBack();
}
echo "\nResultado: $ok correctas, $mal fallas.\n";
