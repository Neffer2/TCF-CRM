<?php

namespace App\Http\Controllers;

use App\Models\PresupuestoProyecto;
use Illuminate\Support\Facades\Auth;
use App\Models\Año;
use Illuminate\Http\Request;
use App\Models\OrdenCompra;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class ProductorController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | ProductorController
    |--------------------------------------------------------------------------
    | This controller is responsible for managing lider de productor actions and views.
    */

    public function index()
    {
        $ultimoAño = Año::orderByDesc('description')->first();

        $fechaInicio = $ultimoAño?->Meses()
            ->whereNotNull('f_inicio')
            ->orderBy('f_inicio')
            ->value('f_inicio');

        abort_if($fechaInicio === null, 500, 'No se configuró la fecha de inicio del último año.');

        $proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')
            ->with('gestion:id,nom_proyecto_cot')
            ->where('productor', Auth::id())
            ->where('fecha_cc', '>=', $fechaInicio)
            ->whereHas('baseComercial', function ($query) {
                $query->where(function ($estados) {
                    $estados->where('id_estado', '<>', 1)
                        ->orWhereNull('id_estado');
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('productor.index', ['proyectos' => $proyectos]);
    }

    public function showRemision($orden){
        return view('productor.remision.index', ['orden' => $orden]);
    }

    public function ShowAnticipo($anticipo_id = null) {
        return view('productor.anticipos.anticipo', ['anticipo_id' => $anticipo_id]);
    }

    public function showListaAnticipos($anticipo_id = null) {
        return view('productor.anticipos.index', compact('anticipo_id'));
    }

    public function showOCEmpleados()
    {
        return view('productor.ordenes.empleado');
    }

}
