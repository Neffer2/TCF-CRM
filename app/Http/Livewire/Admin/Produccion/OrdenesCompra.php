<?php

namespace App\Http\Livewire\Admin\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\EstadoOrdenesCompra;
use Livewire\WithPagination;
use App\Models\Año;
use App\Models\User;
use App\Models\TipoOrdenCompra;

/**
 * Componente Livewire para gestionar órdenes de compra de producción
 * Permite filtrar, paginar y visualizar las órdenes con múltiples criterios
 */
class OrdenesCompra extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // PROPIEDADES DE FILTROS
    public $cod_oc;                // Código de Orden de commprs
    public $cod_cc;                // Código de centro de costo para filtrar por presupuesto
    public $fecha = 'desc';        // Orden de fecha (asc/desc)
    public $estado;                // ID del estado de la orden de compra
    public $año;                   // ID del año para filtrar por rango de fechas
    public $tipo;                  // ID del tipo de orden de compra
    public $productor;             // ID del productor para filtrar órdenes
    public $documento;             // Documento del tercero para filtrar órdenes

    // COLECCIONES PARA OPCIONES DE FILTROS
    public $estados = [];          // Lista de estados disponibles
    public $años = [];             // Lista de años disponibles
    public $tipos = [];            // Lista de tipos de órdenes de compra
    public $productores = [];      // Lista de usuarios productores

    // PROPIEDADES ADICIONALES
    public $productor_id;          // ID específico del productor autenticado
    public $yearInfo;              // Información del año seleccionado
    public $estado_id;             // ID especifico del estado de las OC
    public $tipo_oc;

    public $items = [];
    public $queriedOrden = null;
    public $orden_id = null;
    public $selected_item = null;


    public $submódulo = '';

    /**
     * Renderiza la vista del componente con las órdenes filtradas
     * Aplica todos los filtros disponibles y retorna la vista paginada
     * @return \Illuminate\View\View
     */

    public function render(){
        // Inicia la consulta base
        $query = OrdenCompra::query();

        if ($this->estado_id) {
            $this->estado = $this->estado_id;
        }

        if ($this->tipo_oc) {
            $this->tipo = $this->tipo_oc;
        }

        // Filtro por estado de la orden de compra
        if ($this->estado){
            if (is_array($this->estado)) {
                $query->whereIn('estado_id', $this->estado);
            }
            else {
                $query->where('estado_id', $this->estado);
            }
        }

        // Filtro por año: aplica rango de fechas del año seleccionado
        if($this->año && $this->yearInfo){
            $query->where('created_at', '>=', $this->yearInfo->meses->first()->f_inicio)
                  ->where('created_at', '<=', $this->yearInfo->meses->last()->f_fin);
        }

        // Filtro por tipo de orden de compra
        if($this->tipo){
            $query->where('tipo_oc', $this->tipo);
        }

        // Filtro por código de centro de costo del presupuesto
        if ($this->cod_cc){
            $query->whereHas('presupuesto', function ($presto) {
                $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
            });
        }

        // Filtro por código de orden de compra
        if ($this->cod_oc){
            $query->where('cod_oc', 'LIKE', "%$this->cod_oc%")->orWhere('id', 'LIKE', "%$this->cod_oc%");
        }

        // Filtro por documento
        if ($this->documento) {
            $query->where(function($q) {
                $q->WhereHas('naturalInfo', function ($natural) {
                    $natural->WhereHas('tercero', function ($tercero) {
                        $tercero->where('cedula', 'LIKE', "%$this->documento%");
                    });
                })->orWhereHas('proveedor', function ($proveedor) {
                    $proveedor->where('documento', 'LIKE', "%$this->documento%");
                });
            });
        }

        // Filtro por productor: busca en presupuesto o información natural
        if ($this->productor) {
            $query->where(function($q) {
                $q->whereHas('presupuesto', function ($presupuesto) {
                    $presupuesto->where('productor', $this->productor);
                })
                ->orWhereHas('naturalInfo', function ($natural) {
                    $natural->where('productor_id', $this->productor);
                });
            });
        }

        // Filtro específico para usuario productor autenticado
        if ($this->productor_id && $this->tipo == 2){
            $query->whereHas('naturalInfo', function ($natural) {
                $natural->where('productor_id', $this->productor_id);
            });
        }

        // Aplicar ordenamiento y paginación
        $ordenes = $query->orderBy('created_at', $this->fecha)->paginate(15);

        return view('livewire.admin.produccion.ordenes-compra', ['ordenes' => $ordenes]);
    }

    /**
     * Inicializa el componente cuando se monta
     * Carga todas las opciones disponibles para los filtros
     */
    public function mount(){
        $this->getEstados();
        $this->getAños();
        $this->getTipos();
        $this->getProductores();
    }

    /**
     * Obtiene la lista de usuarios productores (rol 7)
     * Carga solo ID y nombre para optimizar la consulta
     */
    public function getProductores(){
        $this->productores = User::select('id', 'name')->where('rol', 7)->get();
    }

    /**
     * Obtiene todos los tipos de órdenes de compra disponibles
     */
    public function getTipos(){
        $this->tipos = TipoOrdenCompra::all();
    }

    /**
     * Obtiene los estados de órdenes de compra excluyendo el estado 3
     */
    public function getEstados(){
        $this->estados = EstadoOrdenesCompra::where('id', '<>', 3)->get();
    }

    /**
     * Obtiene la lista de años disponibles y establece el año actual por defecto
     * Automáticamente selecciona el año más reciente como filtro inicial
     */
    public function getAños(){
        $this->años = Año::all();
        // Establece el año más reciente como predeterminado
        $añoMasReciente = collect($this->años)->sortByDesc('description')->first();
        if ($añoMasReciente) {
            $this->año = $añoMasReciente->id;
            $this->updatedAño();
        }
    }

    /**
     * Se ejecuta cuando se actualiza el filtro de año
     * Valida la selección y carga la información completa del año
     */
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        $this->yearInfo = Año::find($this->año);
    }

    public function listados(){

    }
}
