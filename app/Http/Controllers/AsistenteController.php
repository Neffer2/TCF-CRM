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
}
