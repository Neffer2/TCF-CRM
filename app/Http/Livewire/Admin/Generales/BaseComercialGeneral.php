<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use Livewire\WithPagination; 
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Año;
use App\Models\User;

class BaseComercialGeneral extends Component
{ 
    use WithPagination; 
    protected $paginationTheme = 'bootstrap';

    // Models
    public $comercial, $centro, $estado, $año;

    // Useful vars
    public $comerciales = [], $estados = [], $años = [], $yearInfo;

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

        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);
        }
        
        if($this->comercial){
            array_push($filtros, ['id_user', $this->comercial]);
        }


        $baseComerciales = Base_comercial::where($filtros)->paginate(25);
        return view('livewire.admin.generales.base-comercial-general', ['baseComerciales' => $baseComerciales]);
    }

    public function mount(){
        $this->getComerciales();
        $this->getEstados();
        $this->getAños();
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
}
 