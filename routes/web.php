<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercialController;  
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\HomeController;  
use App\Http\Controllers\AsistenteController;  
use App\Http\Controllers\LiderProduccionController;  
use App\Http\Controllers\ProductorController;  
use App\Http\Controllers\ContabilidadController; 
use App\Http\Controllers\TesoreriaController; 
 
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
    Route::get('/presupuesto', [AdminController::class, 'showPresupuestos'])->middleware(['auth'])->middleware(['admin'])->name('presupuesto');   
    Route::get('/presupuesto-proyecto', [AdminController::class, 'showPresupuestosProyecto'])->middleware(['auth'])->middleware(['admin'])->name('presupuesto-proyecto');   
    Route::get('/ordenes-compra', [AdminController::class, 'showOrdenesCompra'])->middleware(['auth'])->middleware(['admin'])->name('ordenes-compra');    
    Route::get('/actualizaciones', [AdminController::class, 'actualizaciones'])->middleware(['auth'])->name('actualizaciones');    
    Route::get('/estado-facturacion', [AdminController::class, 'estadoFacturacion'])->middleware(['auth'])->middleware(['admin'])->name('estado-facturacion');    

    Route::get('/orden-juridica/{orden?}', [AdminController::class, 'showOrdenJuridica'])->middleware(['auth'])->middleware(['admin'])->name('orden-juridica');
    Route::get('/consumidos', [AdminController::class, 'showConsumidos'])->middleware(['auth'])->middleware(['admin'])->name('consumidos');    
    Route::get('/consumido/{presupuesto_id?}', [AdminController::class, 'showConsumido'])->middleware(['auth'])->name('consumido');     
    Route::get('/estados/{params?}', [AdminController::class, 'estadoFacturacion'])->middleware(['auth'])->middleware(['admin'])->name('estados');   
    Route::get('/proveedores', [HomeController::class, 'showProveedores'])->middleware(['auth'])->name('proveedores');   
/* --- */  
   
/* commercial */   
    Route::get('/dashboard-com', [ComercialController::class, 'index'])->middleware(['auth'])->middleware(['comercial'])->name('dashboard-com');  
    Route::get('/dashboard-base', [ComercialController::class, 'base'])->middleware(['auth'])->middleware(['comercial'])->name('dashboard-base');  
    Route::get('/actualizar-perfil-com', [ComercialController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['comercial'])->name('actualizar-perfil-com'); 
    Route::get('/gestion-comercial', [ComercialController::class, 'gestionComercial'])->middleware(['auth'])->middleware(['comercial'])->name('gestion-comercial');   
    Route::get('/gestion-helisa', [ComercialController::class, 'gestionHelisa'])->middleware(['auth'])->middleware(['comercial'])->name('gestion-helisa');    
    Route::get('/contactos', [ComercialController::class, 'Contactos'])->middleware(['auth'])->middleware(['comercial'])->name('contactos');    
    Route::get('/consumidos-com', [ComercialController::class, 'showConsumidos'])->middleware(['auth'])->middleware(['comercial'])->name('consumidos-com');     
    Route::post('/base-upload', [ComercialController::class, 'upload_base'])->middleware(['auth'])->name('base-upload'); 
    Route::post('/base-export/{id_user?}', [ComercialController::class, 'export_base'])->middleware(['auth'])->name('base-export');         

    Route::post('/delete-proyecto/{id_user?}', [ComercialController::class, 'delete_proyecto'])->middleware(['auth'])->name('delete-proyecto'); 
    Route::post('/delete-contacto/{id?}', [ComercialController::class, 'delete_contacto'])->middleware(['auth'])->name('delete-contacto'); 
    Route::post('/update-proyecto/{id_user?}', [ComercialController::class, 'update_proyecto'])->middleware(['auth'])->name('update-proyecto');
    Route::post('/com-update-helisa/{id_user?}', [ComercialController::class, 'update_helisa'])->middleware(['auth'])->middleware(['comercial'])->name('com-update-helisa'); 
    Route::get('/update-gestion-comercial/{leadId?}', [ComercialController::class, 'update_gestion'])->middleware(['auth'])->name('update-gestion-comercial'); 
    Route::post('/update-contacto/{id?}', [ComercialController::class, 'update_contacto'])->middleware(['auth'])->name('update-contacto'); 

    // DEPRECATED 
    Route::post('/delete-registro/{centro?}/{num_doc}', [ComercialController::class, 'delete_registro'])->middleware(['auth'])->name('delete-registro'); 

    // Presupuesto
    Route::get('/presupuesto/{gestion?}', [ComercialController::class, 'presupuesto'])->middleware(['auth'])->name('presupuesto'); 
    Route::get('presupuestos', [ComercialController::class, 'presupuestos'])->middleware(['auth'])->name('presupuestos');   
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
    Route::get('/firmar-remision/{orden?}', [ProductorController::class, 'showRemision'])->middleware(['auth'])->middleware(['productor'])->name('firmar-remision');
    Route::get('/firmar-remision/{orden?}', [ProductorController::class, 'showRemision'])->middleware(['auth'])->middleware(['productor'])->name('firmar-remision');
    Route::get('/consumidos-prod', [ProductorController::class, 'showConsumidos'])->middleware(['auth'])->middleware(['productor'])->name('consumidos-prod'); 
    Route::get('/orden-compra-natural', [ProductorController::class, 'showOrdenNatural'])->middleware(['auth'])->middleware(['productor'])->name('orden-natural-prod');

    Route::view('/orden-compra-natural', 'productor.terceros.orden-compra-natural')->middleware(['auth'])->middleware(['productor'])->name('orden-natural-prod');
    Route::view('/personal', 'productor.terceros.personal')->middleware(['auth'])->middleware(['productor'])->name('personal');
/* --- */

/* Contabilidad */   
    Route::get('/dashboard-contabilidad', [ContabilidadController::class, 'index'])->middleware(['auth'])->middleware(['contabilidad'])->name('dashboard-contabilidad');
    Route::get('/anticipos-contabilidad', [ContabilidadController::class, 'showAnticipos'])->middleware(['auth'])->middleware(['contabilidad'])->name('anticipos-contabilidad');
    Route::get('/anticipo-contabilidad/{orden?}', [ContabilidadController::class, 'showAnticipo'])->middleware(['auth'])->middleware(['contabilidad'])->name('anticipo-contabilidad');
/* --- */

/* Tesoreria */
    Route::get('/dashboard-tesoreria', [TesoreriaController::class, 'index'])->middleware(['auth'])->middleware(['tesoreria'])->name('dashboard-tesoreria');
    Route::get('/anticipos', [TesoreriaController::class, 'showAnticipos'])->middleware(['auth'])->middleware(['tesoreria'])->name('anticipos');
    Route::get('/anticipo/{orden?}', [TesoreriaController::class, 'showAnticipo'])->middleware(['auth'])->middleware(['tesoreria'])->name('anticipo');
/* --- */

/* PÚBLICO */
    Route::get('/metricas', function () { 
        return view('publico.metricas');
    })->name('metricas');

    Route::view('/consulta-terceros', 'productor.terceros.consulta-terceros')->name('consulta-terceros');
/* --- */

 
// Route::get('trial-mail', function (){ 
//     return view('mails.grGenerado');
// });
require __DIR__.'/auth.php';   
