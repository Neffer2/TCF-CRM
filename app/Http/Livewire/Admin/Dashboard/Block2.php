<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Base_comercial;
use App\Models\Helisa;
use App\Models\Mes;
use App\Models\Año;
use App\Models\Presupuesto;

class Block2 extends Component
{
    protected $listeners = ['Block2' => 'getData'];

    // Models
    public $xfacturar = 0;
    public $ventaejecucion = 0;
    public $venta = 0;
    public $venta_facturada = 0;
    public $ventatotal = 0;
    public $presto_mensual = 0;

    /* Sumatorio de ventas */
    public $sum_1 = 0;
    public $sum_2 = 0;
    public $sum_3 = 0;

    /* % ESTADO POR FACTURAR + VENTA FACTURADA */
    public $per_1 = 0;
    public $per_2 = 0;
    public $per_3 = 0;

    public function render()
    {
        return view('livewire.admin.dashboard.block2');
    }

    public function mount(){
        $this->default();
    }

    public function default(){ 
        // Obtiene el último año cargado
        $latest_year = Año::select('id','description')->orderBy('created_at', 'DESC')->first();
        $this->getData(['año' => $latest_year->description, 'mes' => null, 'comercial' => null]);
    }

    /* 
        Obtiene datos cuando sólo hay año en los filtros 
        @Params int
    */

    public function getData($filters = null){
        if ($filters == null){
            return $this->default();
        }

        $mes = $this->getMes($filters['mes']); 
        $año = $this->getAño($filters['año']);

        $this->xfacturar = $this->getXfacturar($filters['comercial'], $mes, $año);
        $this->ventaejecucion = $this->getVentaEjecucion($filters['comercial'], $mes, $año);
        $this->venta = $this->getVenta($filters['comercial'], $mes, $año);
        $this->ventatotal = $this->getVentaTotal($filters['comercial'], $mes, $año);
        /* Sumatorio ventas */
        $this->getSumVentas($filters['comercial'], $mes, $año);
        /* % ESTADO POR FACTURAR + VENTA FACTURADA */
        $this->getPers($filters['comercial'], $mes, $año);
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
            array_push($date_filters_array, [$año->description."-01-01", $año->description."-12-31"]);
        }

        $Base_results = Base_comercial::select('valor_proyecto')->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)
                        ->sum('valor_proyecto');

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
            array_push($date_filters_array, [$año->description."-01-01", $año->description."-12-31"]);
        }

        $Base_results = Base_comercial::select('valor_proyecto')->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)
                        ->sum('valor_proyecto');

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
            array_push($date_filters_array, [$año->description."-01-01", $año->description."-12-31"]);
        }

        $Base_results = Base_comercial::select('valor_proyecto')->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)
                        ->sum('valor_proyecto');

        return $Base_results;
    }

    /* Obtiene y filtra las ventas facturadas, para calcular la venta total */
    public function getVentaTotal($comercial_id, $mes, $año) {
        /* Creo 2 arreglos que contendrán los filtros necesarios para la consulta */
        $filters_array = [];
        $date_filters_array = [];

        if ($año){
            array_push($filters_array, ['año', $año->description]);
        }

        if ($mes){
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            array_push($date_filters_array, ['2009-01-01', '2029-01-01']);
        }

        if ($comercial_id){
            array_push($filters_array, ['comercial', $comercial_id]);
        }
        
        $this->venta_facturada = 0;
        $helisa_results = Helisa::select('base_factura')
                    ->where($filters_array)
                    ->whereBetween('fecha', $date_filters_array)
                    ->sum('base_factura');

        $this->venta_facturada = $helisa_results;
        
        return ($this->venta_facturada + $this->xfacturar +$this->ventaejecucion);
    }

    public function getSumVentas($comercial_id, $mes, $año) {
        $this->sum_1 = $this->venta_facturada + $this->xfacturar;
        $this->sum_2 = $this->venta_facturada + $this->xfacturar +$this->ventaejecucion;
        $this->sum_3 = $this->sum_2 + $this->venta;
    }

    
    public function getPers($comercial_id, $mes, $año) {
        $filters_array = [];

        if ($año){
            array_push($filters_array, ['ano_id', $año->id]);
        }

        if ($mes){
            array_push($filters_array, ['mes_id', $mes->id]);
        } 

        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        $presupuesto = Presupuesto::select('id', 'valor')
                                    ->where($filters_array)
                                    ->sum('valor');

        /* Evita division por 0 */
        if ($presupuesto > 0) {
            $this->per_1 = ($this->sum_1/$presupuesto) * 100;
            $this->per_2 = ($this->sum_2/$presupuesto) * 100;
            $this->per_3 = ($this->sum_3/$presupuesto) * 100;
        }else {
            $this->per_1 = 0;
            $this->per_2 = 0;
            $this->per_3 = 0;
        }
    }
}
