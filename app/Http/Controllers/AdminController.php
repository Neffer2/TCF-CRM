<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Helisa;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HelisaExport;

use Illuminate\Support\Facades\Auth;
 
class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Controller
    |--------------------------------------------------------------------------
    | This controller is reponsible for managing the views and actions of the admin user.
    | Functions wich start with "show" and index, are for show views, the others functions are for actions.
    | "exportHelisa" function calls a class named HelisaExport, this class is responsible for exporting the data to an excel file.
    */

    public function index (){
        return view('admin.index');  
    }
 
    public function show_team (){
        $listUsers = User::all(); 
        return view('admin.team.index', ['listUsers' => $listUsers]);  
    }

    public function showActualizarPerfil (){ 
        return view('admin.ajustes.perfil.actualizar');
    } 

    public function showBaseComercialGeneral (Request $request){
        $filtro = ['año' => $request->año, 'mes' => $request->mes, 'comercial' => $request->comercial, 'estado' => $request->estado];
        return view('admin.data.base-comercial', ['filtros' => $filtro]);
    }

    public function showHelisaGeneral() {
        return view('admin.generales.helisa');
    }

    public function showPresupuestos (){
        return view('admin.acciones.presupuesto');
    }

    public function estadoFacturacion(Request $request){ 
        return view('admin.data.estado-facturacion', ['año' => $request->año, 'mes' => $request->mes, 'comercial' => $request->comercial]); 
    }  

    public function showPresupuestosProyecto(){ 
        return view('admin.gestion.presupuestos');
    }

    public function actualizaciones(){ 
        return view('admin.gestion.actualizaciones');  
    }

    public function showOrdenesCompra(){
        return view('admin.produccion.index');  
    } 

    public function showOrdenJuridica($orden_id){ 
        $orden = OrdenCompra::find($orden_id); 
        $presupuesto = $orden->presupuesto; 
        $proveedores = Proveedor::select('id', 'tercero')->get();

        return view('admin.produccion.ordenes.juridica', ['presupuesto' => $presupuesto, 'orden' => $orden, 'proveedores' => $proveedores]);  
    }

    public function showConsumidos(){ 
        return view('admin.produccion.consumidos.list');
    }

    public function showConsumido($presupuesto_id){
        if (Auth::user()->rol == 1){
            $rol = 'admin';
        }else {            
            $rol = (Auth::user()->rol == 2) ? 'comercial' : 'productor';
        }

        return view('admin.produccion.consumidos.index', ['presupuesto_id' => $presupuesto_id, 'rol' => $rol]);
    } 

    public function exportHelisa($comercial = null, $centro = null){     
        $this->filtros = [];

        if ($comercial != 'none'){
            $title = "Reporte Helisa - ".User::find($comercial)->name.".xlsx";
            array_push($this->filtros, ['comercial', $comercial]);
        }else {
            $title = "Reporte Helisa.xlsx";
        }

        if($centro != 'none'){
            array_push($this->filtros, ['centro', 'LIKE', "%{$centro}%"]);
        }

        $registros_helisa = Helisa::where($this->filtros)->get(); 

        return Excel::download(new HelisaExport(['registros_helisa' => $registros_helisa]), $title); 
    }
}
