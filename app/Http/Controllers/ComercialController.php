<?php

namespace App\Http\Controllers;

// Importación de clases para manejo de archivos Excel
use App\Exports\CotExport;
use App\Exports\BaseExport;
use App\Models\HistorialItemPresupuesto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

// Importación de clases base de Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

// Importación de modelos de la aplicación
use App\Models\Base_comercial;
use App\Models\GestionComercial;
use App\Models\PresupuestoProyecto;
use App\Models\ItemPresupuesto;
use App\Models\Proveedor;
use App\Models\Contacto;
use App\Models\Helisa;
use App\Models\Asistente;


use App\Traits\Email;

// Importación de librería para generación de PDFs
use Dompdf\Dompdf;

/**
 * Controlador principal para la gestión comercial del CRM
 *
 * Este controlador maneja todas las operaciones relacionadas con:
 * - Gestión comercial y leads
 * - Presupuestos y cotizaciones
 * - Base comercial y archivos Helisa
 * - Contactos y proveedores
 * - Exportación de datos en PDF y Excel
 */
class ComercialController extends Controller
{
    use WithFileUploads, Email;
    /*
    |--------------------------------------------------------------------------
    | Comercial Controller
    |--------------------------------------------------------------------------
    | This controller is responsible for managing the comercial actions and views.
    | Functions wich start with "show" and index, are for show views, the others functions are for actions.
    | "cotizacionPdf" function calls a class named Dompdf, this class is responsible for exporting the data to a pdf file.
    */

    /**
     * Muestra la página principal del módulo comercial
     * @return \Illuminate\View\View
     */
    public function index(){
        return view('comercial.index');
    }

    /**
     * Muestra la vista de gestión comercial para administrar leads y oportunidades
     * @return \Illuminate\View\View
     */
    public function gestionComercial(){
        return view('comercial.gestion');
    }

    public function gestionClientes(){
        return view('comercial.gestion.clientes');
    }

    /**
     * Muestra la vista principal para la gestión de archivos Helisa
     * @return \Illuminate\View\View
     */
    public function gestionHelisa(){
        return view('comercial.helisa.index');
    }

    /**
     * Muestra la vista de la base comercial
     * @return \Illuminate\View\View
     */
    public function base(){
        return view('comercial.base');
    }

    /**
     * Muestra la vista para cargar archivos de base comercial
     * @return \Illuminate\View\View
     */
    public function show_upload(){
        return view('comercial.base.upload');
    }

    /**
     * Muestra la vista principal de Helisa (alias del método gestionHelisa)
     * @return \Illuminate\View\View
     */
    public function comercialHelisa(){
        return view('comercial.helisa.index');
    }

    /**
     * Muestra la lista de productos consumidos en producción
     * @return \Illuminate\View\View
     */
    public function showConsumidos(){
        return view('comercial.produccion.consumidos.list');
    }

    /**
     * Muestra la vista para editar una gestión comercial específica
     * @param int $leadId ID del lead a editar
     * @return \Illuminate\View\View
     */
    public function update_gestion($leadId){
        return view('comercial.gestion.edit', ['leadId' => $leadId]);
    }

    /**
     * Muestra la vista de gestión de contactos
     * @return \Illuminate\View\View
     */
    public function Contactos(){
        return view('comercial.contactos');
    }

    /**
     * Muestra la vista de presupuesto para una gestión comercial específica
     * @param int $id_gestion ID de la gestión comercial
     * @return \Illuminate\View\View
     */
    public function presupuesto($id_gestion){
        return view('comercial.presupuesto.index', ['id_gestion' => $id_gestion]);
    }

    /**
     * Muestra la lista de todos los presupuestos
     * @return \Illuminate\View\View
     */
    public function presupuestos(){
        return view('comercial.presupuesto.list');
    }

