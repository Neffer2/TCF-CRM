<?php

namespace App\Http\Controllers; 

use App\Models\PresupuestoProyecto;
use Illuminate\Support\Facades\Auth;
use App\Models\Proveedor;

class ProductorController extends Controller
{   
    
    /*
    |--------------------------------------------------------------------------
    | ProductorController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing lider de productor actions and views.
    */

    public function index(){
        $proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('productor', Auth::id())->get();
        return view('productor.index', ['proyectos' => $proyectos]);
    }
 
    public function showRemision($orden){
        return view('productor.remision.index', ['orden' => $orden]);
    }
} 