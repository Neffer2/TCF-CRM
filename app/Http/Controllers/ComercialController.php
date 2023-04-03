<?php

namespace App\Http\Controllers;

use App\Imports\BaseSheetHandler;
use App\Exports\BaseExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request; 
use App\Models\Base_comercial;
use App\Models\Helisa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ComercialController extends Controller 
{
    public function index(){  
        return view('comercial.index');
    }

    public function gestionComercial(){
        return view('comercial.gestion');
    }

    public function gestionHelisa(){
        return view('comercial.helisa.index'); 
    }
    
    public function base(){ 
        return view('comercial.base');
    } 

    public function show_upload(){
        return view('comercial.base.upload');
    } 
 
    public function showActualizarPerfil(){
        return view('comercial.ajustes.perfil.actualizar');
    }  

    public function comercialHelisa(){
        return view('comercial.helisa.index');
    }

    public function Contactos(){
        return view('comercial.contactos'); 
    }

    // Hubo que hacer esto porque livewire no es compatible con el datatable
    public function delete_proyecto($user_id){
        Base_comercial::destroy($user_id);
        return redirect()->back()->with('success', 'Proyecto eliminado exitosamente.');
    } 

    public function update_proyecto(Request $request, $proyecto_id){ 
        $request->validate([
            'nom_cliente' => 'string|min:0',
            'nom_proyecto' => 'string|min:0',
            'cod_cc' => 'string|min:0',
            'valor_proyecto' => 'numeric', 
            'id_estado' => 'numeric',
            'id_cuenta' => 'numeric' 
        ]);  

        $proyecto = Base_comercial::where('id',$proyecto_id)->first(); 
        if ($request->nom_cliente){
            $proyecto->nom_cliente = $request->nom_cliente;
        }

        if ($request->nom_proyecto){
            $proyecto->nom_proyecto = $request->nom_proyecto;
        }

        if ($request->cod_cc){
            $proyecto->cod_cc = $request->cod_cc;
        }

        if ($request->valor_proyecto){
            $proyecto->valor_proyecto = $request->valor_proyecto;
        }
        
        if ($request->estado_id){
            $proyecto->id_estado = $request->estado_id;
        }

        if ($request->id_cuenta){
            $proyecto->id_cuenta = $request->id_cuenta;
        }
        
        $proyecto->id_asistente = Auth::user()->id;

        $proyecto->update();
        return redirect()->back()->with('success', 'Proyecto actualizado exitosamente.');
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

        return redirect()->route('gestion-helisa')->with('success', '¡Datos guardados exitosamente!');
    }
    
    public function upload_base (Request $request){          
        $request->validate([
            'base_xls' => 'required|mimes:xlsx, csv, xls'
        ]);   
 
        Base_comercial::where('id_user', Auth::user()->id)->delete();  
        Excel::import(new BaseSheetHandler, $request->base_xls);   
        return redirect()->route('dashboard-base')->with('success', '¡Base comercial cargada exitosamente!');
    }

    public function export_base($user_id){
        $name = Auth::user()->name;
        return Excel::download(new BaseExport, $name." Base.xlsx");
    }
}
 