<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

/*
 * Autenticación: el CRM solo usa inicio y cierre de sesión. Las cuentas las
 * crea un administrador desde "Mi equipo" y las claves las restablece un
 * desarrollador; por eso NO existen registro público, recuperación de clave
 * por correo ni verificación de correo (Breeze las traía y quedaban abiertas:
 * /register permitía a cualquiera crear una cuenta y /forgot-password daba 500).
 */
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
