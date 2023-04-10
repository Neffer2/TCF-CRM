<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercialController; 
use App\Http\Controllers\ContableController; 
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\HomeController;  
use App\Http\Controllers\AsistenteController;  
 
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
    /* --- */
/* --- */  

/* contable */ 
    /* base - functions */  
        Route::get('/dashboard-con', [ContableController::class, 'index'])->middleware(['auth'])->middleware(['contable'])->name('dashboard-con');  
        Route::get('/actualizar-perfil-con', [ContableController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['contable'])->name('actualizar-perfil-con');  

        Route::post('/helisa-upload', [ContableController::class, 'helisa_upload'])->middleware(['auth'])->middleware(['contable'])->name('helisa-upload');  
        Route::post('/helisa-export/{id_user?}', [ContableController::class, 'export_helisa'])->middleware(['auth'])->name('helisa-export'); 
        Route::post('/vaciar_helisa', [ContableController::class, 'helisa_truncate'])->middleware(['auth'])->middleware(['contable'])->name('vaciar_helisa'); 
        Route::post('/update-helisa/{id_user?}', [ContableController::class, 'update_helisa'])->middleware(['auth'])->middleware(['contable'])->name('update-helisa'); 

        // Delete proyecto
        Route::post('/delete-registro/{id_user?}', [ContableController::class, 'delete_registro'])->middleware(['auth'])->name('delete-registro'); 
    /* --- */
/* --- */
 
/* Asistenet */ 
    /* base - functions */  
    Route::get('/dashboard-asis', [AsistenteController::class, 'index'])->middleware(['auth'])->middleware(['asistente'])->name('dashboard-asis');  
/* --- */
/* --- */  

require __DIR__.'/auth.php';   
 