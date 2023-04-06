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

    public function update_helisa(Request $request, $proyecto_id){
        $request->validate([
            'fecha' => ['required'],
            'tipo_doc' => ['required', 'string'],
            'num_doc' => ['required', 'string'],
            'concepto' => ['required', 'string'],
            'identidad' => ['required', 'string'],
            'nom_tercero' => ['required', 'string'],
            'centro' => ['required', 'string'],
            'nom_centro_costo' => ['required', 'string'],
            'debito' => ['required', 'numeric'],
            'credito' => ['required', 'numeric'], 
            'porcentaje' => ['numeric'], 
            'comercial' => ['required', 'numeric'],
            'id_cuenta' => ['numeric'],
            // 'participacion' => ['required', 'numeric'],
            'base_factura' => ['required', 'numeric'],
            'mes' => ['required', 'string'],
            'año' => ['required', 'string'],
            'comision' => ['required', 'numeric']
        ]); 

        $helisa = Helisa::where('id', $proyecto_id)->first(); 

        $helisa->fecha = $request->fecha;
        $helisa->tipo_doc = $request->tipo_doc;
        $helisa->num_doc = $request->num_doc;
        $helisa->concepto = $request->concepto;
        $helisa->identidad = $request->identidad;
        $helisa->nom_tercero = $request->nom_tercero;
        $helisa->centro = $request->centro;
        $helisa->nom_centro_costo = $request->nom_centro_costo;
        $helisa->debito = $request->debito;
        $helisa->credito = $request->credito;
        $helisa->comercial = $request->comercial;
        $helisa->base_factura = $request->base_factura;

        if ($request->id_cuenta){ 
            $helisa->id_cuenta = $request->id_cuenta;
        }

        $helisa->participacion = 0;
        $helisa->base_factura = $request->base_factura;
        $helisa->mes = $request->mes;
        $helisa->año = $request->año;
        $helisa->comision = $request->comision;
        $helisa->update();

        return redirect()->route('dashboard')->with('success', '¡Datos guardados exitosamente!');
    }
 
    public function export_helisa(){
        return Excel::download(new HelisaExport, "Reporte Helisa.xlsx");
    }
}
  