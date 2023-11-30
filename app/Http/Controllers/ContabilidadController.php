<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContabilidadController extends Controller
{
    public function index(){ 
        return view('contabilidad.index');
    }

    public function showAnticipos(){
        return view('contabilidad.anticipos.anticipos');
    }

    public function showAnticipo($orden){
        return view('contabilidad.anticipos.anticipo', ['orden_id' => $orden]);
    }
} 
