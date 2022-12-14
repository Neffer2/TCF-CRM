<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Mes;
use App\Models\Año;
use App\Models\Helisa;
use App\Models\Presupuesto;
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

    public function render()
    {
        return view('livewire.admin.dashboard.block1');
    } 

    public function getData($filters) {
        $mes = $this->getMes($filters['mes']);
        $año = $this->getAño($filters['año']);

        $this->getVentaFacturada($año->description, $mes, $filters['comercial']);
        $this->getVentaConsolidada($año->id, $año->description, $mes, $filters['comercial']);
        $this->getPresupuesto($mes, $filters['comercial']);
        $this->getPresupuestoAcumulado($año->id, $mes, $filters['comercial']);

        $this->updateCumpli_venta_men();
        $this->updateCumpli_acum_venta_men();
        $this->updateCumpli_acum_venta_men();
        $this->updatePresto_x_cumplir();
    }

    public function getMes ($mes){
        $mes = Mes::select('id', 'description', 'identifier', 'f_inicio', 'f_fin')->where('id', $mes)->first();
        return $mes;
    }

    public function getAño ($año){
        $año = Año::select('id', 'description')->where('description', $año)->first();
        return $año;
    }

    public function getVentaFacturada($año, $mes, $comercial) {
        $filters_array = [];
        $date_filters_array = [];

        if ($año){
            array_push($filters_array, ['año', $año]);
        }

        if ($mes){
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            array_push($date_filters_array, ['2009-01-01', '2025-01-01']);
        }

        if ($comercial){
            array_push($filters_array, ['comercial', $comercial]);
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

    public function getVentaConsolidada($año_id, $año_desc, $mes, $comercial) {
        $first_month = Mes::select('id', 'description', 'f_inicio')->where([['identifier', 1], ['ano_id', $año_id]])->first();

        $filters_array = [];

        if ($año_desc){
            array_push($filters_array, ['año', $año_desc]);
        }

        if ($comercial){
            array_push($filters_array, ['comercial', $comercial]);
        }
        
        // Si no hay mes no hay venta consolidada
        if ($mes){
            $helisa_results = Helisa::select('id', 'concepto', 'base_factura')
                        ->where($filters_array)
                        ->whereBetween('fecha', [$first_month->f_inicio, $mes->f_fin])
                        ->get();

            $this->venta_consolidada = 0;
            foreach ($helisa_results as $helisa_result){
                $this->venta_consolidada += $helisa_result->base_factura;
            }
        }

    }

    public function getPresupuesto($mes, $comercial) {
        $filters_array = [];

        if ($mes){
            array_push($filters_array, ['mes_id', $mes->id]);
        }

        if ($comercial){
            array_push($filters_array, ['id_user', $comercial]);
        }

        $presupuestos = Presupuesto::select('id', 'valor')
                                    ->where($filters_array)
                                    ->get();

        $this->presto_mensual = 0;
        foreach ($presupuestos as $presupuesto){
            $this->presto_mensual += $presupuesto->valor;
        }                                   
    }

    public function getPresupuestoAcumulado ($año_id, $mes, $comercial){
        // Si no hay mes, no hay presupuesto acumulado
        if ($mes) {
            if ($comercial){
                $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.id_user = $comercial AND presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND $mes->identifier"));
            }else {
                $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND $mes->identifier"));
            }
    
            $this->presto_acumulado = 0;
            foreach ($presupuestos as $value) {
                $this->presto_acumulado += $value->valor;
            }
        }else {
            $this->presto_acumulado = 0;
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
        if ($this->presto_acumulado){
            $this->presto_x_cumplir = $this->presto_acumulado - 100;
        }
    }
}
