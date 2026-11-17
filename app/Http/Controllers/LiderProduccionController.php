<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class LiderProduccionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LiderProduccionController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing lider de produccion actions and views.
    | index function shows the view of the lider de produccion according to the user's role.
    */

    public function showAsignacionProyectos(){
        $productores = User::select('id', 'name')->where('rol', 7)->get();
        return view('lider-produccion.index', ['productores' => $productores ]);
    }

    public function showListaAnticipos(){
        return view('lider-produccion.anticipos.lista-anticipos-index');
    }

    public function showactualizaciones(){
        return view('lider-produccion.anticipos.actualizaciones-anticipos-index');
    }

    public function showOrdenesCompra(){
        return view('lider-produccion.ordenes.index');      
    }

    /**
     * Muestra la página de orden jurídica específica
     *
     * @param int $orden_id - ID de la orden de compra
     * @return \Illuminate\View\View
     */

    public function showOrdenJuridica($orden_id){
        // Buscar la orden de compra por ID
        $orden = OrdenCompra::find($orden_id);
        // Obtener el presupuesto relacionado con la orden
        $presupuesto = $orden->presupuesto;
        // Obtener todos los proveedores (solo ID y tercero)
        $proveedores = Proveedor::select('id', 'tercero')->get();
        return view('lider-produccion.ordenes.ordenescompra.orden-juridica', ['presupuesto' => $presupuesto, 'orden' => $orden, 'proveedores' => $proveedores]);
    }

    /**
     * Muestra la página de orden jurídica específica
     *
     * @param int $orden_id - ID de la orden de compra
     * @return \Illuminate\View\View
     */

    public function showOrdenNatural($orden_id){

        $orden = OrdenCompra::find($orden_id);

        $presupuesto = $orden->presupuesto;

        $proveedores = Proveedor::select('id','tercero');

        return view('lider-produccion.ordenes.ordenescompra.orden-natural', ['presupuesto' => $presupuesto, 'orden' => $orden, 'proveedores' => $proveedores]);
    }

    public function ordenCompraPdfLider(OrdenCompra $orden)
    {
        $orden->load([
            'proveedor',
            'presupuesto.gestion.contacto',
            'presupuesto.presupuestoItems',
            'ordenItems',
        ]);

        $dompdf = new Dompdf(['enable_remote' => true]);
        $html = View::make('exports.orden_compra_pdf', [
            'orden' => $orden,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Cache-Control', 'private, no-store')
            ->header('Content-Disposition', 'inline; filename="orden_compra_'.$orden->id.'.pdf"');
    }
}
