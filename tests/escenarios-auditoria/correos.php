<?php
// Solo en la base local de pruebas: los correos y SMS deben estar simulados (se deshace la base, pero un correo real no).
$__bd = (string) config('database.connections.'.config('database.default').'.database');
if (app()->environment('production') || config('mail.default') !== 'log' || config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || !($__bd === 'tfc_crm' || str_ends_with($__bd, '_testing'))) {
    echo "Detenido: estos escenarios solo corren en local (base tfc_crm o *_testing y MAIL_MAILER=log).\n"; return;
}
// Llama cada función pública del trait Email con datos reales (correo simulado, transacción deshecha).
use App\Models\{PresupuestoProyecto, OrdenCompra, Anticipo, User, GestionComercial, NotificacionLog};
use Illuminate\Support\Facades\DB;
DB::beginTransaction();
$x = new class { use \App\Traits\Email; };
$presto = PresupuestoProyecto::whereNotNull('cod_cc')->where('estado_id', 1)->latest('id')->first();
$user = User::where('email', 'prueba.comercial@local.test')->first();
$gestion = $presto->gestion;
$ocJ = OrdenCompra::where('tipo_oc', 1)->where('estado_id', 5)->latest('id')->first();
$ocN = OrdenCompra::where('tipo_oc', 2)->whereNotNull('presupuesto_id')->latest('id')->first() ?? OrdenCompra::where('tipo_oc', 2)->latest('id')->first();
$aj = Anticipo::find(4);
$args = function (ReflectionMethod $m) use ($presto, $user, $gestion, $ocJ, $ocN, $aj) {
    $n = $m->getName(); $out = [];
    foreach ($m->getParameters() as $p) {
        $v = $p->getName();
        $out[] = match (true) {
            in_array($v, ['presto', 'presupuesto']) => $presto,
            $v === 'user' => $user,
            $v === 'gestion' => $gestion,
            $v === 'anticipo' => $aj,
            $v === 'orden' => str_starts_with($n, 'ocNatural') ? $ocN : $ocJ,
            in_array($v, ['justificacion', 'observaciones']) => 'Texto de prueba',
            $v === 'cod_cc' => $presto->cod_cc,
            default => ($p->isDefaultValueAvailable() ? $p->getDefaultValue() : null),
        };
    }
    return $out;
};
$ok = 0; $mal = 0;
foreach ((new ReflectionClass($x))->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
    if ($m->getName() === 'sendMail') continue;
    $antes = NotificacionLog::count();
    try {
        $m->invokeArgs($x, $args($m));
        $nuevo = NotificacionLog::latest('id')->first();
        $n = NotificacionLog::count() - $antes;
        $dest = $n ? count((array) $nuevo->destinatarios) : 0;
        echo ($n ? "  ✓ " : "  · ").str_pad($m->getName(), 38)." registró $n correo(s), $dest destinatario(s)".($n && !$dest ? '  ← SIN DESTINATARIOS' : '')."\n";
        $ok++;
    } catch (\Throwable $e) {
        $mal++; echo "  ✗ ".str_pad($m->getName(), 38).' '.get_class($e).': '.substr($e->getMessage(), 0, 140).' @ '.basename($e->getFile()).':'.$e->getLine()."\n";
    }
}
DB::rollBack();
echo "\n$ok funciones sin error, $mal con error (transacción deshecha).\n";
