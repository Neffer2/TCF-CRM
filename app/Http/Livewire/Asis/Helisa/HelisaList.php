<?php

namespace App\Http\Livewire\Asis\Helisa;
 
use Livewire\Component;
use App\Models\Helisa;
use App\Models\User;
use App\Models\Año;
use App\Models\Mes;
use App\Models\Cuenta;
use App\Models\Asistente;
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
        return view('livewire.asis.helisa.helisa-list');
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
        $this->getComerciales(); 
        $this->getAños();
        $this->getMeses();
        $this->getCuentas(); 

        $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
        $this->list = Helisa::where('comercial', $asistente->comercial_id)->get();
    }
}