    /**
     * Genera y descarga un PDF de cotización
     *
     * @param int $prespuesto ID del presupuesto
     * @param string $nom_proyecto Nombre del proyecto para el archivo
     * @param string $tipo Tipo de cotización (Interno/Cliente)
     * @return \Illuminate\Http\Response Descarga del PDF
     */
    /* Tipo: Interno, cliente */
    public function cotizacionPdf($prespuesto, $nom_proyecto, $tipo){
        // Obtener el presupuesto y sus items relacionados
        $presto = PresupuestoProyecto::where('id_gestion', $prespuesto)->first();
        
        if (!$presto) {
            abort(404, 'Presupuesto no encontrado.');
        }
        // Solo quien trabaja el proyecto (o su productor): la versión interna muestra costos y proveedores.
        abort_unless(Auth::user()->puedeVerPresupuesto($presto), 403);

        // Obtener sus items relacionados ORDENADOS por el campo 'orden'
        $items = ItemPresupuesto::where('presupuesto_id', $presto->id)
            ->orderBy('orden', 'asc')
            ->get();

        // Configurar Dompdf para permitir recursos remotos
        $dompdf = new Dompdf(array('enable_remote' => true));

        // Generar HTML desde la vista de exportación
        $html = View::make('exports.pdf', ['presto' => $presto, 'items' => $items, 'tipo' => $tipo])->render();

        // Cargar HTML en Dompdf, renderizar y descargar
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream($nom_proyecto);
    }

    /**
     * Genera y descarga un archivo Excel de cotización
     *
     * @param int $prespuesto ID del presupuesto
     * @param string $nom_proyecto Nombre del proyecto para el archivo
     * @param string $tipo Tipo de cotización (Interno/Cliente)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Descarga del Excel
     */
    public function cotizacionExcel($prespuesto, $nom_proyecto, $tipo) {
        $presto = PresupuestoProyecto::where('id_gestion', $prespuesto)->first();

        if (!$presto) {
            return redirect()->back()->with('error', 'El presupuesto no existe.');
        }
        abort_unless(Auth::user()->puedeVerPresupuesto($presto), 403);

        $allItems = ItemPresupuesto::where('presupuesto_id', $presto->id)->get();

        // Las mismas cifras de la pantalla del presupuesto (venta = ítems a precio cliente + recargos).
        // Antes se sumaba v_total_cliente, que no cuadra con la venta del proyecto.
        $costosProyecto = (float) $presto->costos_proy;
        $ventaProyecto = (float) $presto->venta_proy;
        $margenBruto = (float) $presto->margen_bruto;
        $margenProyecto = (float) $presto->margen_proy;

        // Margen Items con la MISMA fórmula que usa la pantalla (getMetricas):
        // sum(v_total con margen>0) / sum(v_total_cot). El promedio simple
        // (avg) producía un margen distinto en el Excel que en la aplicación.
        $sumTotalCot = $allItems->where('evento', 0)->sum('v_total_cot');
        $margenItems = $sumTotalCot > 0
            ? $allItems->where('evento', 0)->where('margen_utilidad', '>', 0)->sum('v_total') / $sumTotalCot
            : 0;

        $itemIds = $allItems->pluck('id');

        $itemsHistorial = HistorialItemPresupuesto::whereIn('item_presupuesto_id', $itemIds)
            ->with('itemPresupuesto') // Trae la relación por si necesitas el código actual
            ->latest()
            ->get();

        $proveedores = Proveedor::select('id', 'categoria_id', 'tercero')->get();

        $payload = [
            'presto'      => $presto,
            'items'       => $allItems,
            'tipo'        => $tipo,
            'proveedores' => $proveedores,

            'margenItems' => $margenItems,
            'ventaProyecto' => $ventaProyecto,
            'costosProyecto' => $costosProyecto,
            'margenProyecto' => $margenProyecto,
            'margenBruto' => $margenBruto,

            'historial'   => $itemsHistorial
        ];

        return Excel::download(new CotExport($payload), $nom_proyecto.".xlsx");
    }


    /**
     * Elimina un proyecto de la base comercial
     *
     * @param int $user_id ID del proyecto a eliminar
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
     */
    /** Comerciales cuyos datos puede tocar el usuario: null = todos (administración); el ejecutivo, los de su comercial. */
    private function comercialesDelUsuario(): ?array
    {
        $user = Auth::user();
        if ((int) $user->rol === 1) {
            return null;
        }
        if ((int) $user->rol === 5) {
            return Asistente::where('asistente_id', $user->id)->pluck('comercial_id')->map(function ($id) { return (int) $id; })->all();
        }
        return [(int) $user->id];
    }

    private function esDelUsuario($idComercial): bool
    {
        $propios = $this->comercialesDelUsuario();
        return $propios === null || in_array((int) $idComercial, $propios, true);
    }

