<?php

namespace App\Http\Livewire\Admin\EstadosFacturacion;

use Livewire\Component;
use App\Models\Base_comercial;
use App\Models\Helisa;
use App\Models\Mes;
use App\Models\Año;
use App\Models\Presupuesto;
use Illuminate\Support\Facades\DB;

/**
 * Componente Livewire para mostrar los estados de facturación
 * Genera reportes de ventas agrupados por estado: por facturar, en ejecución y ventas
 * Los datos se agrupan por clientes y se pueden filtrar por año, mes y comercial
 */
class EstadosFacturacion extends Component
{
    // Listener para recibir datos desde otros componentes (Block1)
    protected $listeners = ['Block1' => 'getData'];

    // Arrays que almacenan los resultados de cada estado de facturación
    public $xfacturar = [];  // Registros en estado "Por Facturar" (estado 3)
    public $ejecucion = [];  // Registros en estado "Venta en Ejecución" (estado 7)
    public $venta = [];      // Registros en estado "Venta" (estado 6)

    /**
     * Renderiza la vista del componente
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.estados-facturacion.estados-facturacion');
    }

    /**
     * Método que se ejecuta al inicializar el componente
     * Puede recibir filtros opcionales desde la URL o componente padre
     * @param string|null $año Año para filtrar (descripción del año)
     * @param int|null $mes ID del mes para filtrar
     * @param int|null $comercial ID del comercial para filtrar
     */
    public function mount($año = null, $mes = null, $comercial = null){
        // Si se proporcionaron filtros, los aplica; sino, carga valores por defecto
        if (!($año == null && $mes == null && $comercial == null)){
            $this->getData(['año' => $año, 'mes' => $mes, 'comercial' => $comercial]);
        }else {
            return $this->default();
        }
    }

    /**
     * Carga los datos por defecto cuando no se especifican filtros
     * Utiliza el año más reciente disponible
     */
    public function default(){
        // Obtiene el último año cargado en la base de datos
        $latest_year = Año::select('id','description')->orderBy('created_at', 'DESC')->first();
        if ($latest_year){
            // Carga datos con el año más reciente, sin filtros de mes ni comercial
            $this->getData(['año' => $latest_year->description, 'mes' => null, 'comercial' => null]);
        }
    }

    /**
     * Método principal que obtiene y carga todos los datos según los filtros aplicados
     * @param array|null $filters Array con filtros: año, mes, comercial
     */
    public function getData($filters){
        // Si no hay filtros, carga valores por defecto
        if ($filters == null){
            return $this->default();
        }

        // Obtiene objetos completos del mes y año para usar en las consultas
        $mes = $this->getMes($filters['mes']);
        $año = $this->getAño($filters['año']);

        // Carga los datos para cada estado de facturación
        $this->xfacturar = $this->getXfacturar($filters['comercial'], $mes, $año);
        $this->ejecucion = $this->getVentaEjecucion($filters['comercial'], $mes, $año);
        $this->venta = $this->getVenta($filters['comercial'], $mes, $año);
    }

    /**
     * Obtiene la información completa de un mes específico
     * @param int $mes ID del mes
     * @return \App\Models\Mes|null Objeto del mes con fechas de inicio y fin
     */
    public function getMes ($mes){
        $mes = Mes::select('id', 'description', 'identifier', 'f_inicio', 'f_fin')->where('id', $mes)->first();
        return $mes;
    }

    /**
     * Obtiene la información completa de un año específico
     * @param string $año Descripción del año (ej: "2024")
     * @return \App\Models\Año|null Objeto del año
     */
    public function getAño ($año){
        $año = Año::select('id', 'description')->where('description', $año)->first();
        return $año;
    }

