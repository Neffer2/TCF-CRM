<?php

namespace App\Http\Controllers;

use App\Imports\BaseComercialImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Base_comercial;

class ComercialController extends Controller 
{
    public function index (){ 
        return view('comercial.index');
    }

    public function show_upload (){
        return view('comercial.base.upload');
    }

    public function showActualizarPerfil (){
        return view('comercial.ajustes.perfil.actualizar');
    }

    public function upload_base (Request $request){ 
         
        $request->validate([
            'base_xls' => 'required|mimes:xlsx, csv, xls'
        ]);  

        Base_comercial::truncate();  
        Excel::import(new BaseComercialImport, request()->file('base_xls')->store('temp'));  
        return redirect()->route('dashboard')->with('success', '¡Base comercial cargada exitosamente!');
    } 
}
 