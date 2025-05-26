<?php

namespace App\Http\Controllers;

use App\Models\PresupuestoProyecto;
use Illuminate\Support\Facades\Auth;
use App\Models\Año;

class ProductorController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | ProductorController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing lider de productor actions and views.
    */

    public function index(){
        // id_estado =  1 CERRADO
<<<<<<< HEAD
        $proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->whereHas('baseComercial', function($query){
            // return $query->where('id_estado', '!=', 1);
            return true;
        })
=======
        $proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')
>>>>>>> dev
        ->where([
            ['productor', Auth::id()],
            ['fecha_cc', '>=', Año::orderBy('description', 'desc')->first()->description.'-01-01']
        ])->orderBy('id', 'desc')
        ->paginate(17);

        return view('productor.index', ['proyectos' => $proyectos]);
    }

    public function showRemision($orden){
        return view('productor.remision.index', ['orden' => $orden]);
    }
}
