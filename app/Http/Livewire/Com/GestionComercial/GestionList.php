<?php

namespace App\Http\Livewire\Com\GestionComercial;

use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\EstadoGestionComercial;

class GestionList extends Component
{
    // Useful vars
    public $datos = [];
    public $estados = [];
    protected $listeners = ['list' => 'mount'];

    public function render()
    {
        return view('livewire.com.gestion-comercial.gestion-list');
    }

    public function mount(){
        $this->getEstados();
        $this->datos = GestionComercial::select('nombre','apellido','empresa','cargo','id_estado','correo','celular')->get();
    }

    public function getEstados(){
        $this->estados = EstadoGestionComercial::select('id', 'description')->get();
    }
}