    /**
     * Obtiene la sumatoria de registros en estado "Por Facturar" (estado 3)
     * Agrupa los resultados por clientes y suma el valor de los proyectos
     * @param int|null $comercial_id ID del comercial para filtrar
     * @param \App\Models\Mes|null $mes Objeto del mes para filtrar fechas
     * @param \App\Models\Año $año Objeto del año para filtrar fechas
     * @return \Illuminate\Support\Collection Colección con clientes y valores agrupados
     */
    public function getXfacturar($comercial_id, $mes, $año){
        $this->xfacturar = 0;
        // Arrays para manejar los filtros de la consulta
        $filters_array = [];
        $date_filters_array = [];

        // Estado de EJECUCIONXFACTURAR = 3 (Por Facturar)
        array_push($filters_array, ['id_estado', 3]);

        // Filtro opcional por comercial específico
        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        // Filtro de fechas: si hay mes específico o todo el año
        if ($mes){
            // Si se especifica mes, filtra solo ese mes
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, trae todos los meses del año (enero a diciembre)
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        // Consulta SQL equivalente: SELECT nom_cliente, SUM(valor_proyecto) FROM base_comerciales WHERE id_estado = 3 GROUP BY nom_cliente
        $Base_results = DB::table('base_comerciales')
                        ->select(DB::raw('nom_cliente, SUM(valor_proyecto) as valor'))
                        ->where($filters_array)
                        ->whereBetween('fecha', $date_filters_array)
                        ->groupBy('nom_cliente')
                        ->get();

        return $Base_results;
    }

    /**
     * Obtiene la sumatoria de registros en estado "Venta en Ejecución" (estado 7)
     * Agrupa los resultados por clientes y suma el valor de los proyectos
     * @param int|null $comercial_id ID del comercial para filtrar
     * @param \App\Models\Mes|null $mes Objeto del mes para filtrar fechas
     * @param \App\Models\Año $año Objeto del año para filtrar fechas
     * @return \Illuminate\Support\Collection Colección con clientes y valores agrupados
     */
    public function getVentaEjecucion($comercial_id, $mes, $año){
        // Arrays para manejar los filtros de la consulta
        $filters_array = [];
        $date_filters_array = [];

        // Estado de VENTAEJECUCION = 7 (Venta en Ejecución)
        array_push($filters_array, ['id_estado', 7]);

        // Filtro opcional por comercial específico
        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        // Filtro de fechas: si hay mes específico o todo el año
        if ($mes){
            // Si se especifica mes, filtra solo ese mes
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, trae todos los meses del año (enero a diciembre)
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        // Consulta que agrupa por clientes y suma los valores de proyectos en ejecución
        $Base_results = DB::table('base_comerciales')
                            ->select(DB::raw("nom_cliente, SUM(valor_proyecto) as valor"))
                            ->where($filters_array)
                            ->whereBetween('fecha', $date_filters_array)
                            ->groupBy('nom_cliente')
                            ->get();

        return $Base_results;
    }

    /**
     * Obtiene la sumatoria de registros en estado "Venta" (estado 6)
     * Agrupa los resultados por clientes y suma el valor de los proyectos
     * @param int|null $comercial_id ID del comercial para filtrar
     * @param \App\Models\Mes|null $mes Objeto del mes para filtrar fechas
     * @param \App\Models\Año $año Objeto del año para filtrar fechas
     * @return \Illuminate\Support\Collection Colección con clientes y valores agrupados
     */
    public function getVenta($comercial_id, $mes, $año){
        // Arrays para manejar los filtros de la consulta
        $filters_array = [];
        $date_filters_array = [];

        // Estado de VENTA = 6 (Venta finalizada)
        array_push($filters_array, ['id_estado', 6]);

        // Filtro opcional por comercial específico
        if ($comercial_id){
            array_push($filters_array, ['id_user', $comercial_id]);
        }

        // Filtro de fechas: si hay mes específico o todo el año
        if ($mes){
            // Si se especifica mes, filtra solo ese mes
            array_push($date_filters_array, [$mes->f_inicio, $mes->f_fin]);
        }else {
            // Si no hay mes, trae todos los meses del año (enero a diciembre)
            $primer_mes = Mes::select('f_inicio')->where('ano_id', $año->id)->where('identifier', 1)->first();
            $ultimo_mes = Mes::select('f_fin')->where('ano_id', $año->id)->where('identifier', 12)->first();

            array_push($date_filters_array, [$primer_mes->f_inicio, $ultimo_mes->f_fin]);
        }

        // Consulta que agrupa por clientes y suma los valores de proyectos vendidos
        $Base_results = DB::table('base_comerciales')
                            ->select(DB::raw("nom_cliente, SUM(valor_proyecto) as valor"))
                            ->where($filters_array)
                            ->whereBetween('fecha', $date_filters_array)
                            ->groupBy('nom_cliente')
                            ->get();

        return $Base_results;
    }
}
