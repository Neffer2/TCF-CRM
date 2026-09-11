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
        if (!Auth::check() || !in_array((string) Auth::user()->rol, $roles, true)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
