<?php

namespace App\Http\Livewire\Admin\Produccion\Consumidos;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto;
use Livewire\WithPagination;

/**
 * Componente Livewire para la gestión de consumidos en producción
 * Permite filtrar y paginar presupuestos con órdenes de compra específicas
 */
class Consumidos extends Component
{
    // Trait para implementar paginación en el componente
    use WithPagination;

    // Configura el tema de paginación a Bootstrap
    protected $paginationTheme = 'bootstrap';

    // Propiedades del componente

    /**
     * Código de centro de costo para filtrar presupuestos
     * @var string
     */
    public $cod_cc;

    /**
     * Array que almacena los estados de presupuesto disponibles
     * @var array
     */
    public $estados = [];

    /**
     * Método principal para renderizar el componente
     * Aplica filtros y obtiene presupuestos paginados con sus órdenes de compra
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Inicializa array de filtros
        $filters = [];

        // Si se especifica un código de centro de costo, agrega filtro
        if ($this->cod_cc){
            array_push($filters, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }

        // Obtiene presupuestos con sus órdenes de compra relacionadas
        // Filtra solo órdenes con estado 1 (activo) o estado 5 (procesado)
        // Ordena por ID descendente y aplica paginación de 15 elementos
        $presupuestos = PresupuestoProyecto::with('ordenesCompra')
            ->whereHas('ordenesCompra', function ($orden){
                $orden->where('estado_id', 1)->orWhere('estado_id', 5);
            })
            ->orderBy('id', 'desc')
            ->where($filters)
            ->paginate(15);

        return view('livewire.admin.produccion.consumidos.consumidos', ['presupuestos' => $presupuestos]);
    }

    /**
     * Método que se ejecuta al inicializar el componente
     * Carga los estados de presupuesto disponibles
     *
     * @return void
     */
    public function mount(){
        $this->getEstados();
    }

    /**
     * Obtiene los estados de presupuesto disponibles
     * Excluye el estado con ID 3 del listado
     *
     * @return void
     */
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')
            ->where('id', '<>', 3)
            ->get();
    }

}
