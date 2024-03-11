<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\Año; 
use App\Models\User; 
use Livewire\WithPagination; 

class PresupuestosList extends Component
{     
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //Models 
    public $cod_cc, $año, $yearInfo, $orderBy = 'DESC', $nom_proyecto, $comercial;

    // Useful vars 
    public $estados = [], $años = [], $comerciales = [];  

    public function render()  
    {    
        $filtros = [];
        $filtrosGestion = [];

        if ($this->cod_cc){
            array_push($filtros, ['cod_cc', 'like', "%$this->cod_cc%"]);
        }
            
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        } 

        if ($this->nom_proyecto){
            array_push($filtrosGestion, ['nom_proyecto_cot', 'like', "%$this->nom_proyecto%"]); 
        }

        if ($this->comercial){
            array_push($filtrosGestion, ['id_user', $this->comercial]); 
        }

        $presupuestos = PresupuestoProyecto::with('gestion')->whereHas('gestion', function ($gestion) use ($filtrosGestion) { 
                            $gestion->where($filtrosGestion);
                        })->where($filtros)->orderBy('created_at', $this->orderBy)->paginate(15);
        return view('livewire.admin.gestion-comercial.presupuestos-list', ['presupuestos' => $presupuestos]);
    }

    public function mount(){
        $this->getAños();
        $this->getComerciales();
    }
 
    public function getComerciales(){
        $this->comerciales = User::where('rol', 2)->get();
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
