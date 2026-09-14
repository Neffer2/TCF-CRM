<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\PresupuestoProyecto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Dashboard del rol CONTROLLER (rol 11): lo que está pendiente de revisión
 * financiera y lo último aprobado. Las pantallas de detalle son las mismas
 * de administración (presupuestos, actualizaciones, consumidos, reportes),
 * acotadas por el menú y las rutas rol:1,11.
 */
class ControlController extends Controller
{
    public function index()
    {
        $inicioMes = Carbon::now()->startOfMonth()->toDateString();

        $resumen = [
            'validacion_lider'    => PresupuestoProyecto::where('estado_id', 4)->count(),
            'validacion_gerencia' => PresupuestoProyecto::where('estado_id', 5)->count(),
            'cambios'             => PresupuestoProyecto::where('notificacion_actualizacion', 1)->count(),
            'aprobados_mes'       => PresupuestoProyecto::where('estado_id', 1)->whereDate('fecha_cc', '>=', $inicioMes)->count(),
            'oc_revision'         => OrdenCompra::where('estado_id', 2)->count(),
            'oc_gerencia'         => Auth::user()->can('validar-nomina') ? OrdenCompra::where('estado_id', 9)->count() : null,
        ];

        // Presupuestos aprobados a los que el comercial les hizo cambios después
        $cambios = PresupuestoProyecto::with(['gestion.comercial:id,name', 'estado'])
            ->where('notificacion_actualizacion', 1)
            ->orderByDesc('updated_at')->limit(8)->get();

        // Pendientes de validación (líder comercial o gerencia)
        $pendientes = PresupuestoProyecto::with(['gestion.comercial:id,name', 'estado'])
            ->whereIn('estado_id', [4, 5])
            ->orderByDesc('updated_at')->limit(8)->get();

        // Últimos aprobados con centro de costos
        $aprobados = PresupuestoProyecto::with(['gestion.comercial:id,name'])
            ->where('estado_id', 1)->whereNotNull('cod_cc')
            ->orderByDesc('fecha_cc')->orderByDesc('id')->limit(8)->get();

        return view('controller.index', compact('resumen', 'cambios', 'pendientes', 'aprobados'));
    }
}
