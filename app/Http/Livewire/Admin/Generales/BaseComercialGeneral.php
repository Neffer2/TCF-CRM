<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Año;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;

/**
 * Componente Livewire para mostrar y filtrar la base comercial general
 * Permite filtrar por comercial, centro de costos, mes, estado y año
 * También incluye funcionalidad de exportación a Excel
 */
class BaseComercialGeneral extends Component
{
    // Trait para manejar la paginación de los resultados
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Propiedades para los filtros del formulario
    public $comercial;     // ID del comercial/usuario seleccionado
    public $centro;        // Código de centro de costos para filtrar
    public $mes;           // ID del mes seleccionado
    public $estado;        // ID del estado de cuenta seleccionado
    public $año;           // ID del año seleccionado
    public $valorTotal;    // Suma total de los valores de proyectos filtrados

    // Arrays para almacenar las opciones de los filtros
    public $comerciales = []; // Lista de comerciales/usuarios disponibles
    public $estados = [];     // Lista de estados de cuenta disponibles
    public $años = [];        // Lista de años disponibles
    public $yearInfo;         // Objeto completo del año con sus meses

    // Filtros solicitados desde componente padre o URL
    public $requested_filters;

    /**
     * Método principal que renderiza el componente con la base comercial filtrada
     * Aplica todos los filtros de búsqueda y calcula el valor total
     */
    public function render()
    {
        // Array para almacenar todos los filtros aplicados
        $filtros = [];

        // Filtro por código de centro de costos (búsqueda parcial)
        if($this->centro){
            array_push($filtros, ['cod_cc', 'LIKE', "%$this->centro%"]);
        }

        // Filtro por año: busca registros entre el primer y último día del año
        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtro por mes específico: busca registros entre el primer y último día del mes
        if ($this->mes){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->find($this->mes)->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->smeses->find($this->mes)->f_fin]);
        }

        // Filtro por estado de cuenta específico
        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }

        // Filtro por comercial/usuario específico
        if($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }

        // Calcula el valor total de todos los proyectos que coinciden con los filtros
        $this->valorTotal = Base_comercial::where($filtros)->sum('valor_proyecto');

        // Obtiene los registros paginados de la base comercial
        $baseComerciales = Base_comercial::where($filtros)->paginate(25);

        return view('livewire.admin.generales.base-comercial-general', ['baseComerciales' => $baseComerciales]);
    }

    /**
     * Método que se ejecuta al inicializar el componente
     * Carga todos los datos iniciales necesarios para los filtros
     */
    public function mount(){
        $this->getComerciales();
        $this->getEstados();
        $this->getAños();
        $this->getRequestedFilters();
    }

    /**
     * Aplica los filtros que fueron solicitados desde el componente padre o URL
     * Permite pre-cargar el componente con filtros específicos
     */
    public function getRequestedFilters(){
        $this->año = $this->requested_filters['año'];
        $this->mes = $this->requested_filters['mes'];
        $this->comercial = $this->requested_filters['comercial'];
        $this->estado = $this->requested_filters['estado'];
    }

    /**
     * Carga la lista de comerciales (usuarios con rol = 2)
     * Los comerciales son usuarios que pueden gestionar la base comercial
     */
    public function getComerciales(){
        $this->comerciales = User::where('rol', 2)->get();
    }

    /**
     * Carga todos los estados de cuenta disponibles
     * Los estados definen el status de los registros de la base comercial
     */
    public function getEstados(){
        $this->estados = EstadoCuenta::all();
    }

    /**
     * Carga la lista de años disponibles y establece el año más reciente por defecto
     * Automáticamente selecciona el año más reciente si no hay filtros previos
     */
    public function getAños(){
        $this->años = Año::all();

        // Si no hay año pre-seleccionado, toma el más reciente
        if (!$this->año) {
            $añoMasReciente = $this->años->sortByDesc('description')->first();
            if ($añoMasReciente) {
                $this->año = $añoMasReciente->id;
            }
        }
        $this->updatedAño();
    }

    /**
     * Método que se ejecuta cuando se actualiza la propiedad 'año'
     * Valida el año seleccionado y carga la información completa del año
     */
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        // Carga la información completa del año incluyendo sus meses
        $this->yearInfo = Año::find($this->año);
    }

    /**
     * Exporta los datos de la base comercial filtrada a un archivo Excel
     * Aplica los mismos filtros que la vista para generar el reporte
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportar(){
        // Array para almacenar los filtros que se aplicarán a la exportación
        $filtros = [];

        // Aplica filtro por comercial si está seleccionado
        if ($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }

        // Aplica filtro por año si está seleccionado
        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Aplica filtro por estado si está seleccionado
        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }

        // Genera y descarga el archivo Excel con los datos filtrados
        return Excel::download(new BaseExport(['filtros' => $filtros]), 'Reporte Base Comercial.xlsx');
    }
}
