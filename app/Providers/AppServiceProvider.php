<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
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

        $this->app->singleton('servicios', function () {
            return config('servicios');
        });

        $this->app->singleton('bancos', function () {
            return config('bancos');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        config([
            'ciudades' =>
            [
                'NACIONAL',
                'BOGOTA D.C',
                'MEDELLIN',
                'CALI',
                'BARRANQUILLA',
                'CARTAGENA',
                'SOACHA',
                'CUCUTA',
                'SOLEDAD',
                'BUCARAMANGA',
                'BELLO',
                'VALLEDUPAR',
                'VILLAVICENCIO',
                'SANTA MARTA',
                'IBAGUE',
                'MONTERIA',
                'PEREIRA',
                'MANIZALES',
                'PASTO',
                'NEIVA',
                'PALMIRA',
                'POPAYAN',
                'BUENAVENTURA',
                'ARMENIA',
                'FLORIDABLANCA',
                'SINCELEJO',
                'ITAGUI',
                'TUMACO',
                'ENVIGADO',
                'DOS QUEBRADAS',
                'TULUA',
                'BARRANCABERMEJA',
                'RIOHACHA',
                'BRICEÑO',
                'GUARNE',

                'URIBIA',
                'MAICAO',
                'PIEDECUESTA',
                'TUNJA',
                'YOPAL',
                'FLORENCIA',
                'GIRON',
                'FACATATIVA',
                'JAMUNDI',
                'FUSAGASUGA',
                'MOSQUERA',
                'CHIA',
                'ZIPAQUIRA',
                'RIONEGRO',
                'MALAMBO',
                'MAGANGUE',
                'MADRID',
                'CARTAGO',
                'TURBO',
                'QUIBDO',
                'APARTADO',
                'SOGAMOSO',
                'OCAÑA',
                'PITALITO',
                'BUGA',
                'DUITAMA',
                'CIENAGA',
                'AGUACHICA',
                'GIRARDOT',
                'LORICA',
                'TURBACO',
                'IPIALES',
                'FUNZA',
                'SANTANDER DE QUILICHAO',
                'VILLA DEL ROSARIO',
                'SAHAGUN',
                'YUMBO',
                'CERETE',
                'SABANALARGA',
                'CAJICA',
                'ARAUCA',
                'SAN ANDRES',
                'APARTADO'
            ],

            'departamentos' =>
            [
                'BOGOTA D.C',
                'AMAZONAS',
                'ANTIOQUIA',
                'ARAUCA',
                'ATLANTICO',
                'BOLIVAR',
                'BOYACA',
                'CALDAS',
                'CAQUETA',
                'CASANARE',
                'CAUCA',
                'CESAR',
                'CHOCO',
                'CORDOBA',
                'CUNDINAMARCA',
                'GUAINIA',
                'GUAVIARE',
                'HUILA',
                'LA GUAJIRA',
                'MAGDALENA',
                'META',
                'NARIÑO',
                'NORTE DE SANTANDER',
                'PUTUMAYO',
                'QUINDIO',
                'RISARALDA',
                'SAN ANDRES Y PROVIDENCIA',
                'SANTANDER',
                'SUCRE',
                'TOLIMA',
                'VALLE DEL CAUCA',
                'VAUPES',
                'VICHADA'
            ],

            'servicios' =>
            [
                'TRANSPORTE',
                'COORDINADOR',
                'LOGISTICO',
                'BRANDING',
                'PROMOTORIA',
                'ANIMADOR',
                'COMPRA',
                'ALQUILER',
            ],

            'bancos' =>
            [
                'BANCO AGRARIO DE COLOMBIA',
                'BANCO AV VILLAS',
                'BANCO CAJA SOCIAL',
                'BANCO DAVIVIENDA S.A.',
                'BANCO DE BOGOTÁ',
                'BANCO DE OCCIDENTE',
                'BANCO FALABELLA S.A.',
                'BANCOOMEVA',
                'BANCOLOMBIA',
                'BANCO PICHINCHA',
                'BANCO POPULAR',
                'BBVA COLOMBIA',
                'DAVIPLATA',
                'FINANCIERA JURISCOOP',
                'ITAU',
                'LINK',
                'LULO BANK',
                'NEQUI',
                'NU BANK',
                'SCOTIABANCK COLPATRIA'
            ]
        ]);
    }
}
