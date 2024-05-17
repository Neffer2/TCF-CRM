<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContabilidadController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Contabilidad Controller
    |--------------------------------------------------------------------------
    | This controller is responsible for managing the Contabilidad actions and views.
    | Functions wich start with "show" and index, are for show views, the others functions are for actions.
    */

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
