<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\HelisaExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HelisaContableImport; 
use App\Models\Helisa;

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
        return redirect()->route('dashboard-con')->with('success', '¡Reporte Helisa cargado exitosamente!');
    }

    public function helisa_truncate (){
        Helisa::truncate();
        return redirect()->route('dashboard-con')->with('success', '¡La base datos ha sido vaciada con éxito!');
    }

    // Hubo que hacer esto porque livewire no es compatible con el datatable
    public function delete_registro($id){
        Helisa::destroy($id);
        return redirect()->back()->with('success', 'Registrado eliminado exitosamente.');
    } 
 
    public function export_helisa(){
        return Excel::download(new HelisaExport, "Reporte Helisa.xlsx");
    }
}
  