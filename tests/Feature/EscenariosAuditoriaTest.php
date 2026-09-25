<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Corre los escenarios de tests/escenarios-auditoria (los casos por rol de la auditoría) contra una copia de la
 * base de pruebas llamada *_testing (ver crear-bd-testing.sh). Cada escenario trabaja en una transacción que se
 * deshace. Sin esa base se marcan como omitidos: nunca tocan la base del .env.
 */
class EscenariosAuditoriaTest extends TestCase
{
    /** @dataProvider escenarios */
    public function test_escenario(string $archivo): void
    {
        $base = env('DB_TESTING_DATABASE', 'tfc_crm_testing');
        $this->assertTrue(str_ends_with($base, '_testing'), 'La base de escenarios debe terminar en _testing');
        config(['database.default' => 'mysql', 'database.connections.mysql.database' => $base, 'mail.default' => 'log',
            'crm.notificaciones.correo_modo' => 'log', 'crm.sms.modo' => 'log', 'crm.notificaciones.alertas.siempre' => false,
            // Con APP_ENV=testing Livewire deja las cargas de archivos en un disco propio ("tmp-for-tests", falso y vacío)
            // y ->store() también escribe ahí. Se apunta al disco del CRM para que los archivos queden donde quedan en
            // producción y los escenarios los encuentren con Storage::exists().
            'filesystems.disks.tmp-for-tests' => config('filesystems.disks.'.config('filesystems.default'))]);
        $this->assertSame('log', config('crm.notificaciones.correo_modo'), 'El correo debe estar simulado');
        DB::purge('mysql');
        try {
            DB::connection('mysql')->table('users')->count();
        } catch (\Throwable $e) {
            $this->markTestSkipped("No hay base {$base} (crear-bd-testing.sh): ".$e->getMessage());
        }

        ob_start();
        try {
            include __DIR__.'/../escenarios-auditoria/'.$archivo;
        } finally {
            $salida = ob_get_clean();
        }
        // "Resultado: N correctas, M fallas" o, en correos.php, "N funciones sin error, M con error"
        $patron = '/(?:Resultado: (\d+) correctas, (\d+) fallas|(\d+) funciones sin error, (\d+) con error)/';
        $this->assertMatchesRegularExpression($patron, $salida, "El escenario no terminó:\n".$salida);
        preg_match($patron, $salida, $m);
        $m = [null, $m[1] !== '' ? $m[1] : ($m[3] ?? '0'), $m[2] !== '' ? $m[2] : ($m[4] ?? '0')];
        $this->assertSame(0, (int) $m[2], "Fallas en {$archivo}:\n".$salida);
        $this->assertGreaterThan(0, (int) $m[1]);
    }

    public function escenarios(): array
    {
        $lista = [];
        // (el proveedor de datos corre antes de arrancar la aplicación: sin base_path())
        foreach (glob(__DIR__.'/../escenarios-auditoria/*.php') as $f) {
            $lista[basename($f)] = [basename($f)];
        }
        return $lista;
    }
}
