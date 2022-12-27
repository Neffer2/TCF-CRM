<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HelisaContableImport;

class ContableController extends Controller
{
    public function index (){ 
        return view('contable.index');
    } 

    public function showActualizarPerfil (){
        return view('contable.ajustes.perfil.actualizar');
    }

    public function helisa_upload (Request $request){
        $request->validate([
            'helisa_xls' => 'required|mimes:xlsx, csv, xls'
        ]);

        Excel::import(new HelisaContableImport, $request->helisa_xls);  
        return redirect()->route('dashboard')->with('success', '¡Base comercial cargada exitosamente!');
    }
}
  