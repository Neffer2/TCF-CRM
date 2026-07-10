<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use App\Http\Livewire\Com\GestionComercial\Clientes\Cliente;
use App\Models\clientes;
use App\Models\Año;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ValidacionesCliente extends Component
{
    public $año;
    public $cod_cc;
    public $cod_cliente;
    public $fecha = 'desc';


    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render(){

        // Filtros base: solo cliente con código de centro de costo
        $filtros = [];

        // Filtro por año si se especifica
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Validamos si el usuario es Lider Comercial
        if (Auth::user()->comerciales()->exists()) {
            array_push($filtros, ['estado_id', 4]);

            // Obtenemos los ids de los comerciales asignados al Lider
            $comerciales_id = Auth::user()->comerciales()->pluck('users.id');

            // Obtenemos el listado de cliente
            $clientes = Cliente::where($filtros)
                ->whereHas('gestion', function ($query) use ($comerciales_id) {
                    $query->whereIn('id_user', $comerciales_id);
                })
                ->orderBy('id', $this->fecha)
                ->paginate(15);
        }
        else {
            $clientes = [];
        }
        return view('livewire.admin.gestion-comercial.validaciones-cliente', [
            'clientes' => $clientes,
            'añosList' => Año::all(),
            'estadosList' => $this->getEstados()]);
    }

    public function mount(){
        $this->getEstados();
        $this->getAños();
    }

    /**
     * Obtiene la lista de estados de presupuesto disponibles
     */
    public function getEstados()
    {
        return clientes::select('id', 'DescripcionCliente')->get();
    }

    /**
     * Obtiene la lista de años disponibles
     * Establece el año actual como seleccionado por defecto
     */
    public function getAños(){
        $this->años = Año::all();
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    /**
     * Se ejecuta cuando cambia el año seleccionado
     * Valida el año y obtiene su información detallada
     */
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        // Obtiene la información completa del año incluyendo sus meses
        $this->yearInfo = Año::find($this->año);
    }
}
