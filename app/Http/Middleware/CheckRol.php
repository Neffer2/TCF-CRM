<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware de rol con parámetros: ->middleware('rol:1,2,5') permite
 * el acceso solo a los roles listados. Complementa a los middlewares de
 * rol únicos (admin, comercial, ...) para rutas compartidas entre roles.
 */
class CheckRol
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Gerencia (20) accede a todo lo que puede Admin (1)
        $rolEfectivo = Auth::check() && Auth::user()->rol == 20 ? '1' : (string) optional(Auth::user())->rol;
        if (!Auth::check() || !in_array($rolEfectivo, $roles, true)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
