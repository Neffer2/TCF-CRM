<?php

namespace App\Http\Livewire\Com\Helisa;

use Livewire\Component;
use Livewire\WithPagination; 
use App\Models\Helisa; 
use App\Models\User;
use App\Models\Año;
use App\Models\Mes;
use App\Models\Cuenta; 
use Illuminate\Support\Facades\Auth; 

class HelisaList extends Component
{   
    use WithPagination; 
    protected $paginationTheme = 'bootstrap'; 

    // Models
    public $año, $centro, $orderBy = 'DESC';

    //USEFUL VARS
    public $cuentas = [], $años = [], $meses = []; 
     
    public function render() 
    {   
        $filtros = [['comercial', Auth::user()->id]];

        if ($this->centro){
            array_push($filtros, ['centro', 'LIKE', "%$this->centro%"]);
        }

        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        $registrosHelisa = Helisa::where($filtros)->orderBy('created_at', $this->orderBy)->paginate(15);
        return view('livewire.com.helisa.helisa-list', ['registrosHelisa' => $registrosHelisa]);
    }   

    public function mount(){
        $this->getAños();
        $this->getMeses();
        $this->getCuentas();
    }

    public function getMeses(){
        $this->meses = Mes::select('id','description')->where('id', '<', 13)->get();
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
} 
