<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Mes;
use App\Models\Año;
use App\Models\Helisa;
use App\Models\Presupuesto;
use Illuminate\Support\Facades\DB;

class Block1 extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Block1
    |--------------------------------------------------------------------------
    | This livewire component is reponsible for managing the actions of the admin/dashboard/block1 view.
    */

    // Define los eventos que este componente puede escuchar
    protected $listeners = ['Block1' => 'getData'];

    // Variables públicas para almacenar datos de ventas y presupuestos
    public $venta_facturada = 0;       // Total de ventas facturadas en el período
    public $venta_consolidada = 0;     // Total de ventas consolidadas acumuladas
    public $presto_mensual = 0;        // Presupuesto mensual
    public $presto_acumulado = 0;      // Presupuesto acumulado hasta la fecha

    // Variables para calcular porcentajes de cumplimiento
    public $cumpli_venta_men = 0;      // Porcentaje de cumplimiento de venta mensual
    public $cumpli_acum_venta_men = 0; // Porcentaje de cumplimiento acumulado
    public $presto_x_cumplir = 0;      // Presupuesto por cumplir

    // Variables auxiliares para almacenar año y mes más recientes
    public $latest_year;    // Último año cargado en el sistema
    public $latest_month;   // Último mes del año actual

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.dashboard.block1');
    }

    /**
     * Método que se ejecuta al montar el componente
     * Inicializa los datos por defecto
     */
    public function mount(){
        $this->default();
    }

    /**
     * Carga los datos iniciales del dashboard
     * Obtiene el último año y mes cargados en el sistema
     */
    public function default(){
        // Obtiene el último año cargado ordenado por fecha de creación
        $this->latest_year = Año::select('id','description')->orderBy('created_at', 'DESC')->first();

        // Obtiene el mes actual del último año
        $this->latest_month = $this->getCurrentMes($this->latest_year);

        // Si existe un año, carga los datos sin filtros específicos
        if ($this->latest_year){
            $this->getData(['año' => $this->latest_year->description, 'mes' => null, 'comercial' => null]);
        }
    }

    /**
     * Método principal para obtener y procesar todos los datos del dashboard
     *
     * @param array|null $filters Array con filtros: año, mes, comercial
     */
    public function getData($filters = null) {
        // Si no hay filtros, usa los valores por defecto
        if ($filters == null){
            return $this->default();
        }

        // Obtiene los objetos de mes y año basados en los filtros
        $mes = $this->getMes($filters['mes']);
        $año = $this->getAño($filters['año']);

        // Ejecuta todos los métodos para calcular las métricas del dashboard
        $this->getVentaFacturada($año->description, $mes, $filters['comercial'], $año->id);
        $this->getVentaConsolidada($año->id, $año->description, $mes, $filters['comercial']);
        $this->getPresupuesto($mes, $filters['comercial'], $año->id);
        $this->getPresupuestoAcumulado($año->id, $mes, $filters['comercial']);

        // Actualiza los porcentajes de cumplimiento
        $this->updateCumpli_venta_men();
        $this->updateCumpli_acum_venta_men();
        $this->updateCumpli_acum_venta_men(); // Nota: Esta línea parece duplicada
        $this->updatePresto_x_cumplir();
    }

    /**
     * Obtiene el mes actual basado en el número de mes del sistema
     *
     * @param object $latest_year Objeto del último año
     * @return object Objeto del mes actual
     */
    public function getCurrentMes ($latest_year){
        $mes = Mes::select('id', 'description', 'identifier', 'f_inicio', 'f_fin')
                  ->where('identifier', number_format(date("m")))
                  ->where('ano_id', $latest_year->id)
                  ->first();
        return $mes;
    }

    /**
     * Obtiene los datos de un mes específico por su ID
     *
     * @param int $mes ID del mes
     * @return object Objeto del mes solicitado
     */
    public function getMes ($mes){
        $mes = Mes::select('id', 'description', 'identifier', 'f_inicio', 'f_fin')
                  ->where('id', $mes)
                  ->first();
        return $mes;
    }

    /**
     * Obtiene los datos de un año específico por su descripción
     *
     * @param string $año Descripción del año (ej: "2024")
     * @return object Objeto del año solicitado
     */
    public function getAño ($año){
        $año = Año::select('id', 'description')
                  ->where('description', $año)
                  ->first();
        return $año;
    }

    /**
     * Calcula el total de ventas facturadas según los filtros aplicados
     *
     * @param string $año Año a filtrar
     * @param object|null $mes Objeto del mes a filtrar (null para todos los meses)
     * @param int|null $comercial ID del comercial a filtrar
     * @param int|null $año_id ID del año
     */
    public function getVentaFacturada($año, $mes, $comercial, $año_id = null) {
        // Crea arrays para almacenar los filtros de la consulta
        $filters_array = [];
        $date_filters_array = [];

        // Agrega filtro por año si existe
        if ($año){
            array_push($filters_array, ['año', $año]);
        }

        // Configura el rango de fechas según si hay mes específico o no
        if ($mes){
            // Si hay mes específico, usa su rango de fechas
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, usa desde enero hasta el mes actual
            $primerMes = Mes::select('id', 'description', 'f_inicio', 'f_fin')
                            ->where('ano_id', $año_id)
                            ->where('identifier', 1)
                            ->first();
            array_push($date_filters_array, [$primerMes->f_inicio, $this->latest_month->f_fin]);
        }

        // Agrega filtro por comercial si existe
        if ($comercial){
            array_push($filters_array, ['comercial', $comercial]);
        }

        // Ejecuta la consulta con todos los filtros aplicados
        $helisa_results = Helisa::select('id', 'concepto', 'base_factura')
                    ->where($filters_array)
                    ->whereBetween('fecha', $date_filters_array)
                    ->get();

        // Suma todas las bases de factura obtenidas
        $this->venta_facturada = 0;
        foreach ($helisa_results as $helisa_result){
            $this->venta_facturada += $helisa_result->base_factura;
        }
    }

    /**
     * Calcula el total de ventas consolidadas (acumuladas) según los filtros
     *
     * @param int $año_id ID del año
     * @param string $año_desc Descripción del año
     * @param object|null $mes Objeto del mes (null para todo el año)
     * @param int|null $comercial ID del comercial a filtrar
     */
    public function getVentaConsolidada($año_id, $año_desc, $mes, $comercial) {
        // Obtiene el primer y último mes del año para establecer rangos
        $first_month = Mes::select('id', 'description', 'f_inicio')
                          ->where([['identifier', 1], ['ano_id', $año_id]])
                          ->first();
        $last_month = Mes::select('id', 'description', 'f_fin')
                         ->where([['identifier', 12], ['ano_id', $año_id]])
                         ->first();

        $filters_array = [];

        // Agrega filtro por año si existe
        if ($año_desc){
            array_push($filters_array, ['año', $año_desc]);
        }

        // Agrega filtro por comercial si existe
        if ($comercial){
            array_push($filters_array, ['comercial', $comercial]);
        }

        // Configura el rango de fechas según si hay mes específico
        if ($mes){
            // Si hay mes específico, consolida desde enero hasta ese mes
            $helisa_results = Helisa::select('id', 'concepto', 'base_factura')
                        ->where($filters_array)
                        ->whereBetween('fecha', [$first_month->f_inicio, $mes->f_fin])
                        ->get();
        }else {
            // Si no hay mes, consolida todo el año
            $helisa_results = Helisa::select('id', 'concepto', 'base_factura')
                        ->where($filters_array)
                        ->whereBetween('fecha', [$first_month->f_inicio, $last_month->f_fin])
                        ->get();
        }

        // Suma todas las bases de factura para obtener venta consolidada
        $this->venta_consolidada = 0;
        foreach ($helisa_results as $helisa_result){
            $this->venta_consolidada += $helisa_result->base_factura;
        }
    }

    /**
     * Obtiene el presupuesto mensual según los filtros aplicados
     *
     * @param object|null $mes Objeto del mes (null para acumulado hasta fecha actual)
     * @param int|null $comercial ID del comercial a filtrar
     * @param int $año ID del año
     */
    public function getPresupuesto($mes, $comercial, $año) {
        $filters_array = [];

        // Agrega filtro por año
        if ($año){
            array_push($filters_array, ['ano_id', $año]);
        }

        // Agrega filtro por comercial si existe
        if ($comercial){
            array_push($filters_array, ['id_user', $comercial]);
        }

        // Agrega filtro por mes si existe
        if ($mes){
            array_push($filters_array, ['mes_id', $mes->id]);
        }

        // Si el mes es nulo pero hay comercial, muestra presupuesto acumulado hasta la fecha
        if (is_null($mes) && $comercial != ""){
            $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.ano_id = $año AND presupuestos.id_user = $comercial AND presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND '".$this->latest_month->identifier."'"));
        }else {
            // Para casos normales, usa el query builder de Eloquent
            $presupuestos = Presupuesto::select('id', 'valor')
                                ->where($filters_array)
                                ->get();
        }

        // Suma todos los valores de presupuesto obtenidos
        $this->presto_mensual = 0;
        foreach ($presupuestos as $presupuesto){
            $this->presto_mensual += $presupuesto->valor;
        }
    }

    /**
     * Calcula el presupuesto acumulado desde enero hasta el mes especificado
     *
     * @param int $año_id ID del año
     * @param object|null $mes Objeto del mes (null para todo el año)
     * @param int|null $comercial ID del comercial a filtrar
     * @param mixed $general Parámetro adicional (no utilizado actualmente)
     */
    public function getPresupuestoAcumulado ($año_id, $mes, $comercial, $general = null){
        // Si hay mes específico, acumula desde enero hasta ese mes
        if ($mes) {
            if ($comercial){
                // Con filtro de comercial específico
                $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.ano_id = $año_id AND presupuestos.id_user = $comercial AND presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND $mes->identifier"));
            }else {
                // Sin filtro de comercial (todos los comerciales)
                $presupuestos = DB::select(DB::raw("SELECT valor, description FROM presupuestos, meses WHERE presupuestos.ano_id = $año_id AND presupuestos.mes_id = meses.id AND meses.identifier BETWEEN 1 AND $mes->identifier"));
            }
        }else {
            // Si no hay mes, toma todo el año
            if ($comercial){
                // Con filtro de comercial específico
                $presupuestos = DB::select(DB::raw("SELECT valor FROM presupuestos WHERE id_user = $comercial AND ano_id = $año_id"));
            }else {
                // Sin filtro de comercial (todos los comerciales)
                $presupuestos = DB::select(DB::raw("SELECT valor FROM presupuestos WHERE ano_id = $año_id"));
            }
        }

        // Suma todos los valores de presupuesto acumulados
        $this->presto_acumulado = 0;
        foreach ($presupuestos as $value) {
            $this->presto_acumulado += $value->valor;
        }
    }

    /**
     * Calcula el porcentaje de cumplimiento de venta mensual
     * Compara la venta facturada vs el presupuesto mensual
     */
    public function updateCumpli_venta_men (){
        if ($this->presto_mensual > 0){
            // Calcula el porcentaje: (venta facturada / presupuesto mensual) * 100
            $this->cumpli_venta_men = ($this->venta_facturada/$this->presto_mensual)*100;
        }else{
            // Si no hay presupuesto mensual, el cumplimiento es 0%
            $this->cumpli_venta_men = 0;
        }
    }

    /**
     * Calcula el porcentaje de cumplimiento de venta acumulada
     * Compara la venta consolidada vs el presupuesto acumulado
     */
    public function updateCumpli_acum_venta_men (){
        if ($this->presto_acumulado > 0){
            // Calcula el porcentaje: (venta consolidada / presupuesto acumulado) * 100
            $this->cumpli_acum_venta_men = ($this->venta_consolidada/$this->presto_acumulado)*100;
        }else {
            // Si no hay presupuesto acumulado, el cumplimiento es 0%
            $this->cumpli_acum_venta_men = 0;
        }
    }

    /**
     * Calcula el porcentaje de presupuesto por cumplir
     * Basado en el cumplimiento acumulado, determina cuánto falta o sobra
     */
    public function updatePresto_x_cumplir (){
        $this->presto_x_cumplir = 0;
        if ($this->presto_acumulado){
            // Si el cumplimiento excede el 200%, se limita al 100%
            if (($this->cumpli_acum_venta_men - 100) > 100){
                $this->presto_x_cumplir = 100;
            }
            else {
                // Calcula cuánto falta para cumplir el 100% (puede ser negativo si ya se cumplió)
                $this->presto_x_cumplir = ($this->cumpli_acum_venta_men - 100);
            }
        }
    }
}
