<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistente;
use Illuminate\Support\Facades\Auth;

class AsistenteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Asistente Controller
    |--------------------------------------------------------------------------
    | Este controlador era responsable de manejar las acciones y vistas del asistente.
    | Los usuarios asistentes fueron deshabilitados del CRM, por lo que este controlador ya no está en uso.
    */

    /**
     * Muestra la página para actualizar el perfil del asistente
     *
     * @return \Illuminate\View\View
     */
    public function showActualizarPerfil(){
        return view('asistente.ajustes.perfil.actualizar');
    }

    /**
     * Muestra la página principal del asistente con información del comercial asignado
     *
     * @return \Illuminate\View\View
     */
    public function index (){
        // Buscar el comercial asignado al asistente autenticado
        $comercialAsignado = Asistente::where('asistente_id', Auth::user()->id)->first();
        return view('asistente.index', ['comercial' => $comercialAsignado->comercial->name]);
    }

    /**
     * Muestra la página de gestión comercial del asistente
     *
     * @return \Illuminate\View\View
     */
    public function gestionComercial(){
        return view('asistente.gestion');
    }

    /**
     * Muestra la página de gestión Helisa con información del comercial asignado
     *
     * @return \Illuminate\View\View
     */
    public function gestionHelisa(){
        // Obtener el comercial asignado al asistente autenticado
        $comercialAsignado = Asistente::where('asistente_id', Auth::user()->id)->first();
        return view('asistente.helisa.index', ['comercial' => $comercialAsignado->comercial->name]);
    }

    /**
     * Muestra la página de contactos del asistente
     *
     * @return \Illuminate\View\View
     */
    public function Contactos(){
        return view('asistente.contactos');
    }

    /**
     * Muestra la página de base comercial con información del comercial asignado
     * Incluye tanto el nombre como el ID del comercial
     *
     * @return \Illuminate\View\View
     */
    public function base(){
        // Buscar el comercial asignado al asistente autenticado
        $comercialAsignado = Asistente::where('asistente_id', Auth::user()->id)->first();
        return view('asistente.base', [
            'comercial' => $comercialAsignado->comercial->name,
            'comercial_id' => $comercialAsignado->comercial_id
        ]);
    }
}
