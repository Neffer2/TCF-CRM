<?php

namespace App\Http\Livewire\Admin\EstadosFacturacion;

use Livewire\Component;
use App\dels\Base_comercial;
use App\Models\Helisa;
use App\Models\Mes;
use App\Models\Año;
use App\Models\Presupuesto;
use Illuminate\Support\Facades\DB;

class EstadosFacturacion extends Component
{   
    protected $listeners = ['Block1' => 'getData'];
    //Models 
    public $xfacturar = []; 
    public $ejecucion = [];
    public $venta = [];
 
    public function render()
    {
        return view('livewire.admin.estados-facturacion.estados-facturacion');
    }

    public function mount($año = null, $mes = null, $comercial = null){
        if (!($año == null && $mes == null && $comercial == null)){
            $this->getData(['año' => $año, 'mes' => $mes, 'comercial' => $comercial]);
        }else {
            return $this->default();
        }
    }

    public function default(){ 
        // Obtiene el último año cargado
        $latest_year = Año::select('id','description')->orderBy('created_at', 'DESC')->first();
        if ($latest_year){
            $this->getData(['año' => $latest_year->description, 'mes' => null, 'comercial' => null]);
        }
    }

    public function getData($filters){
        if ($filters == null){
            return $this->default();
        }

        $mes = $this->getMes($filters['mes']); 
        $año = $this->getAño($filters['año']);

        $this->xfacturar = $this->getXfacturar($filters['comercial'], $mes, $año);
        $this->ejecucion = $this->getVentaEjecucion($filters['comercial'], $mes, $año);
        $this->venta = $this->getVenta($filters['comercial'], $mes, $año);
    }

    /* Trae datos almacenados del mes */
    public function getMes ($mes){
        $mes = Mes::select('id', 'description', 'identifier', 'f_inicio', 'f_fin')->where('id', $mes)->first();
        return $mes;
    }

    /* Trae datos almacenados del año */
    public function getAño ($año){
        $año = Año::select('id', 'description')->where('description', $año)->first();
        return $año;
    }

    /* Obtiene la sumatoria de ejecucionxfacturar
        @params int, obj, obj
    */
    public function getXfacturar($comercial_id, $mes, $año){
        $this->xfacturar = 0;
        // Arreglos que manejan los filtros
        $filters_array = [];
        $date_filters_array = [];

        // Estado de EJECUCIONXFACTURAR = 3
        array_push($filters_array, ['id_estado', 3]);

        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        /* Si no hay mes, trae todos los meses del año actual*/
        if ($mes){
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }
        // SELECT nom_cliente, SUM(valor_proyecto) FROM `base_comerciales` WHERE id_estado = 3 GROUP BY nom_cliente;

        $Base_results = DB::table('base_comerciales')
                        ->select(DB::raw("nom_cliente, SUM(valor_proyecto) as valor"))
                        ->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)->orderBy('fecha')
                        ->groupBy('nom_cliente')
                        ->get();

        return $Base_results;
    }

    /* Obtiene la sumatoria de ventas ejecucion
        @params int, obj, obj
    */
    public function getVentaEjecucion($comercial_id, $mes, $año){
        $this->ventaejecucion = 0;
        // Arreglos que manejan los filtros
        $filters_array = [];
        $date_filters_array = [];

        // Estado de VENTAEJECUCION = 7
        array_push($filters_array, ['id_estado', 7]);

        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        if ($mes){
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        $Base_results = DB::table('base_comerciales')
                            ->select(DB::raw("nom_cliente, SUM(valor_proyecto) as valor"))
                            ->where($filters_array)
                            ->whereBetween('fecha', $date_filters_array)->orderBy('fecha')
                            ->groupBy('nom_cliente')
                            ->get();

        return $Base_results;
    }

    /* Obtiene la sumatoria de ventas ejecucion
        @params int, obj, obj
    */
    public function getVenta($comercial_id, $mes, $año){
        $this->venta = 0;
        // Arreglos que manejan los filtros
        $filters_array = [];
        $date_filters_array = [];

        // Estado de VENTA = 6
        array_push($filters_array, ['id_estado', 6]);

        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        if ($mes){
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        $Base_results = DB::table('base_comerciales')
                            ->select(DB::raw("nom_cliente, SUM(valor_proyecto) as valor"))
                            ->where($filters_array)
                            ->whereBetween('fecha', $date_filters_array)->orderBy('fecha')
                            ->groupBy('nom_cliente')
                            ->get();

        return $Base_results;
    }
}
