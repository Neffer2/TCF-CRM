<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TesoreriaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ProductorController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing lider de productor actions and views.
    | Index function is for show the main lider produccion view. This function should not be here. I was made for provisional purposes.
    */

    public function index(){ 
        return view('tesoreria.index');
    }

    public function showAnticipos(){
        return view('tesoreria.anticipos.anticipos');
    }

    public function showAnticipo($orden){
        return view('tesoreria.anticipos.anticipo', ['orden_id' => $orden]);
    }
}
 