<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Permisos con nombre (tabla permisos): en Blade @can('clave') y en
        // código Gate::allows('clave') / $user->can('clave'). Reemplazan los
        // IDs de usuario quemados en vistas y traits.
        foreach ([
            'aprobar-presupuestos',
            'aprobador-margen',
            'gerente-comercial',
            'validar-nomina',
            'revisar-anticipos-gerencia',
            'gestionar-presupuestos-especiales',
            'ver-menu-admin-avanzado',
        ] as $clave) {
            Gate::define($clave, function ($user) use ($clave) {
                try {
                    return \App\Models\Permiso::tiene($user, $clave);
                } catch (\Throwable $e) {
                    // Antes de correr la migración de permisos, ningún gate
                    // concede acceso (fallo cerrado, sin tumbar la app).
                    return false;
                }
            });
        }
    }
}
