<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LiderProduccionController extends Controller
{
    public function index(){
        $productores = User::select('id', 'name')->where('rol', 7)->get();
        return view('lider-produccion.index', ['productores' => $productores ]);
    }
}
