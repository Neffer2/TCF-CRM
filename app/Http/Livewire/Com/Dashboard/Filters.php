<?php

namespace App\Http\Livewire\Com\Dashboard;

use Livewire\Component;
use App\Models\Año;
use App\Models\Mes;
use App\Models\User;
use App\Models\Cuenta;
use Illuminate\Support\Facades\Auth;

class Filters extends Component
{
    // Models
    public $mes;
    public $comercial;
    public $año;
    public $cuenta;

    //Useful vars
    public $StdMes = [];
    public $StdComercial = [];
    public $StdAño = [];
    public $StdCuenta = [];

    public function render()
    {
        return view('livewire.com.dashboard.filters');
    }

    public function mount(){ 
        /* Trae la lislta de años */
            $this->StdAño = Año::select('id', 'description')->get();
        /* --- */
        $this->getFilters();
    }

    public function updatedAño(){
        $this->getFilters();
        $this->signals();
    }

    public function updatedMes(){
        $this->signals();
    }

    public function updatedComercial(){
        $this->signals();
    } 

    public function updatedCuenta(){
        $this->signals();
    } 

    public function signals (){
        /* Año debe ser string (description) */
        // Emite el año seleccionado
        if ($this->año){
            $año_desc = Año::select('description')->where('id', $this->año)->first();
            // $this->emit('Graphs', ['año' => $año_desc->description, 'mes' => $this->mes, 'comercial' => $this->comercial]);
            $this->emit('Block1', ['año' => $año_desc->description, 'mes' => $this->mes, 'comercial' => Auth::id(), 'cuenta' => $this->cuenta]);
            $this->emit('Block2', ['año' => $año_desc->description, 'mes' => $this->mes, 'comercial' => Auth::id(), 'cuenta' => $this->cuenta]);
        } 
    }

    public function getFilters (){
        if ($this->año){
            $this->StdMes = Mes::select('id', 'description')->where('ano_id', $this->año)->get();
            // 
            $this->StdComercial = User::select('id', 'name')->where('id', Auth::id())->get();
            $this->StdCuenta = Cuenta::select('id', 'description')->get();
        }else {
            $this->StdMes = [];
            $this->StdComercial = [];
            $this->StdCuenta = [];

            // Block1 refresh
            $this->emit('Block1');
            $this->emit('Block2');
        }
    } 
}

