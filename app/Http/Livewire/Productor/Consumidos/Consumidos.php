<?php

namespace App\Http\Livewire\Productor\Consumidos;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination; 

class Consumidos extends Component
{
    use WithPagination; 
    protected $paginationTheme = 'bootstrap';
 
    //Models 
    public $cod_cc;

    // Useful vars  
    public $estados = [];

    public function render()
    {
        $filters = [];  

        if ($this->cod_cc){
            array_push($filters, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }

        array_push($filters, ['productor', Auth::id()]);

        $presupuestos = PresupuestoProyecto::with('ordenesCompra')->whereHas('ordenesCompra', function ($orden){
            $orden->where('estado_id', 1)->orWhere('estado_id', 5);
        })->orderBy('id', 'desc')->where($filters)->paginate(15);

        return view('livewire.productor.consumidos.consumidos', ['presupuestos' => $presupuestos]);
    }

    public function mount(){ 
        $this->getEstados();
    }
 
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }
}
 