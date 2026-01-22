<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Helisa;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HelisaExport;
use App\Http\Livewire\Com\Presupuesto\Presupuesto;
use Illuminate\Support\Facades\Auth;
use App\Exports\ConsumidosExport;
use App\Exports\PlanoExport;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Controller
    |--------------------------------------------------------------------------
    | Este controlador es responsable de manejar las vistas y acciones del usuario administrador.
    | Las funciones que comienzan con "show" e "index" son para mostrar vistas, las otras funciones son para acciones.
    | La función "exportHelisa" llama a una clase llamada HelisaExport, esta clase es responsable de exportar los datos a un archivo excel.
    */

    /**
     * Muestra la página principal del panel de administración
     *
     * @return \Illuminate\View\View
     */
    public function index (){
        return view('admin.index');
    }

    /**
     * Muestra la página del equipo con la lista de todos los usuarios
     *
     * @return \Illuminate\View\View
     */
    public function show_team (){
        // Obtener todos los usuarios de la base de datos
        $listUsers = User::all();
        return view('admin.team.index', ['listUsers' => $listUsers]);
    }

    /**
     * Muestra la página para actualizar el perfil del administrador
     *
     * @return \Illuminate\View\View
     */
    public function showActualizarPerfil (){
        return view('admin.ajustes.perfil.actualizar');
    }

    /**
     * Muestra la página de base comercial general con filtros aplicados
     *
     * @param Request $request - Contiene los filtros: año, mes, comercial, estado
     * @return \Illuminate\View\View
     */
    public function showBaseComercialGeneral (Request $request){
        // Crear array con los filtros recibidos del request
        $filtro = ['año' => $request->año, 'mes' => $request->mes, 'comercial' => $request->comercial, 'estado' => $request->estado];
        return view('admin.data.base-comercial', ['filtros' => $filtro]);
    }

    /**
     * Muestra la página general de Helisa
     *
     * @return \Illuminate\View\View
     */
    public function showHelisaGeneral() {
        return view('admin.generales.helisa');
    }

    /**
     * Muestra la página de presupuestos (acciones)
     *
     * @return \Illuminate\View\View
     */
    public function showPresupuestos (){
        return view('admin.acciones.presupuesto');
    }

    /**
     * Muestra la página del estado de facturación con filtros aplicados
     *
     * @param Request $request - Contiene los filtros: año, mes, comercial
     * @return \Illuminate\View\View
     */
    public function estadoFacturacion(Request $request){
        return view('admin.data.estado-facturacion', ['año' => $request->año, 'mes' => $request->mes, 'comercial' => $request->comercial]);
    }

    /**
     * Muestra la página de presupuestos de proyecto (gestión)
     *
     * @return \Illuminate\View\View
     */
    public function showPresupuestosProyecto(){
        return view('admin.gestion.presupuestos');
    }

    /**
     * Muestra la página de actualizaciones del sistema
     *
     * @return \Illuminate\View\View
     */
    public function actualizaciones(){
        return view('admin.gestion.actualizaciones');
    }

    /**
     * Muestra la página principal de órdenes de compra (producción)
     *
     * @return \Illuminate\View\View
     */
    public function showOrdenesCompra(){
        return view('admin.produccion.index');
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

        return view('admin.produccion.ordenes.juridica', ['presupuesto' => $presupuesto, 'orden' => $orden, 'proveedores' => $proveedores]);
    }

    /**
     * Muestra la página de lista de consumidos
     *
     * @return \Illuminate\View\View
     */
    public function showConsumidos(){
        return view('admin.produccion.consumidos.list');
    }

    /**
     * Reporte de consumidos
     *
     * @return \Illuminate\View\View
     */
    public function reporteConsumidos($mes = null){
        if (Auth::user()->rol == 1  || Auth::user()->rol == 6){
            return Excel::download(new ConsumidosExport($mes), "reporte_consumidos-{$mes}.xlsx");
        }else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Reporte de plano Helisa
     *
     * @return \Illuminate\View\View
     */
    public function reportePlanoHelisa($mes = null){
        if (Auth::user()->rol == 1 || Auth::user()->rol == 10){
            return Excel::download(new PlanoExport($mes), "reporte_plano_helisa-{$mes}.xlsx");
        }else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Muestra la página de un consumido específico basado en el presupuesto
     * También determina el rol del usuario autenticado
     *
     * @param int $presupuesto_id - ID del presupuesto
     * @return \Illuminate\View\View
     */
    public function showConsumido($presupuesto_id){
        // Determinar el rol del usuario autenticado
        if (Auth::user()->rol == 1){
            $rol = 'admin';
        }else {
            // Si el rol es 2 es comercial, si no es productor
            $rol = (Auth::user()->rol == 2) ? 'comercial' : 'productor';
        }

        return view('admin.produccion.consumidos.index', ['presupuesto_id' => $presupuesto_id, 'rol' => $rol]);
    }

    /**
     * Exporta los datos de Helisa a un archivo Excel con filtros opcionales
     *
     * @param string|null $comercial - ID del comercial para filtrar (opcional)
     * @param string|null $centro - Centro para filtrar (opcional)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse - Descarga del archivo Excel
     */
    public function exportHelisa($comercial = null, $centro = null){
        // Inicializar array de filtros como variable local
        $filtros = [];

        // Si se especifica un comercial, agregar al filtro y personalizar el título
        if ($comercial != 'none'){
            $title = "Reporte Helisa - ".User::find($comercial)->name.".xlsx";
            array_push($filtros, ['comercial', $comercial]);
        }else {
            $title = "Reporte Helisa.xlsx";
        }

        // Si se especifica un centro, agregar al filtro usando LIKE para búsqueda parcial
        if($centro != 'none'){
            array_push($filtros, ['centro', 'LIKE', "%{$centro}%"]);
        }

        // Obtener registros de Helisa aplicando los filtros
        $registros_helisa = Helisa::where($filtros)->get();

        // Generar y descargar el archivo Excel usando la clase HelisaExport
        return Excel::download(new HelisaExport(['registros_helisa' => $registros_helisa]), $title);
    }

    /**
     * Genera el PDF de una orden de compra para visualizar/embeder
     *
     * @param \App\Models\OrdenCompra $orden
     * @return \Illuminate\Http\Response
     */
    public function ordenCompraPdf(OrdenCompra $orden)
    {
        $orden->load([
            'proveedor',
            'presupuesto',
//            'presupuesto.gestion.contacto',
            'presupuesto.presupuestoItems',
            'ordenItems'
        ]);

        foreach ($orden->ordenItems as $item) {
            $orden->subtotal += $item->vtotal_oc;
        }

        $dompdf = new Dompdf(['enable_remote' => true]);
        $html = View::make('exports.orden_compra_pdf', [
            'orden' => $orden,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="orden_compra_'.$orden->id.'.pdf"');
    }
}
