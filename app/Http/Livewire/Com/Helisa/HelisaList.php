<?php

namespace App\Http\Livewire\Com\Helisa;

use Livewire\Component;
use App\Models\Helisa;
use App\Models\User;
use App\Models\Año;
use App\Models\Mes;
use App\Models\Cuenta;
use Illuminate\Support\Facades\Auth;

class HelisaList extends Component
{   
    //USEFUL VARS
    public $comerciales = [];
    public $cuentas = []; 
    public $años = []; 
    public $meses = []; 
     
    public function render() 
    {   
        $this->getComerciales();
        $this->getAños();
        $this->getMeses();
        $this->getCuentas();
        return view('livewire.com.helisa.helisa-list');
    }    

    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    public function getAños(){
        $this->años = Año::select('id','description')->get();
    }

    public function getMeses(){
        $this->meses = Mes::select('id','description')->where('id', '<', 13)->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    public function mount(){
        $this->list = Helisa::where('comercial', Auth::user()->id)->get();
    }
}