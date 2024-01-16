<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\Año; 
use Livewire\WithPagination; 

class PresupuestosList extends Component
{     
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //Models 
    public $cod_cc, $año, $yearInfo, $orderBy = 'DESC';

    // Useful vars 
    public $estados = [], $años = [];  

    public function render()  
    {    
        $filtros = [];

        if ($this->cod_cc){
            array_push($filtros, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }
            
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        $presupuestos = PresupuestoProyecto::where($filtros)->orderBy('created_at', $this->orderBy)->paginate(15);
        return view('livewire.admin.gestion-comercial.presupuestos-list', ['presupuestos' => $presupuestos]);
    }

    public function mount(){
        $this->getEstados();
        $this->getAños();
    }
 
    public function getEstados(){
        // $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }

    public function getAños(){
        $this->años = Año::all();
        /* CURRENT YEAR */
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);
        
        $this->yearInfo = Año::find($this->año);
    }
} 
