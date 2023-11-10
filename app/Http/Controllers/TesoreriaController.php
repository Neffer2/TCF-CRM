<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TesoreriaController extends Controller
{
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
 