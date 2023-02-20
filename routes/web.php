<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercialController; 
use App\Http\Controllers\ContableController; 
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\HomeController;  
 
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
        Route::get('/actualizar-perfil-com', [ComercialController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['comercial'])->name('actualizar-perfil-com');  
        // Route::get('/base-upload', [ComercialController::class, 'show_upload'])->middleware(['auth'])->name('base-upload');  

        Route::post('/base-upload', [ComercialController::class, 'upload_base'])->middleware(['auth'])->name('base-upload'); 
    /* --- */
/* --- */  

/* contable */ 
    /* base - functions */  
        Route::get('/dashboard-con', [ContableController::class, 'index'])->middleware(['auth'])->middleware(['contable'])->name('dashboard-con');  
        Route::get('/actualizar-perfil-con', [ContableController::class, 'showActualizarPerfil'])->middleware(['auth'])->middleware(['contable'])->name('actualizar-perfil-con');  

        Route::post('/helisa-upload', [ContableController::class, 'helisa_upload'])->middleware(['auth'])->middleware(['contable'])->name('helisa-upload');  
        Route::post('/vaciar_helisa', [ContableController::class, 'helisa_truncate'])->middleware(['auth'])->middleware(['contable'])->name('vaciar_helisa'); 
    /* --- */
/* --- */

require __DIR__.'/auth.php';   
 