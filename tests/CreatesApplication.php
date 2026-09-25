<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Freno de seguridad: RefreshDatabase borra todas las tablas de la base configurada.
        // Si phpunit.xml no aplicó la base en memoria (por ejemplo, con la configuración en
        // caché), las pruebas se detienen antes de tocar la base local o la de producción.
        $conexion = config('database.default');
        $base = (string) config("database.connections.{$conexion}.database");
        if (config('crm.notificaciones.correo_modo') !== 'log' || config('crm.sms.modo') !== 'log' || config('crm.notificaciones.alertas.siempre')) {
            throw new \RuntimeException('Pruebas detenidas: el correo o los SMS no están en modo simulado (MAIL_MAILER=log, SMS_MODO=log, CRM_ALERTA_SIEMPRE=false en phpunit.xml).');
        }
        if (!($conexion === 'sqlite' && $base === ':memory:') && !str_ends_with($base, '_testing')) {
            throw new \RuntimeException("Pruebas detenidas: apuntan a la base '{$base}' ({$conexion}). Usa SQLite en memoria (phpunit.xml) o una base terminada en _testing. Si cacheaste la configuración, corre php artisan config:clear.");
        }

        return $app;
    }
}
