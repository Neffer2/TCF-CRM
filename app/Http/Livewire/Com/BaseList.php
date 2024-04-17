<?php

namespace App\Http\Livewire\Com;

use Livewire\Component;
use Livewire\WithPagination; 
use App\Models\Base_comercial; 
use App\Models\EstadoCuenta;
use App\Models\Año;
use App\Models\Cuenta;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;

class BaseList extends Component   
{  
    use WithPagination; 
    protected $paginationTheme = 'bootstrap';

    // Models
    public $comercial, $centro, $nomProyecto, $estado, $año, $orderBy = 'DESC';
    // Useful vars
    public $cuentas = [], $user_id, $comerciales = [], $estados = [], $años = [], $yearInfo;

    protected $listeners = ['proyectoAdded' => 'mount'];

    public function render() 
    {
        $filtros = [['id_user', $this->user_id]];
        
        if($this->centro){
            array_push($filtros, ['cod_cc', 'LIKE', "%$this->centro%"]);
        }

        if ($this->nomProyecto){
            array_push($filtros, ['nom_proyecto', 'LIKE', "%$this->nomProyecto%"]);
        }

        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }
        
        if($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }

        $proyectos = Base_comercial::where($filtros)->orderBy('created_at', $this->orderBy)->paginate(15);
        return view('livewire.com.base-list', ['proyectos' => $proyectos]);
    }
    
    public function mount ($user_id){
        $this->user_id = $user_id;
        $this->getEstados();
        $this->getCuentas();
        $this->getAños();        
    }

    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
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

    public function exportar(){    
        $filtros = [['id_user', $this->user_id]];

        if($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }

        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }

        return Excel::download(new BaseExport(['filtros' => $filtros]), 'Reporte Base Comercial.xlsx');
    }
} 
 