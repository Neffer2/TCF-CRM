<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Año;
use App\Models\Mes;
use App\Models\User;

class Filters extends Component
{
    // Models
    public $mes;
    public $comercial;

    //Stored
    public $StdMes = [];
    public $StdComercial = [];

    public function render()
    {
        return view('livewire.admin.dashboard.filters');
    } 

    public function mount(){
        $this->getFilters();
    }

    public function updatedMes(){
        $this->signals();
    }

    public function updatedComercial(){
        $this->signals();
    }

    public function signals (){
        $this->emit('Graphs', ['mes' => $this->mes, 'comercial' => $this->comercial]);
    }

    public function getFilters (){
        $año = Año::select('id')->where('description', date("Y"))->first();
        $this->StdMes = Mes::select('id', 'description')->where('ano_id', $año->id)->get();
        // 
        $this->StdComercial = User::select('id', 'name')->where('rol', 2)->get();
    }
}
