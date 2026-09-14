<?php

namespace App\Http\Controllers;

use App\Models\Asistente;
use App\Models\Permiso;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * "Mi perfil": ficha de solo lectura con la foto, el cargo (rol activo), los
 * roles asignados, el equipo y los permisos especiales del usuario que entra.
 * Los datos y la clave los cambia un desarrollador; aquí solo se consultan.
 */
class PerfilController extends Controller
{
    public function show()
    {
        $u = Auth::user();

        $roles = $u->roles()->get(['roles_user.id', 'roles_user.description']);
        $permisos = Permiso::whereIn('id', DB::table('permiso_user')->where('user_id', $u->id)->pluck('permiso_id'))
            ->orWhereIn('id', DB::table('permiso_rol')->whereIn('rol_id', $roles->pluck('id'))->pluck('permiso_id'))
            ->get(['clave', 'descripcion']);

        // Relaciones comerciales: a quién reporta y a quién tiene a cargo
        $lideres = $u->lideres()->get(['users.id', 'users.name']);
        $equipo = $u->comercialesAsignados()->get(['users.id', 'users.name']);

        // Ejecutivo de cuenta: comercial al que asiste; comercial: sus ejecutivos
        $asisteA = User::whereIn('id', Asistente::where('asistente_id', $u->id)->pluck('comercial_id'))->get(['id', 'name']);
        $asistentes = User::whereIn('id', Asistente::where('comercial_id', $u->id)->pluck('asistente_id'))->get(['id', 'name']);

        return view('perfil.index', compact('u', 'roles', 'permisos', 'lideres', 'equipo', 'asisteA', 'asistentes'));
    }
}
