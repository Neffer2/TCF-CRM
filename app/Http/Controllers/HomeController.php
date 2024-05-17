<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{ 
    /*
    |--------------------------------------------------------------------------
    | HomeController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing general actions and views.
    | "dashboard" function is for redirect the user to the correct dashboard according to the user's role.
    | "showProveedores" function shows the view of the proveedores according to the user's role (each rol has diferent permises on proveedores view).
    */

    public function dashboard (){ 
        if (Auth::user()->rol == 1) {
            return redirect()->route('dashboard-admin');
        }elseif (Auth::user()->rol == 2){
            return redirect()->route('dashboard-com');
        }elseif (Auth::user()->rol == 3){
            return redirect()->route('dashboard-con');
        }elseif (Auth::user()->rol == 5){
            return redirect()->route('dashboard-asis');  
        }elseif (Auth::user()->rol == 6){
            return redirect()->route('dashboard-lider-produccion');  
        }elseif (Auth::user()->rol == 7){
            return redirect()->route('dashboard-productor');  
        }elseif (Auth::user()->rol == 8){
            return redirect()->route('dashboard-tesoreria');   
        }elseif (Auth::user()->rol == 9){
            return redirect()->route('dashboard-contabilidad');   
        }
    }

    public function showProveedores (){
        if (Auth::user()->rol == 1) {
            return view('admin.produccion.proveedores.index');
        }elseif (Auth::user()->rol == 2){
            return view('comercial.produccion.proveedores.index');
        }elseif (Auth::user()->rol == 7){
            return view('productor.proveedores.index'); 
        }  
    }
}  
 