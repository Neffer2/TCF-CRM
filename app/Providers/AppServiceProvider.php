<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** 
     * Register any application services. 
     *
     * @return void
     */
    public function register()
    {
        // registra la variable global
        $this->app->singleton('ciudades', function () { 
            return config('ciudades');
        }); 
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config(['ciudades' =>
            [
                "NACIONAL",
                "BOGOTA D.C",
                "MEDELLIN",
                "CALI",
                "BARRANQUILLA",
                "CARTAGENA",
                "SOLEDAD",
                "CUCUTA",
                "IBAGUE",
                "ARMENIA",
                "SOACHA",
                "VILLAVICENCIO",
                "BUCARAMANGA",
                "SANTA MARTA",
                "VALLEDUPAR",
                "BELLO",
                "PEREIRA",
                "PASTO",
                "BUENAVENTURA",
                "MANIZALES",
                "NEIVA",
                "PALMIRA",
                "RIOHACHA",
                "SINCELEJO",
                "POPAYAN",
                "ITAGÜI",
                "FLORIDABLANCA",
                "ENVIGADO",
                "TULUA",
                "SAN ANDRES",
                "DOSQUEBRADAS",
                "APARTADO",
                "TUNJA",
                "GIRON",
                "URIBIA",
                "BARRANQUILLA",
                "BARRANCABERMEJA",
                "FLORENCIA",
                "TURBO",
                "MAICAO",
                "PIEDECUESTA",
                "YOPAL",
            ]
        ]);
    }
}
