<?php

namespace App\Http\Controllers;  

use Illuminate\Http\Request; 
use App\Models\Asistente;
use Illuminate\Support\Facades\Auth; 

class AsistenteController extends Controller
{   
    public function index (){ 
        $comercialAsignado = Asistente::where('asistente_id', Auth::user()->id)->first();
        return view('asistente.index', ['comercial' => $comercialAsignado->comercial->name]);
    } 
 
    public function gestionComercial(){ 
        return view('asistente.gestion'); 
    }
 
    public function gestionHelisa(){ 
        $comercialAsignado = Asistente::where('asistente_id', Auth::user()->id)->first();
        return view('asistente.helisa.index', ['comercial' => $comercialAsignado->comercial->name]); 
    }

    public function Contactos(){
        return view('asistente.contactos'); 
    }

    public function base(){ 
        $comercialAsignado = Asistente::where('asistente_id', Auth::user()->id)->first();
        return view('asistente.base', ['comercial' => $comercialAsignado->comercial->name, 'comercial_id' => $comercialAsignado->comercial_id]); 
    } 
} 
