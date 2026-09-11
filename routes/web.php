<?php

use App\Http\Middleware\productor;
use App\Models\Anticipo;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercialController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AsistenteController;
use App\Http\Controllers\LiderProduccionController;
use App\Http\Controllers\ProductorController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\TesoreriaController;
use App\Traits\SMS;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/* Home */
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');
/* --- */


/* Admin */
    Route::get('/dashboard-admin', [AdminController::class, 'index'])->middleware(['auth'])->middleware(['admin'])->name('dashboard-admin');
    Route::get('/mi-equpo', [AdminController::class, 'show_team'])->middleware(['auth'])->middleware(['admin'])->name('mi-equpo');
    Route::get('/actualizar-perfil-adm', [AdminController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['admin'])->name('actualizar-perfil-adm');
    Route::get('/base-comercial-general/{filtro?}', [AdminController::class, 'showBaseComercialGeneral'])->middleware(['auth'])->middleware(['admin'])->name('base-comercial-general');
    Route::get('/helisa-general', [AdminController::class, 'showHelisaGeneral'])->middleware(['auth'])->middleware(['admin'])->name('helisa-general');
    Route::get('/export-helisa/{comercial?}/{centro?}', [AdminController::class, 'exportHelisa'])->middleware(['auth'])->middleware(['admin'])->name('export-helisa');
    Route::get('/presupuesto', [AdminController::class, 'showPresupuestos'])->middleware(['auth'])->middleware(['admin'])->name('presupuestos-admin');
    Route::get('/presupuesto-proyecto', [AdminController::class, 'showPresupuestosProyecto'])->middleware(['auth'])->middleware(['admin'])->name('presupuesto-proyecto');
    Route::get('/ordenes-compra', [AdminController::class, 'showOrdenesCompra'])->middleware(['auth'])->middleware(['admin'])->name('ordenes-compra');
    Route::get('/ordenes-compra/pdf/{orden}', [AdminController::class, 'ordenCompraPdf'])->middleware(['auth'])->middleware(['admin'])->name('orden-compra.pdf');
    Route::get('/actualizaciones', [AdminController::class, 'actualizaciones'])->middleware(['auth', 'rol:1'])->name('actualizaciones');
    Route::get('/validaciones', [AdminController::class, 'validaciones'])->middleware(['auth', 'rol:1'])->name('validaciones');
    Route::get('/estado-facturacion', [AdminController::class, 'estadoFacturacion'])->middleware(['auth'])->middleware(['admin'])->name('estado-facturacion');

    Route::get('/orden-juridica/{orden?}', [AdminController::class, 'showOrdenJuridica'])->middleware(['auth'])->middleware(['admin'])->name('orden-juridica');
    Route::get('/orden-natural/{orden_id?}', function ($orden_id){
        return view('admin.produccion.ordenes.natural', ['orden_id' => $orden_id]);
    })->middleware(['auth'])->middleware(['admin'])->name('orden-natural');
    Route::get('/orden-nomina/{orden?}', [AdminController::class, 'showOrdenNomina'])->middleware(['auth'])->middleware(['admin'])->name('orden-nomina');
    Route::get('/orden-compra_anticipó', [AdminController::class, 'showOrdenCompra_Anticipo'])->middleware(['auth'])->middleware(['admin'])->name('orden-compra_anticipate');

    Route::get('/cuenta-cobro/pdf/{orden}', [AdminController::class, 'cuentaCobroPdf'])->middleware(['auth'])->middleware(['admin'])->name('cuenta-cobro.pdf');

    Route::get('/validacionesCliente', [AdminController::class, 'ValidacionesClientes'])->middleware(['auth', 'rol:1'])->name('validacionesCliente');

    Route::get('/consumidos', [AdminController::class, 'showConsumidos'])->middleware(['auth'])->middleware(['admin'])->name('consumidos');
    Route::get('/consumido/{presupuesto_id?}', [AdminController::class, 'showConsumido'])->middleware(['auth', 'rol:1,2,5,7'])->name('consumido');
    Route::get('/estados/{params?}', [AdminController::class, 'estadoFacturacion'])->middleware(['auth'])->middleware(['admin'])->name('estados');
    Route::get('/proveedores', [HomeController::class, 'showProveedores'])->middleware(['auth', 'rol:1,2,7'])->name('proveedores');

    Route::view('/personal', 'productor.terceros.personal')->middleware(['auth'])->middleware(['admin'])->name('personal');
    Route::get('/reporte-consumidos', [AdminController::class, 'reporteConsumidos'])->middleware(['auth'])->middleware(['admin'])->name('reporte-consumidos');
    Route::get('/reporte-plano-helisa/{mes?}', [AdminController::class, 'reportePlanoHelisa'])->middleware(['auth', 'rol:1,9,10'])->name('reporte-plano-helisa');
/* --- */

/* commercial */
    Route::get('/dashboard-com', [ComercialController::class, 'index'])->middleware(['auth'])->middleware(['comercial'])->name('dashboard-com');
    Route::get('/dashboard-base', [ComercialController::class, 'base'])->middleware(['auth'])->middleware(['comercial'])->name('dashboard-base');
    Route::get('/actualizar-perfil-com', [ComercialController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['comercial'])->name('actualizar-perfil-com');
    Route::get('/gestion-comercial', [ComercialController::class, 'gestionComercial'])->middleware(['auth'])->middleware(['comercial'])->name('gestion-comercial');
    Route::get('/gestion-helisa', [ComercialController::class, 'gestionHelisa'])->middleware(['auth'])->middleware(['comercial'])->name('gestion-helisa');
    Route::get('/contactos', [ComercialController::class, 'Contactos'])->middleware(['auth'])->middleware(['comercial'])->name('contactos');

    Route::get('/clientes', [ComercialController::class, 'gestionClientes'])->middleware(['auth'])->middleware(['comercial'])->name('clientes');

    Route::get('/consumidos-com', [ComercialController::class, 'showConsumidos'])->middleware(['auth'])->middleware(['comercial'])->name('consumidos-com');
    Route::post('/base-upload', [ComercialController::class, 'upload_base'])->middleware(['auth', 'rol:1,2,5'])->name('base-upload');
    Route::post('/base-export/{id_user?}', [ComercialController::class, 'export_base'])->middleware(['auth', 'rol:1,2,5'])->name('base-export');

    Route::post('/delete-proyecto/{id_user?}', [ComercialController::class, 'delete_proyecto'])->middleware(['auth', 'rol:1,2,5'])->name('delete-proyecto');
    Route::post('/delete-contacto/{id?}', [ComercialController::class, 'delete_contacto'])->middleware(['auth', 'rol:1,2,5'])->name('delete-contacto');
    Route::post('/update-proyecto/{id_user?}', [ComercialController::class, 'update_proyecto'])->middleware(['auth', 'rol:1,2,5'])->name('update-proyecto');
    Route::post('/com-update-helisa/{id_user?}', [ComercialController::class, 'update_helisa'])->middleware(['auth'])->middleware(['comercial'])->name('com-update-helisa');
    Route::get('/update-gestion-comercial/{leadId?}', [ComercialController::class, 'update_gestion'])->middleware(['auth', 'rol:1,2,5'])->name('update-gestion-comercial');
    Route::post('/update-contacto/{id?}', [ComercialController::class, 'update_contacto'])->middleware(['auth', 'rol:1,2,5'])->name('update-contacto');

    // DEPRECATED
    Route::post('/delete-registro/{centro?}/{num_doc}', [ComercialController::class, 'delete_registro'])->middleware(['auth', 'rol:1,2,5'])->name('delete-registro');

    // Presupuesto
    Route::get('/presupuesto/{gestion?}', [ComercialController::class, 'presupuesto'])->middleware(['auth', 'rol:1,2,5'])->name('presupuesto');
    Route::get('presupuestos', [ComercialController::class, 'presupuestos'])->middleware(['auth', 'rol:1,2,5'])->name('presupuestos');
    /* --- */
    Route::get('cotizacion/{prespuesto?}/{nom_proyecto?}/{tipo}', [ComercialController::class, 'cotizacionPdf'])->middleware(['auth'])->name('cotizacion');
    Route::get('cotizacionExcel/{prespuesto?}/{nom_proyecto?}/{tipo}', [ComercialController::class, 'cotizacionExcel'])->middleware(['auth'])->name('cotizacionExcel');
/* Asistenet */
    Route::get('/dashboard-asis', [AsistenteController::class, 'index'])->middleware(['auth'])->middleware(['asistente'])->name('dashboard-asis');
    Route::get('/asis-dashboard-base', [AsistenteController::class, 'base'])->middleware(['auth'])->middleware(['asistente'])->name('asis-dashboard-base');
    Route::get('/asis-gestion-helisa', [AsistenteController::class, 'gestionHelisa'])->middleware(['auth'])->middleware(['asistente'])->name('asis-gestion-helisa');
    Route::get('/asis-gestion-comercial', [AsistenteController::class, 'gestionComercial'])->middleware(['auth'])->middleware(['asistente'])->name('asis-gestion-comercial');
    Route::get('/asis-contactos', [AsistenteController::class, 'Contactos'])->middleware(['auth'])->middleware(['asistente'])->name('asis-contactos');
    Route::get('/actualizar-perfil-asis', [AsistenteController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['asistente'])->name('actualizar-perfil-asis');
/* --- */

/* Líder produccion */
    Route::get('/dashboard-lider-produccion', [LiderProduccionController::class, 'index'])->middleware(['auth'])->middleware(['lproduccion'])->name('dashboard-lider-produccion');
/* --- */

/* Productor */
    Route::get('/dashboard-productor', [ProductorController::class, 'index'])->middleware(['auth'])->middleware(['productor'])->name('dashboard-productor');
    Route::get('/firmar-remision/{orden?}', [ProductorController::class, 'showRemision'])->middleware(['auth'])->middleware(['productor'])->name('firmar-remision');;
    Route::get('/consumidos-prod', [ProductorController::class, 'showConsumidos'])->middleware(['auth'])->middleware(['productor'])->name('consumidos-prod');
    Route::view('/ordenes-compra-prod', 'productor.ordenes.index')->middleware(['auth'])->middleware(['productor'])->name('ordenes-prod');
    Route::get('/orden-compra-natural/{orden_id?}', function ($orden_id = null){
        return view('productor.terceros.orden-compra-natural', ['orden_id' => $orden_id]);
    })->middleware(['auth'])->middleware(['productor'])->name('orden-natural-prod');

/* --- */

/* Contabilidad */
    Route::get('/dashboard-contabilidad', [ContabilidadController::class, 'index'])->middleware(['auth'])->middleware(['contabilidad'])->name('dashboard-contabilidad');
    Route::get('/anticipos-contabilidad', [ContabilidadController::class, 'showAnticipos'])->middleware(['auth'])->middleware(['contabilidad'])->name('anticipos-contabilidad');
    Route::get('/anticipo-contabilidad/{orden?}', [ContabilidadController::class, 'showAnticipo'])->middleware(['auth'])->middleware(['contabilidad'])->name('anticipo-contabilidad');
    Route::view('/lista-anticipos-contabilidad', 'contabilidad.anticipos_.index')->middleware(['auth'])->middleware(['contabilidad'])->name('lista-anticipos-contabilidad');

    Route::get('/detalle-anticipo-contabilidad/{anticipo_id?}', function ($anticipo_id){
        return view('contabilidad.anticipos_.anticipo', ['anticipo_id' => $anticipo_id]);
    })->middleware(['auth'])->middleware(['contabilidad'])->name('detalle-anticipo-contabilidad');
/* --- */

/* Tesoreria */
    Route::get('/dashboard-tesoreria', [TesoreriaController::class, 'index'])->middleware(['auth'])->middleware(['tesoreria'])->name('dashboard-tesoreria');
    Route::get('/anticipos', [TesoreriaController::class, 'showAnticipos'])->middleware(['auth'])->middleware(['tesoreria'])->name('anticipos');
    Route::get('/anticipo/{orden?}', [TesoreriaController::class, 'showAnticipo'])->middleware(['auth'])->middleware(['tesoreria'])->name('anticipo');
/* --- */

/* Anticipos (flujo nuevo): rutas que el código invocaba pero nunca fueron definidas */
    // Productor
    Route::view('/lista-anticipos-prod', 'productor.anticipos.index')->middleware(['auth', 'productor'])->name('lista-anticipos-prod');
    Route::get('/anticipo-prod/{anticipo_id}', function ($anticipo_id){
        $tipo = optional(Anticipo::find($anticipo_id))->oc_id ? 1 : 2;
        return view('productor.anticipos.anticipo', ['anticipo_id' => $anticipo_id, 'tipo' => $tipo]);
    })->middleware(['auth', 'productor'])->name('anticipo-prod');
    Route::view('/solicitud-anticipo-prod', 'productor.ordenes.anticipo')->middleware(['auth', 'productor'])->name('solicitd-anticipo-prod');
    Route::get('/ordenes-nomina-prod/{orden_id?}', function ($orden_id = null){
        return view('productor.ordenes.nomina', ['orden_id' => $orden_id]);
    })->middleware(['auth', 'productor'])->name('ordenes-nomina-prod');

    // Líder de producción
    Route::view('/lista-anticipos-lid', 'lider-produccion.anticipos.index')->middleware(['auth', 'lproduccion'])->name('lista-anticipos-lid');
    Route::get('/anticipo-lid/{anticipo_id}', function ($anticipo_id){
        $tipo = optional(Anticipo::find($anticipo_id))->oc_id ? 1 : 2;
        return view('lider-produccion.anticipos.anticipo', ['anticipo_id' => $anticipo_id, 'tipo' => $tipo]);
    })->middleware(['auth', 'lproduccion'])->name('anticipo-lid');
    Route::view('/ordenes-compra-lid', 'lider-produccion.ordenes.index')->middleware(['auth', 'lproduccion'])->name('ordenes-compra-lid');

    // Admin
    Route::view('/lista-anticipos-admin', 'admin.produccion.anticipos.index')->middleware(['auth', 'admin'])->name('lista-anticipos-admin');
    Route::view('/anticipos-admin', 'admin.produccion.anticipos.index')->middleware(['auth', 'admin'])->name('anticipos-admin');
    Route::get('/anticipo-admin/{anticipo_id}', function ($anticipo_id){
        $tipo = optional(Anticipo::find($anticipo_id))->oc_id ? 1 : 2;
        return view('admin.produccion.anticipos.anticipo', ['anticipo_id' => $anticipo_id, 'tipo' => $tipo]);
    })->middleware(['auth', 'admin'])->name('anticipo-admin');

    // Tesorería (con esto la cola de pago del flujo nuevo por fin es alcanzable)
    Route::view('/lista-anticipos-tesoreria', 'tesoreria.anticipos_.index')->middleware(['auth', 'tesoreria'])->name('lista-anticipos-tesoreria');
    Route::get('/detalle-anticipo-tesoreria/{anticipo_id}', function ($anticipo_id){
        return view('tesoreria.anticipos_.anticipo', ['anticipo_id' => $anticipo_id]);
    })->middleware(['auth', 'tesoreria'])->name('detalle-anticipo-tesoreria');
/* --- */

/* PÚBLICO */
    // El enlace del tercero viaja FIRMADO (URL::signedRoute desde el SMS):
    // el id de orden era secuencial y cualquiera podia iterar ?orden=1,2,3...
    // y ver datos personales o subir evidencias ajenas.
    Route::view('/consulta-terceros/{orden?}', 'productor.terceros.consulta-terceros')
        ->middleware('signed')->name('consulta-terceros');
/* --- */


Route::get('trial-mail', function (){
    return view('mails.grGenerado');
});

Route::get('trial', function (){
    return view('exports.orden_compra_pdf');
});
require __DIR__.'/auth.php';