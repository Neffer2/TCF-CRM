<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LiderProduccionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LiderProduccionController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing lider de produccion actions and views.
    | index function shows the view of the lider de produccion according to the user's role.
    */

    public function index(){
        $productores = User::select('id', 'name')->where('rol', 7)->get();
        return view('lider-produccion.index', ['productores' => $productores ]);
    }
}
