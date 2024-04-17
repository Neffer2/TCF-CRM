<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use Livewire\WithPagination; 
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Año;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;

class BaseComercialGeneral extends Component
{ 
    use WithPagination; 
    protected $paginationTheme = 'bootstrap';

    // Models
    public $comercial, $centro, $mes, $estado, $año, $valorTotal;

    // Useful vars
    public $comerciales = [], $estados = [], $años = [], $yearInfo;

    // Filled 
    public $requested_filters;

    public function render()
    {
        $filtros = [];
    
        if($this->centro){
            array_push($filtros, ['cod_cc', 'LIKE', "%$this->centro%"]);
        }

        if($this->año){ 
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        if ($this->mes){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->find($this->mes)->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->find($this->mes)->f_fin]);
        }

        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }
        
        if($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }

        $this->valorTotal = Base_comercial::where($filtros)->sum('valor_proyecto');
        $baseComerciales = Base_comercial::where($filtros)->paginate(25);
        return view('livewire.admin.generales.base-comercial-general', ['baseComerciales' => $baseComerciales]);
    }

    public function mount(){
        $this->getRequestedFilters();
        $this->getComerciales();
        $this->getEstados();
        $this->getAños();
    }

    public function getRequestedFilters(){
        $this->año = $this->requested_filters['año'];
        $this->mes = $this->requested_filters['mes'];
        $this->comercial = $this->requested_filters['comercial'];
        $this->estado = $this->requested_filters['estado'];
    }

    public function getComerciales(){ 
        $this->comerciales = User::where('rol', 2)->get();
    }

    public function getEstados(){ 
        $this->estados = EstadoCuenta::all();
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
        $filtros = [];

        if ($this->comercial){
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
 