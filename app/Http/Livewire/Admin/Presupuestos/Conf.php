<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\models\Año;
use App\models\Mes;

class Conf extends Component
{   
    // help vars
    public $años = ''; 
    public $storedAñoData;

    // models
    public $añoModel;

    // listeners
    protected $listeners = ['refresh' => 'mount'];

    public function render()
    {
        return view('livewire.admin.presupuestos.conf');
    }

    public function mount(){
        $this->getAños();
    }

    public function getAños(){
        $this->años = Año::select('id', 'description')->get();
    }

    public function updatedAñomodel(){
        $this->storedAñoData = Mes::where('ano_id', $this->añoModel)->get();   
    }
}
