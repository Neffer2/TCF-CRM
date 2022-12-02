<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercialController; 
 
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
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin.index');
});


/* commercial */ 
    /* base - functions */ 
        Route::get('/dashboard', [ComercialController::class, 'index'])->middleware(['auth'])->name('dashboard');  
        Route::get('/base-upload', [ComercialController::class, 'show_upload'])->middleware(['auth'])->name('base-upload');  

        Route::post('/base-upload', [ComercialController::class, 'upload_base'])->middleware(['auth'])->name('base-upload');  
    /* --- */
/* --- */ 

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
 