<?php

namespace App\Http\Livewire\Admin\Produccion\Consumidos;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto; 
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
        if ($this->cod_cc){
            $presupuestos = PresupuestoProyecto::with('ordenesCompra')->whereHas('ordenesCompra', function ($orden){
                $orden->where('estado_id', 1)->orWhere('estado_id', 5);
            })->orderBy('id', 'desc')->where('cod_cc', 'like', "%$this->cod_cc%")->paginate(15);
        }else {
            $presupuestos = PresupuestoProyecto::with('ordenesCompra')->whereHas('ordenesCompra', function ($orden){
                $orden->where('estado_id', 1)->orWhere('estado_id', 5);
            })->orderBy('id', 'desc')->paginate(15);
        }
                
        return view('livewire.admin.produccion.consumidos.consumidos', ['presupuestos' => $presupuestos]);
    }

    public function mount(){ 
        $this->getEstados();
    }
 
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }
} 
 