<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EstadoCuenta;
 
class AdminController extends Controller
{
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

    public function showBaseComercialGeneral (){
        return view('admin.data.base-comercial');
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
}
