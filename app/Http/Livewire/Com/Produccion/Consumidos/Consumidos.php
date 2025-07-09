<?php

namespace App\Http\Livewire\Com\Produccion\Consumidos;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Consumidos extends Component
{
    // Habilita la paginación y define el tema de Bootstrap
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Modelo para el filtro de código de centro de costos
    public $cod_cc;

    // Variable para almacenar los estados disponibles
    public $estados = [];

    // Renderiza la vista principal y aplica los filtros de búsqueda
    public function render()
    {
        $filters = [];

        // Si se ingresa un código de centro de costos, lo agrega a los filtros
        if ($this->cod_cc){
            array_push($filters, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }

        // Consulta los presupuestos del usuario autenticado que tengan órdenes en estado 1 o 5
        $presupuestos = PresupuestoProyecto::with('gestion', 'ordenesCompra')
            ->whereHas('gestion', function ($gestion) {
                $gestion->where('id_user', Auth::id());
            })
            ->whereHas('ordenesCompra', function ($orden){
                $orden->where('estado_id', 1)->orWhere('estado_id', 5);
            })
            ->orderBy('id', 'desc')->where($filters)->paginate(15);

        // Retorna la vista con los presupuestos filtrados y paginados
        return view('livewire.com.produccion.consumidos.consumidos', ['presupuestos' => $presupuestos]);
    }

    // Al montar el componente, carga los estados disponibles
    public function mount(){
        $this->getEstados();
    }

    // Obtiene la lista de estados de presupuesto excluyendo el estado 3
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }
}
