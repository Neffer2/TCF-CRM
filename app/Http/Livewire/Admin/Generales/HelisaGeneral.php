<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use App\Models\Helisa;
use App\Models\Año;
use App\Models\User;
use Livewire\WithPagination;
 
class HelisaGeneral extends Component
{   
    use WithPagination; 
    protected $paginationTheme = 'bootstrap';

    // Models
    public $comercial, $centro, $año; 

    // Useful vars
    public $comerciales = [], $años = [], $yearInfo;

    public function render()
    {
        $filtros = [];

        if($filtros){
            array_push($filtros, ['centro', 'LIKE', "%$this->centro%"]);
        }

        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        if($this->comercial){
            array_push($filtros, ['comercial', $this->comercial]);
        }

        $registros_helisa = Helisa::where($filtros)->paginate(15);
        return view('livewire.admin.generales.helisa-general', ['registros_helisa' => $registros_helisa]);
    } 

    public function mount (){ 
        $this->getComerciales();
        $this->getAños();
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

    public function exportar(){
        if ($this->comercial){
            $comercial = $this->comercial; 
        }else {
            $comercial = 'none';
        }

        if ($this->centro){
            $centro = $this->centro;
        }else{
            $centro = 'none';
        }
 
        return redirect()->route('export-helisa', ['comercial' => $comercial, 'centro' => $centro]);  
    }     
}
