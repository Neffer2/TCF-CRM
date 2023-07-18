<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto; 
use App\Models\GestionComercial;
use App\Models\User; 
use Livewire\WithPagination; 

class PresupuestosList extends Component
{   
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //Models 
    public $cod_cc;

    // Useful vars
    public $estados = []; 

    public function render()
    {           
        $registros = 0;

        if ($this->cod_cc){
            $presupuestos = PresupuestoProyecto::orderBy('id', 'desc')->where('cod_cc', 'like', "%$this->cod_cc%")->paginate(10);
            $registros = PresupuestoProyecto::orderBy('id', 'desc')->where('cod_cc', 'like', "%$this->cod_cc%")->count();
        }else {
            $presupuestos = PresupuestoProyecto::orderBy('id', 'desc')->paginate(15);
            $registros = PresupuestoProyecto::orderBy('id', 'desc')->count();
        }
        

        return view('livewire.admin.gestion-comercial.presupuestos-list', ['presupuestos' => $presupuestos, 'registros' => $registros]);
    }

    public function mount(){
        $this->getEstados();
    }
 
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }
} 