    public function delete_proyecto($user_id){
        // El parámetro es el id de la fila de base comercial: solo se borra si es del usuario (o de su comercial).
        $base = Base_comercial::find($user_id);
        abort_unless($base && $this->esDelUsuario($base->id_user), 403);
        $base->delete();
        return redirect()->back()->with('success', 'Proyecto eliminado exitosamente.');
    }

    /**
     * Elimina un registro específico de Helisa
     *
     * @param string $centro Centro del registro
     * @param string $num_doc Número de documento del registro
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
     */
    public function delete_registro($centro, $num_doc){
        // Antes borraba el documento de todos los participantes; ahora solo las filas del usuario (o de su comercial).
        $propios = $this->comercialesDelUsuario();
        Helisa::where('centro', $centro)->where('num_doc', $num_doc)
            ->when($propios !== null, function ($q) use ($propios) { $q->whereIn('comercial', $propios); })
            ->delete();
        return redirect()->back()->with('success', 'Registro eliminado exitosamente.');
    }

    /**
     * Carga un archivo de base comercial (FUNCIÓN DESCONTINUADA)
     *
     * @param Request $request Solicitud HTTP con el archivo
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de error
     */
    public function upload_base (Request $request){
        // Función descontinuada: la base comercial se registra desde la gestión comercial
        return redirect()->route('dashboard-base')->with('error', '¡Ésta función fué descontinuada.!');
    }

    /**
     * Exporta la base comercial del usuario actual a Excel
     *
     * @param int $user_id ID del usuario (no utilizado actualmente)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Descarga del archivo Excel
     */
    public function export_base($user_id){
        // Si el usuario es asistente (rol 5), obtener el nombre del comercial asociado
        if (Auth::user()->rol == 5){
            $name = Asistente::where('asistente_id', Auth::user()->id)->first();
            if (!$name || !$name->comercial) {
                return redirect()->back()->with('error', 'No tienes un comercial asignado.');
            }
            return Excel::download(new BaseExport(), $name->comercial->name." Base.xlsx");
        }

        // Para otros roles, usar el nombre del usuario autenticado
        $name = Auth::user()->name;
        return Excel::download(new BaseExport(), $name." Base.xlsx");
    }

    /**
     * Elimina un contacto del sistema
     *
     * Verifica que el contacto no esté asociado a ninguna gestión comercial
     * antes de proceder con la eliminación
     *
     * @param int $id ID del contacto a eliminar
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito o error
     */
    public function delete_contacto($id){
        $contacto = Contacto::find($id);
        abort_unless($contacto && $this->esDelUsuario($contacto->id_user), 403);

        // Verificar si el contacto está vinculado a alguna gestión comercial
        $gestionComercial = GestionComercial::where('id_contacto', $id)->first();

        if ($gestionComercial){
            return redirect()->back()->withErrors(['Éste usuario esta enlazado con una de tus gestiones comerciales.']);
        }

        // Si no está vinculado, proceder con la eliminación
        Contacto::destroy($id);
        return redirect()->back()->with('success', 'Contacto eliminado exitosamente.');
    }

    /**
     * Actualiza la información de un contacto existente
     *
     * @param int $id ID del contacto a actualizar
     * @param Request $request Datos del formulario de actualización
     * @return \Illuminate\Http\RedirectResponse Redirección con mensaje de éxito
     */
    public function update_contacto($id, Request $request){
        // Validar los datos del formulario
        $request->validate([
            "cargo_edit" => 'required|string',
            "celular_edit" => 'required|numeric',
            "correo_edit" => 'required|email',
            "pbx_edit" => 'required|string',
            "web_edit" => 'required|string',
            "direccion_edit" => 'required|string'
        ]);

        // Buscar y actualizar el contacto (solo si es del usuario o de su comercial)
        $contacto = Contacto::find($id);
        abort_unless($contacto && $this->esDelUsuario($contacto->id_user), 403);
        $contacto->cargo = $request->cargo_edit;
        $contacto->celular = $request->celular_edit;
        $contacto->correo = $request->correo_edit;
        $contacto->web = $request->web_edit;
        $contacto->pbx = $request->pbx_edit;
        $contacto->direccion = $request->direccion_edit;
        $contacto->update();

        return redirect()->back()->with('success', 'Contacto actualizado exitosamente!');
    }
}
