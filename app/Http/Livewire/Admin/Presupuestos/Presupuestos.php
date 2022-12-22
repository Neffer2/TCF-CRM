<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\models\User;
use App\models\Presupuesto;
use App\models\Año;

class Presupuestos extends Component
{
    // Model     
    public $comercial; 
    public $año; 
    
    
    // Useful vars
    public $comerciales; 
    public $presupuestos = [];
    public $años = [];
    protected $listeners = ['refresh' => 'mount'];

    public function render()
    {
        return view('livewire.admin.presupuestos.presupuestos');
    }

    public function updatedComercial (){
        $this->getPresupuesto($this->comercial);
    }

    public function updatedAño (){
        $this->getPresupuesto(null, $this->año);
    }

    public function getPresupuesto ($comercial = null, $año = null){

        if ($this->comercial && $this->año){
            return $this->presupuestos = Presupuesto::select('ano_id', 'mes_id', 'valor', 'id_user')
                                                ->where('id_user', $this->comercial)
                                                ->where('ano_id', $this->año)
                                                ->get();
        }

        if ($comercial){
            return $this->presupuestos = Presupuesto::select('mes_id', 'valor', 'id_user')->where('id_user', $this->comercial)->get();
        }
    }

    public function mount (){
        $this->getComerciales();
        $this->getAños();
    }

    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    public function getAños (){
        $this->años = Año::select('id', 'description')->get();
    }
} 
