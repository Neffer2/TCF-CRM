<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\models\Año;
use App\models\User;
use Illuminate\Validation\Rules;

class AsignarPresupuesto extends Component
{
    // models
    public $comercialesModel;
    public $añosModel;

    public $eneroModel;
    public $febreroModel;
    public $marzoModel;
    public $abrilModel;
    public $mayoModel;
    public $junioModel;
    public $julioModel;
    public $agostoModel;
    public $septiembreModel;
    public $octubreModel;
    public $noviembreModel;
    public $diciembreModel;

    // Help vars
    public $presupuestoStored;
    

    public function render()
    {
        return view('livewire.admin.presupuestos.asignar-presupuesto');
    }

    public function mount(){
        $this->getModels();
    }

    public function getModels (){
        $this->comercialesStored = User::select('id', 'name', 'rol')->where('rol', 2)->get();
        $this->añosStored = Año::select('id', 'description')->get();
    }

    public function getPresupuestoStored (){
        $this->validate([
            'comercialesModel' => 'required',
            'añosModel' => 'required'
        ]);

        // $this->presupuestoStored::select()
    }
}
