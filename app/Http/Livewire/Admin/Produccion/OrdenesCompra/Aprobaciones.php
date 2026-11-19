<?php

namespace App\Http\Livewire\Admin\Produccion\OrdenesCompra;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\EstadoOrdenesCompra;
use Livewire\WithPagination;
use App\Models\Año;
use App\Models\User;
use App\Models\TipoOrdenCompra;


class Aprobaciones extends Component
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

    protected function estadosVisibles(): array
    {
        $usuariosPorEstado = [
            9 => [2, 10, 91, 208, 197, 214, 145, 181, 210, 206],
            10 => [10, 192, 9, 17],
            11 => [10, 8],
        ];
        $estados = [];
        foreach ($usuariosPorEstado as $estado => $usuarios) {
            if ((int) auth()->user()->rol === 1 && in_array((int) auth()->id(), $usuarios, true)) {
                $estados[] = $estado;
            }
        }

        return $estados ?: [9, 10, 11];
    }

    public function updating($name)
    {
        if (in_array($name, ['cod_oc', 'cod_cc', 'fecha', 'estado', 'año', 'tipo', 'productor', 'documento'], true)) {
            $this->resetPage();
        }
    }

    public function render(){

        // Inicia la consulta base
        $query = OrdenCompra::whereIn('estado_id', $this->estadosVisibles());

        // Filtro por estado de la orden de compra
        if ($this->estado){
            $query->where('estado_id', $this->estado);
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
            $query->where(function ($q) {
                $q->where('cod_oc', 'LIKE', "%$this->cod_oc%")
                    ->orWhere('id', 'LIKE', "%$this->cod_oc%");
            });
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
        if ($this->productor_id){
            $query->whereHas('naturalInfo', function ($natural) {
                $natural->where('productor_id', $this->productor_id);
            });
        }

        // Aplicar ordenamiento y paginación
        $ordenes = $query->orderBy('created_at', $this->fecha)->paginate(15);

        return view('livewire.admin.ordenescompra.aprobaciones.aprobaciones-ordenes-compra', ['ordenes' => $ordenes]);
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
     * Obtiene los estados de órdenes de compra Lider produccion
     */
    public function getEstados(){
        $this->estados = EstadoOrdenesCompra::whereIn('id', $this->estadosVisibles())->get();
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
        $this->yearInfo = $this->año ? Año::find($this->año) : null;
    }
}
