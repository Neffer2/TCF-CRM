<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\models\Año;

class Conf extends Component
{   
    public $años = '';

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

}
