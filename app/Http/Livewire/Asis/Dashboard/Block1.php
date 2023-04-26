<?php

namespace App\Http\Livewire\Asis\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Asistente;
use App\Models\Mes;
use App\Models\Año;
use App\Models\Helisa;
use App\Models\Cuenta;
use App\Models\Presupuesto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Block1 extends Component
{   
    protected $listeners = ['Block1' => 'getData'];

    // Models
    public $venta_facturada = 0; 
    public $venta_consolidada = 0;
    public $presto_mensual = 0; 
    public $presto_acumulado = 0;

    public $cumpli_venta_men = 0;
    public $cumpli_acum_venta_men = 0;
    public $presto_x_cumplir = 0; 

    // Useful vars
    public $latest_year;
    public $latest_month;
    public $comercial_id;

    public function render()
    {
        return view('livewire.asis.dashboard.block1');
    }

    public function mount(){
        $this->comercial_id = Asistente::where('asistente_id', Auth::user()->id)->first()->comercial_id;
        $this->default();
    } 

    public function default(){
        // Obtiene el último año cargado
        $this->latest_year = Año::select('id', 'description')->orderBy('created_at', 'DESC')->first();
        $this->latest_month = $this->getCurrentMes($this->latest_year);
        $cuenta = Cuenta::select('id', 'description')->where('id', 1)->first();
        if ($this->latest_year){
            $this->getData(['año' => $this->latest_year->description, 'mes' => null, 'comercial' => $this->comercial_id, 'cuenta' => $cuenta->id]);
        } 
    }

    public function getData($filters = null) {
        if ($filters == null){
            return $this->default();
        }

        $mes = $this->getMes($filters['mes']); 
        $año = $this->getAño($filters['año']);

        $this->getVentaFacturada($año->description, $mes, $filters['comercial'], $filters['cuenta']);
        $this->getVentaConsolidada($año->id, $año->description, $mes, $filters['comercial'], $filters['cuenta']);
        $this->getPresupuesto($mes, $filters['comercial'], $año->id);
        $this->getPresupuestoAcumulado($año->id, $mes, $filters['comercial']);

        $this->updateCumpli_venta_men();
        $this->updateCumpli_acum_venta_men();
        $this->updateCumpli_acum_venta_men();
        $this->updatePresto_x_cumplir();
    }

    /* Trae datos almacenados del mes */
    public function getCurrentMes ($latest_year){
        $mes = Mes::select('id', 'description', 'identifier', 'f_inicio', 'f_fin')->where('identifier', number_format(date("m")))->where('ano_id', $latest_year->id)->first();
        return $mes;
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

    /* Obtiene y filtra las ventas facturadas */
    public function getVentaFacturada($año, $mes, $comercial, $cuenta) {
        /* Creo 2 arreglos que contendrán los filtros necesarios para la consulta */
        $filters_array = [];
        $date_filters_array = [];

        if ($año){
            array_push($filters_array, ['año', $año]);
        }

        if ($mes){
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            array_push($date_filters_array, ['2009-01-01', '2029-01-01']);
        }

        if ($comercial){
            array_push($filters_array, ['comercial', $comercial]);
        }

        if ($cuenta){
            array_push($filters_array, ['id_cuenta', $cuenta]);
        }
    
        $helisa_results = Helisa::select('id', 'concepto', 'base_factura')
                    ->where($filters_array)
                    ->whereBetween('fecha', $date_filters_array)
                    ->get();

        $this->venta_facturada = 0;
        foreach ($helisa_results as $helisa_result){
            $this->venta_facturada += $helisa_result->base_factura;
        }
    }

    public function getVentaConsolidada($año_id, $año_desc, $mes, $comercial, $cuenta) {
        $first_month = Mes::select('id', 'description', 'f_inicio')->where([['identifier', 1], ['ano_id', $año_id]])->first();
        $last_month = Mes::select('id', 'description', 'f_fin')->where([['identifier', 12], ['ano_id', $año_id]])->first();

        $filters_array = [];

        if ($año_desc){
            array_push($filters_array, ['año', $año_desc]);
        }

        if ($comercial){
            array_push($filters_array, ['comercial', $comercial]);
        }

        if ($cuenta){
            array_push($filters_array, ['id_cuenta', $cuenta]);
        }
        
        // Si no hay mes no hay venta consolidada, hace la sumatoria de todos los meses
        if ($mes){
            $helisa_results = Helisa::select('id', 'concepto', 'base_factura')
                        ->where($filters_array)
                        ->whereBetween('fecha', [$first_month->f_inicio, $mes->f_fin])
                        ->get();
        }else {
            $helisa_results = Helisa::select('id', 'concepto', 'base_factura')
                        ->where($filters_array)
                        ->whereBetween('fecha', [$first_month->f_inicio, $last_month->f_fin])
                        ->get();
        }

        $this->venta_consolidada = 0;
        foreach ($helisa_results as $helisa_result){
            $this->venta_consolidada += $helisa_result->base_factura;
        }

    }

    public function getPresupuesto($mes, $comercial, $año) {
        $filters_array = [];

        if ($año){
            array_push($filters_array, ['ano_id', $año]);
        }

        if ($comercial){
            array_push($filters_array, ['id_user', $comercial]);
        }

        if ($mes){
            array_push($filters_array, ['mes_id', $mes->id]);
        }    
        
        // Si el mes es nulo pero el comercual no, muestra el presupuyesto mensual acumlado a la fecha
        if (is_null($mes) && $comercial != ""){
            $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.ano_id = $año AND presupuestos.id_user = $comercial AND presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND '".$this->latest_month->identifier."' AND id_user = $comercial"));            
            // Si el mes y comercial son nulos, presupuesto es cero
        }else {
            $presupuestos = Presupuesto::select('id', 'valor')
                                ->where($filters_array)
                                ->get();
        }
        
        $this->presto_mensual = 0;
        foreach ($presupuestos as $presupuesto){
            $this->presto_mensual += $presupuesto->valor;
        }                                   
    }

    public function getPresupuestoAcumulado ($año_id, $mes, $comercial){
        // Si no hay mes, hace el conteo de todos los meses
        if ($mes) {
            if ($comercial){
                $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.ano_id = $año_id AND presupuestos.id_user = $comercial AND presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND $mes->identifier"));
            }else {
                $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.ano_id = $año_id AND presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND $mes->identifier"));
            }
        }else { 
            if ($comercial){
                $presupuestos = DB::select(DB::raw("SELECT valor FROM presupuestos WHERE id_user = $comercial AND ano_id = $año_id"));
            }else {
                $presupuestos = DB::select(DB::raw("SELECT valor FROM presupuestos WHERE ano_id = $año_id"));
            }
        }

        // dd($presupuestos);
        $this->presto_acumulado = 0;
        foreach ($presupuestos as $value) {
            $this->presto_acumulado += $value->valor;
        }
    } 

    public function updateCumpli_venta_men (){
        if ($this->presto_mensual > 0){
            $this->cumpli_venta_men = ($this->venta_facturada/$this->presto_mensual)*100;
        }else{
            $this->cumpli_venta_men = 0;
        }
    } 

    public function updateCumpli_acum_venta_men (){
        if ($this->presto_acumulado > 0){
            $this->cumpli_acum_venta_men = ($this->venta_consolidada/$this->presto_acumulado)*100;
        }else {
            $this->cumpli_acum_venta_men = 0;
        }
    }

    public function updatePresto_x_cumplir (){
        $this->presto_x_cumplir = 0;
        if ($this->presto_acumulado){
            if (($this->cumpli_acum_venta_men - 100) > 100){
                $this->presto_x_cumplir = 100;
            }
            else {
                $this->presto_x_cumplir = ($this->cumpli_acum_venta_men - 100);
            }
        }
    }
}

