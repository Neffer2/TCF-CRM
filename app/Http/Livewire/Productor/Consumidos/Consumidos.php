<?php

namespace App\Http\Livewire\Productor\Consumidos;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Consumidos extends Component
{
    // Habilita la paginación de Livewire
    use WithPagination;
    // Define el tema de paginación como bootstrap
    protected $paginationTheme = 'bootstrap';

    // Variables de modelo
    public $cod_cc;

    // Variables útiles
    public $estados = [];

    // Renderiza la vista principal del componente
    public function render()
    {
        $filters = [];  // Filtros para la consulta

        // Si se ingresó un código de centro de costos, lo agrega a los filtros
        if ($this->cod_cc){
            array_push($filters, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }

        // Filtra por el productor autenticado
        array_push($filters, ['productor', Auth::id()]);

        // Consulta los presupuestos con órdenes de compra en estado 1 o 5
        $presupuestos = PresupuestoProyecto::with('ordenesCompra')->whereHas('ordenesCompra', function ($orden){
            $orden->where('estado_id', 1)->orWhere('estado_id', 5);
        })->orderBy('id', 'desc')->where($filters)->paginate(15);

        // Retorna la vista con los presupuestos paginados
        return view('livewire.productor.consumidos.consumidos', ['presupuestos' => $presupuestos]);
    }

    // Método que se ejecuta al montar el componente
    public function mount(){
        $this->getEstados();
    }

    // Obtiene los estados de presupuesto, excepto el id 3
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }
}
