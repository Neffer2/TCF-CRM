<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercialController;  
use App\Http\Controllers\ContableController; 
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\HomeController;  
use App\Http\Controllers\AsistenteController;  
use App\Http\Controllers\LiderProduccionController;  
use App\Http\Controllers\ProductorController;  
 
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
    Route::get('/base-comercial-general', [AdminController::class, 'showBaseComercialGeneral'])->middleware(['auth'])->middleware(['admin'])->name('base-comercial-general');   
    Route::get('/presupuesto', [AdminController::class, 'showPresupuestos'])->middleware(['auth'])->middleware(['admin'])->name('presupuesto');   
    Route::get('/presupuesto-proyecto', [AdminController::class, 'showPresupuestosProyecto'])->middleware(['auth'])->middleware(['admin'])->name('presupuesto-proyecto');   
    Route::get('/ordenes-compra', [AdminController::class, 'showOrdenesCompra'])->middleware(['auth'])->middleware(['admin'])->name('ordenes-compra');   
    Route::get('/orden-juridica/{orden?}', [AdminController::class, 'showOrdenJuridica'])->middleware(['auth'])->middleware(['admin'])->name('orden-juridica');   
    Route::get('/actualizaciones', [AdminController::class, 'actualizaciones'])->middleware(['auth'])->name('actualizaciones');    
    Route::get('/estado-facturacion', [AdminController::class, 'estadoFacturacion'])->middleware(['auth'])->middleware(['admin'])->name('estado-facturacion');   

    Route::get('/estados/{params?}', [AdminController::class, 'estadoFacturacion'])->middleware(['auth'])->middleware(['admin'])->name('estados');   
/* --- */ 
  
/* commercial */   
    /* base - functions */    
        Route::get('/dashboard-com', [ComercialController::class, 'index'])->middleware(['auth'])->middleware(['comercial'])->name('dashboard-com');  
        Route::get('/dashboard-base', [ComercialController::class, 'base'])->middleware(['auth'])->middleware(['comercial'])->name('dashboard-base');  
        Route::get('/actualizar-perfil-com', [ComercialController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['comercial'])->name('actualizar-perfil-com');
        Route::get('/gestion-comercial', [ComercialController::class, 'gestionComercial'])->middleware(['auth'])->middleware(['comercial'])->name('gestion-comercial');   
        Route::get('/gestion-helisa', [ComercialController::class, 'gestionHelisa'])->middleware(['auth'])->middleware(['comercial'])->name('gestion-helisa');    
        Route::get('/contactos', [ComercialController::class, 'Contactos'])->middleware(['auth'])->middleware(['comercial'])->name('contactos');    
        // Route::get('/base-upload', [ComercialController::class, 'show_upload'])->middleware(['auth'])->name('base-upload');  

  
        Route::post('/base-upload', [ComercialController::class, 'upload_base'])->middleware(['auth'])->name('base-upload'); 
        Route::post('/base-export/{id_user?}', [ComercialController::class, 'export_base'])->middleware(['auth'])->name('base-export');         
        // Delete proyecto
        Route::post('/delete-proyecto/{id_user?}', [ComercialController::class, 'delete_proyecto'])->middleware(['auth'])->name('delete-proyecto'); 
        // Delete contacto
        Route::post('/delete-contacto/{id?}', [ComercialController::class, 'delete_contacto'])->middleware(['auth'])->name('delete-contacto'); 
        // Update proyecto
        Route::post('/update-proyecto/{id_user?}', [ComercialController::class, 'update_proyecto'])->middleware(['auth'])->name('update-proyecto');
        // Update proyecto Helisa  
        Route::post('/com-update-helisa/{id_user?}', [ComercialController::class, 'update_helisa'])->middleware(['auth'])->middleware(['comercial'])->name('com-update-helisa'); 
        // Update proyecto gestion comercial 
        Route::get('/update-gestion-comercial/{leadId?}', [ComercialController::class, 'update_gestion'])->middleware(['auth'])->name('update-gestion-comercial'); 
        // Update contacto
        Route::post('/update-contacto/{id?}', [ComercialController::class, 'update_contacto'])->middleware(['auth'])->name('update-contacto'); 
        // Delete registro Helisa  
        Route::post('/delete-registro/{centro?}/{num_doc}', [ComercialController::class, 'delete_registro'])->middleware(['auth'])->name('delete-registro'); 

        // Presupuesto
        Route::get('/presupuesto/{gestion?}', [ComercialController::class, 'presupuesto'])->middleware(['auth'])->name('presupuesto'); 
        Route::get('presupuestos', [ComercialController::class, 'presupuestos'])->middleware(['auth'])->name('presupuestos');   
    /* --- */
/* --- */   

/* contable */  
    /* base - functions */   
        // Route::get('/dashboard-con', [ContableController::class, 'index'])->middleware(['auth'])->middleware(['contable'])->name('dashboard-con');  
        // Route::get('/actualizar-perfil-con', [ContableController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['contable'])->name('actualizar-perfil-con');  

        // Route::post('/helisa-upload', [ContableController::class, 'helisa_upload'])->middleware(['auth'])->middleware(['contable'])->name('helisa-upload');  
        // Route::post('/helisa-export/{id_user?}', [ContableController::class, 'export_helisa'])->middleware(['auth'])->name('helisa-export'); 
        // Route::post('/vaciar_helisa', [ContableController::class, 'helisa_truncate'])->middleware(['auth'])->middleware(['contable'])->name('vaciar_helisa'); 
        // Route::post('/update-helisa/{id_user?}', [ContableController::class, 'update_helisa'])->middleware(['auth'])->middleware(['contable'])->name('update-helisa'); 
        // Delete proyecto
        // Route::post('/delete-registro/{id_user?}', [ContableController::class, 'delete_registro'])->middleware(['auth'])->name('delete-registro'); 
    /* --- */ 
/* --- */ 
 
    Route::get('cotizacion/{prespuesto?}/{nom_proyecto?}/{tipo}', [ComercialController::class, 'cotizacionPdf'])->middleware(['auth'])->name('cotizacion');     
    Route::get('cotizacionExcel/{prespuesto?}/{nom_proyecto?}/{tipo}', [ComercialController::class, 'cotizacionExcel'])->middleware(['auth'])->name('cotizacionExcel');
    // Route::get('/pdf', [ComercialController::class, 'pdf']);
    // Route::get('/pdf-vista', function(){
    //     return view('pdf.index');
    // });
 
/* Asistenet */    
    /* base - functions */    
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

/* --- */
  

require __DIR__.'/auth.php';   
 