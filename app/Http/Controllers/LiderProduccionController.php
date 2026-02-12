<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Models\User;

class LiderProduccionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LiderProduccionController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing lider de produccion actions and views.
    | index function shows the view of the lider de produccion according to the user's role.
    */

    public function index(){
        $productores = User::select('id', 'name', 'rol')->where('rol', 7)->get();
        return view('lider-produccion.index', ['productores' => $productores ]);
    }

    public function showOrdenJuridica($orden_id) {
        // Buscar la orden de compra por ID
        $orden = OrdenCompra::find($orden_id);
        // Obtener el presupuesto relacionado con la orden
        $presupuesto = $orden->presupuesto;
        // Obtener todos los proveedores (solo ID y tercero)
        $proveedores = Proveedor::select('id', 'tercero')->get();

        return view('admin.produccion.ordenes.juridica', ['presupuesto' => $presupuesto, 'orden' => $orden, 'proveedores' => $proveedores]);
    }
}
