<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*
    |--------------------------------------------------------------------------
    | Controller
    |--------------------------------------------------------------------------
    | This controller is responsible for managing general actions and views.
    | Index function is for show the main lider produccion view. This function should not be here. I was made for provisional purposes.
    */

    public function index(){
        return view('lider-produccion.index');
    }
}
