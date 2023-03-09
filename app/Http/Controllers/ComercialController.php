<?php

namespace App\Http\Controllers;

use App\Imports\BaseSheetHandler;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Base_comercial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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


    // Hubo que hacer esto porque livewire no es compatible con el datatable
    public function delete_proyecto($user_id){
        Base_comercial::destroy($user_id);
        return redirect()->back()->with('success', 'Proyecto eliminado exitosamente.');
    } 

    public function update_proyecto(Request $request, $user_id){
        $proyecto = Base_comercial::where('id',$user_id)->first(); 
        $proyecto->id_estado = $request->estado_id;
        $proyecto->update();

        return redirect()->back()->with('success', 'Estado actualizado exitosamente.');
    }
    

    public function upload_base (Request $request){ 
         
        $request->validate([
            'base_xls' => 'required|mimes:xlsx, csv, xls'
        ]);   

        Base_comercial::where('id_user', Auth::user()->id)->delete();  
        Excel::import(new BaseSheetHandler, $request->base_xls);  
        return redirect()->route('dashboard')->with('success', '¡Base comercial cargada exitosamente!');
    } 
}
 