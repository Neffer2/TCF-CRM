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

        $this->app->singleton('departamentos', function () { 
            return config('departamentos');
        }); 
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config([
            'ciudades' =>
            [
                "NACIONAL",
                "BOGOTA D.C",
                "MEDELLIN",
                "CALI",
                "BARRANQUILLA",
                "CARTAGENA",
                "SOACHA",
                "CUCUTA",
                "SOLEDAD",
                "BUCARAMANGA",
                "BELLO",
                "VALLEDUPAR",
                "VILLAVICENCIO",
                "SANTA MARTA",
                "IBAGUE",
                "MONTERIA",
                "PEREIRA",
                "MANIZALES",
                "PASTO",
                "NEIVA",
                "PALMIRA",
                "POPAYAN",
                "BUENAVENTURA",
                "ARMENIA",
                "FLORIDABLANCA",
                "SINCELEJO",
                "ITAGUI",
                "TUMACO",
                "ENVIGADO",
                "DOS QUEBRADAS",
                "TULUA",
                "BARRANCABERMEJA",
                "RIOHACHA",
                "BRICEÑO",
                "GUARNE",

                "URIBIA",
                "MAICAO",
                "PIEDECUESTA",
                "TUNJA",
                "YOPAL",
                "FLORENCIA",
                "GIRON",
                "FACATATIVA",
                "JAMUNDI",
                "FUSAGASUGA",
                "MOSQUERA",
                "CHIA",
                "ZIPAQUIRA",
                "RIONEGRO",
                "MALAMBO",
                "MAGANGUE",
                "MADRID",
                "CARTAGO",
                "TURBO",
                "QUIBDO",
                "APARTADO",
                "SOGAMOSO",
                "OCAÑA",
                "PITALITO",
                "BUGA",
                "DUITAMA",
                "CIENAGA",
                "AGUACHICA",
                "GIRARDOT",
                "LORICA",
                "TURBACO",
                "IPIALES",
                "FUNZA",
                "SANTANDER DE QUILICHAO",
                "VILLA DEL ROSARIO",
                "SAHAGUN",
                "YUMBO",
                "CERETE",
                "SABANALARGA",
                "CAJICA",
                "ARAUCA",
                "SAN ANDRES",
                "APARTADO"
            ],

            'departamentos' =>
            [
                "BOGOTA D.C",
                "AMAZONAS",
                "ANTIOQUIA",
                "ARAUCA",
                "ATLANTICO",
                "BOLIVAR",
                "BOYACA",
                "CALDAS",
                "CAQUETA",
                "CASANARE",
                "CAUCA",
                "CESAR",
                "CHOCO",
                "CORDOBA",
                "CUNDINAMARCA",
                "GUAINIA",
                "GUAVIARE",
                "HUILA",
                "LA GUAJIRA",
                "MAGDALENA",
                "META",
                "NARIÑO",
                "NORTE DE SANTANDER",
                "PUTUMAYO",
                "QUINDIO",
                "RISARALDA",
                "SAN ANDRES Y PROVIDENCIA",
                "SANTANDER",
                "SUCRE",
                "TOLIMA",
                "VALLE DEL CAUCA",
                "VAUPES",
                "VICHADA"
            ]
        ]);
    }
}
