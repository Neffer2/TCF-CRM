<?php
 
namespace App\Http\Livewire\Asis;

use Livewire\Component;
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Asistente;
use App\Models\Cuenta; 


class BaseList extends Component 
{
    public $list;    
    public $estados = []; 
    public $cuentas = [];
    protected $listeners = ['proyectoAdded' => 'mount'];

    public function render()
    {
        return view('livewire.asis.base-list');
    }
 
    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    public function mount ($user_id){
        $this->getEstados();
        $this->getCuentas(); 
        $idComercialAsignado = Asistente::where('asistente_id', $user_id)->first();
        $this->list = Base_comercial::where('id_user', $idComercialAsignado->comercial_id)->get();
    }
}
