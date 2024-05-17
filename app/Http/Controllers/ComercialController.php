<?php

namespace App\Http\Controllers;

use App\Imports\BaseSheetHandler;
use App\Exports\CotExport;
use App\Exports\BaseExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request; 
use App\Models\Base_comercial;
use App\Models\GestionComercial;
use App\Models\PresupuestoProyecto;
use App\Models\ItemPresupuesto;
use App\Models\Proveedor;
use App\Models\Contacto;
use App\Models\Helisa;
use App\Models\Asistente;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class ComercialController extends Controller 
{
    /*
    |--------------------------------------------------------------------------
    | Comercial Controller
    |--------------------------------------------------------------------------
    | This controller is responsible for managing the comercial actions and views.
    | Functions wich start with "show" and index, are for show views, the others functions are for actions.
    | "cotizacionPdf" function calls a class named Dompdf, this class is responsible for exporting the data to a pdf file.
    */

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

    public function showConsumidos(){ 
        return view('comercial.produccion.consumidos.list'); 
    }

    public function update_gestion($leadId){
        return view('comercial.gestion.edit', ['leadId' => $leadId]);
    }

    public function Contactos(){
        return view('comercial.contactos');  
    } 
    
    public function presupuesto($id_gestion){ 
        return view('comercial.presupuesto.index', ['id_gestion' => $id_gestion]); 
    }

    public function presupuestos(){ 
        return view('comercial.presupuesto.list');    
    }

    /* Tipo: Interno, cliente */
    public function cotizacionPdf($prespuesto, $nom_proyecto, $tipo){ 
        $presto = PresupuestoProyecto::where('id_gestion', $prespuesto)->first();
        $items = ItemPresupuesto::where('presupuesto_id', $presto->id)->get();

        $dompdf = new Dompdf(array('enable_remote' => true));
        $html = View::make('exports.pdf', ['presto' => $presto, 'items' => $items, 'tipo' => $tipo])->render(); 
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($nom_proyecto);  
    }

    public function cotizacionExcel($prespuesto, $nom_proyecto, $tipo) {                
        $presto = PresupuestoProyecto::where('id_gestion', $prespuesto)->first();
        $items = ItemPresupuesto::where('presupuesto_id', $presto->id)->get();        
        $proveedores = Proveedor::select('id', 'categoria_id', 'tercero')->get();        

        return Excel::download(new CotExport(['presto' => $presto, 'items' => $items, 'tipo' => $tipo, 'proveedores' => $proveedores]), $nom_proyecto.".xlsx"); 
    }
 
    // DEPRECATED
    public function pdf(){
        $dompdf = new Dompdf(array('enable_remote' => true));
        $html = View::make('pdf.index')->render();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream();
    } 

    public function delete_proyecto($user_id){ 
        Base_comercial::destroy($user_id);
        return redirect()->back()->with('success', 'Proyecto eliminado exitosamente.');
    }

    public function delete_registro($centro, $num_doc){ 
        Helisa::where('centro', $centro)->where('num_doc', $num_doc)->delete(); 
        return redirect()->back()->with('success', 'Registro eliminado exitosamente.');
    }  
   
    public function upload_base (Request $request){          
        return redirect()->route('dashboard-base')->with('error', '¡Ésta función fué descontinuada.!');
        $request->validate([
            'base_xls' => 'required|mimes:xlsx, csv, xls'
        ]);    
 
        Base_comercial::where('id_user', Auth::user()->id)->delete();  
        Excel::import(new BaseSheetHandler, $request->base_xls);   
        return redirect()->route('dashboard-base')->with('success', '¡Base comercial cargada exitosamente!');
    }

    public function export_base($user_id){
        if (Auth::user()->rol == 5){
            $name = Asistente::where('asistente_id', Auth::user()->id)->first();  
            return Excel::download(new BaseExport, $name->comercial->name." Base.xlsx");    
        } 
                
        $name = Auth::user()->name;
        return Excel::download(new BaseExport, $name." Base.xlsx");
    }

    public function delete_contacto($id){ 
        $gestionComercial = GestionComercial::where('id_contacto', $id)->first();
        
        if ($gestionComercial){
            return redirect()->back()->withErrors(['Éste usuario esta enlazado con una de tus gestiones comerciales.']);
        }
        
        Contacto::destroy($id);
        return redirect()->back()->with('success', 'Contacto eliminado exitosamente.');
    }

    public function update_contacto($id, Request $request){ 
        $request->validate([
            "cargo_edit" => 'required|string',
            "celular_edit" => 'required|numeric',
            "correo_edit" => 'required|email',
            "pbx_edit" => 'required|string',
            "web_edit" => 'required|string',
            "direccion_edit" => 'required|string'
        ]);        

        $contacto = Contacto::find($id); 
        $contacto->cargo = $request->cargo_edit;
        $contacto->celular = $request->celular_edit;
        $contacto->correo = $request->correo_edit;
        $contacto->web = $request->pbx_edit;
        $contacto->pbx = $request->web_edit;
        $contacto->direccion = $request->direccion_edit;
        $contacto->update();

        return redirect()->back()->with('success', 'Contacto actualizado exitosamente!');
    }
}
  