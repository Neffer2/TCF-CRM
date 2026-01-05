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
        $proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')
        ->where([
            ['productor', Auth::id()],
            ['fecha_cc', '>=', Año::orderBy('description', 'desc')->first()->Meses->first()->f_inicio]
        ])->orderBy('id', 'desc')
        ->paginate(17);

        return view('productor.index', ['proyectos' => $proyectos]);
    }

    public function showRemision($orden){
        return view('productor.remision.index', ['orden' => $orden]);
    }
}
