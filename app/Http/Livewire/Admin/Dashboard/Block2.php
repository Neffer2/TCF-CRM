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
    /*
    |--------------------------------------------------------------------------
    | Block2
    |--------------------------------------------------------------------------
    | This livewire component is reponsible for managing the actions of the admin/dashboard/block2 view.
    */

    // Define los eventos que este componente puede escuchar
    protected $listeners = ['Block2' => 'getData'];

    // Variables públicas para almacenar diferentes tipos de ventas
    public $xfacturar = 0;          // Valor de proyectos en estado "ejecución por facturar"
    public $ventaejecucion = 0;     // Valor de proyectos en estado "venta en ejecución"
    public $venta = 0;              // Valor de proyectos en estado "venta"
    public $venta_facturada = 0;    // Total de ventas ya facturadas (desde Helisa)
    public $ventatotal = 0;         // Suma total de todas las ventas
    public $presto_mensual = 0;     // Presupuesto mensual para comparaciones

    // Variables para almacenar los filtros actuales
    public $año;        // Año seleccionado en los filtros
    public $mes;        // Mes seleccionado en los filtros
    public $comercial;  // Comercial seleccionado en los filtros

    // Variables para almacenar sumatorias de diferentes combinaciones de ventas
    public $sum_1 = 0;  // Suma: venta_facturada + xfacturar
    public $sum_2 = 0;  // Suma: venta_facturada + xfacturar + ventaejecucion
    public $sum_3 = 0;  // Suma: sum_2 + venta

    // Variables para almacenar porcentajes de cumplimiento vs presupuesto
    public $per_1 = 0;  // Porcentaje de sum_1 vs presupuesto
    public $per_2 = 0;  // Porcentaje de sum_2 vs presupuesto
    public $per_3 = 0;  // Porcentaje de sum_3 vs presupuesto

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.dashboard.block2');
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
     * Obtiene el último año cargado y ejecuta getData con valores por defecto
     */
    public function default(){
        // Obtiene el último año cargado ordenado por fecha de creación
        $latest_year = Año::select('id','description')->orderBy('created_at', 'DESC')->first();
        if ($latest_year){
            // Ejecuta getData con el último año y sin filtros de mes ni comercial
            $this->getData(['año' => $latest_year->description, 'mes' => null, 'comercial' => null]);
        }
    }

    /**
     * Método principal para obtener y procesar todos los datos del dashboard
     * Calcula diferentes tipos de ventas y sus porcentajes de cumplimiento
     *
     * @param array|null $filters Array con filtros: año, mes, comercial
     */
    public function getData($filters = null){
        // Si no hay filtros, usa los valores por defecto
        if ($filters == null){
            return $this->default();
        }

        // Almacena los filtros actuales para uso en la vista
        $this->año = $filters['año'];
        $this->mes = $filters['mes'];
        $this->comercial = $filters['comercial'];

        // Obtiene los objetos de mes y año basados en los filtros
        $mes = $this->getMes($filters['mes']);
        $año = $this->getAño($filters['año']);

        // Calcula cada tipo de venta según su estado en Base_comercial
        $this->xfacturar = $this->getXfacturar($filters['comercial'], $mes, $año);
        $this->ventaejecucion = $this->getVentaEjecucion($filters['comercial'], $mes, $año);
        $this->venta = $this->getVenta($filters['comercial'], $mes, $año);
        $this->ventatotal = $this->getVentaTotal($filters['comercial'], $mes, $año);

        // Calcula las sumatorias de diferentes combinaciones de ventas
        $this->getSumVentas($filters['comercial'], $mes, $año);

        // Calcula los porcentajes de cumplimiento vs presupuesto
        $this->getPers($filters['comercial'], $mes, $año);
    }

    /**
     * Obtiene los datos de un mes específico por su ID
     *
     * @param int $mes ID del mes
     * @return object Objeto del mes solicitado con fechas de inicio y fin
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
     * Obtiene la sumatoria de proyectos en estado "ejecución por facturar"
     * Estado ID = 3 en la tabla Base_comercial
     *
     * @param int|null $comercial_id ID del comercial a filtrar
     * @param object|null $mes Objeto del mes (null para todo el año)
     * @param object $año Objeto del año
     * @return float Suma total de valores de proyectos en este estado
     */
    public function getXfacturar($comercial_id, $mes, $año){
        $this->xfacturar = 0;
        // Arrays para manejar los filtros de la consulta
        $filters_array = [];
        $date_filters_array = [];

        // Estado de EJECUCIONXFACTURAR = 3
        array_push($filters_array, ['id_estado', 3]);

        // Agrega filtro por comercial si existe
        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        // Configura el rango de fechas según si hay mes específico
        if ($mes){
            // Si hay mes específico, usa su rango de fechas
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, usa todo el año (enero a diciembre)
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        // Ejecuta la consulta y suma los valores de proyectos
        $Base_results = Base_comercial::select('valor_proyecto')->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)
                        ->sum('valor_proyecto');

        return $Base_results;
    }

    /**
     * Obtiene la sumatoria de proyectos en estado "venta en ejecución"
     * Estado ID = 7 en la tabla Base_comercial
     *
     * @param int|null $comercial_id ID del comercial a filtrar
     * @param object|null $mes Objeto del mes (null para todo el año)
     * @param object $año Objeto del año
     * @return float Suma total de valores de proyectos en este estado
     */
    public function getVentaEjecucion($comercial_id, $mes, $año){
        $this->ventaejecucion = 0;
        // Arrays para manejar los filtros de la consulta
        $filters_array = [];
        $date_filters_array = [];

        // Estado de VENTAEJECUCION = 7
        array_push($filters_array, ['id_estado', 7]);

        // Agrega filtro por comercial si existe
        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        // Configura el rango de fechas según si hay mes específico
        if ($mes){
            // Si hay mes específico, usa su rango de fechas
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, usa todo el año (enero a diciembre)
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        // Ejecuta la consulta y suma los valores de proyectos
        $Base_results = Base_comercial::select('valor_proyecto')->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)
                        ->sum('valor_proyecto');

        return $Base_results;
    }

    /**
     * Obtiene la sumatoria de proyectos en estado "venta"
     * Estado ID = 6 en la tabla Base_comercial
     *
     * @param int|null $comercial_id ID del comercial a filtrar
     * @param object|null $mes Objeto del mes (null para todo el año)
     * @param object $año Objeto del año
     * @return float Suma total de valores de proyectos en este estado
     */
    public function getVenta($comercial_id, $mes, $año){
        $this->venta = 0;
        // Arrays para manejar los filtros de la consulta
        $filters_array = [];
        $date_filters_array = [];

        // Estado de VENTA = 6
        array_push($filters_array, ['id_estado', 6]);

        // Agrega filtro por comercial si existe
        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        // Configura el rango de fechas según si hay mes específico
        if ($mes){
            // Si hay mes específico, usa su rango de fechas
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, usa todo el año (enero a diciembre)
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        // Ejecuta la consulta y suma los valores de proyectos
        $Base_results = Base_comercial::select('valor_proyecto')->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)
                        ->sum('valor_proyecto');

        return $Base_results;
    }

    /**
     * Calcula la venta total combinando ventas facturadas y proyectos pendientes
     * Obtiene ventas facturadas de Helisa y las suma con proyectos en diferentes estados
     *
     * @param int|null $comercial_id ID del comercial a filtrar
     * @param object|null $mes Objeto del mes (null para todo el año)
     * @param object $año Objeto del año
     * @return float Suma total: venta_facturada + xfacturar + ventaejecucion
     */
    public function getVentaTotal($comercial_id, $mes, $año) {
        // Arrays para manejar los filtros de la consulta en Helisa
        $filters_array = [];
        $date_filters_array = [];

        // Agrega filtro por año para consulta en Helisa
        if ($año){
            array_push($filters_array, ['año', $año->description]);
        }

        // Configura el rango de fechas según si hay mes específico
        if ($mes){
            // Si hay mes específico, usa su rango de fechas
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, usa todo el año (enero a diciembre)
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        // Agrega filtro por comercial si existe
        if ($comercial_id){
            array_push($filters_array, ['comercial', $comercial_id]);
        }

        // Obtiene la suma de ventas facturadas desde Helisa
        $this->venta_facturada = 0;
        $helisa_results = Helisa::select('base_factura')
                    ->where($filters_array)
                    ->whereBetween('fecha', $date_filters_array)
                    ->sum('base_factura');

        $this->venta_facturada = $helisa_results;

        // Retorna la suma total: facturadas + por facturar + en ejecución
        return ($this->venta_facturada + $this->xfacturar +$this->ventaejecucion);
    }

    /**
     * Calcula las sumatorias de diferentes combinaciones de tipos de ventas
     * Estos valores se usan para mostrar progresión de ventas en el dashboard
     *
     * @param int|null $comercial_id ID del comercial (no utilizado en cálculos)
     * @param object|null $mes Objeto del mes (no utilizado en cálculos)
     * @param object $año Objeto del año (no utilizado en cálculos)
     */
    public function getSumVentas($comercial_id, $mes, $año) {
        // Suma 1: Ventas facturadas + proyectos por facturar
        $this->sum_1 = $this->venta_facturada + $this->xfacturar;

        // Suma 2: Suma 1 + proyectos en ejecución
        $this->sum_2 = $this->venta_facturada + $this->xfacturar +$this->ventaejecucion;

        // Suma 3: Suma 2 + ventas cerradas pero no facturadas
        $this->sum_3 = $this->sum_2 + $this->venta;
    }

    /**
     * Calcula los porcentajes de cumplimiento de cada sumatoria vs el presupuesto
     * Compara las diferentes combinaciones de ventas con el presupuesto establecido
     *
     * @param int|null $comercial_id ID del comercial a filtrar
     * @param object|null $mes Objeto del mes a filtrar
     * @param object $año Objeto del año a filtrar
     */
    public function getPers($comercial_id, $mes, $año) {
        $filters_array = [];

        // Agrega filtro por año
        if ($año){
            array_push($filters_array, ['ano_id', $año->id]);
        }

        // Agrega filtro por mes si existe
        if ($mes){
            array_push($filters_array, ['mes_id', $mes->id]);
        }

        // Agrega filtro por comercial si existe
        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        // Obtiene la suma total del presupuesto según los filtros
        $presupuesto = Presupuesto::select('id', 'valor')
                                    ->where($filters_array)
                                    ->sum('valor');

        // Calcula porcentajes evitando división por cero
        if ($presupuesto > 0) {
            // Porcentaje de cumplimiento: facturadas + por facturar
            $this->per_1 = ($this->sum_1/$presupuesto) * 100;

            // Porcentaje de cumplimiento: facturadas + por facturar + en ejecución
            $this->per_2 = ($this->sum_2/$presupuesto) * 100;

            // Porcentaje de cumplimiento: todas las ventas
            $this->per_3 = ($this->sum_3/$presupuesto) * 100;
        }else {
            // Si no hay presupuesto, todos los porcentajes son 0%
            $this->per_1 = 0;
            $this->per_2 = 0;
            $this->per_3 = 0;
        }
    }
}
