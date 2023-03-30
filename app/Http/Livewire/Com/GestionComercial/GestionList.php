<?php

namespace App\Http\Livewire\Com\GestionComercial;

use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\EstadoGestionComercial;
use Livewire\WithPagination;

class GestionList extends Component 
{ 
    use WithPagination; 
    protected $paginationTheme = 'bootstrap'; 
    
    // Useful vars
    public $estados = [];
    protected $listeners = ['list' => 'getData'];

    public function render()
    {   
        $this->getData();
        $datos = GestionComercial::select('id','nombre','apellido','empresa','cargo','id_estado','correo','celular')->orderBy('id', 'asc')->paginate(5);
        return view('livewire.com.gestion-comercial.gestion-list', ['datos' => $datos]);
    }

    public function getData(){ 
        $this->estados = EstadoGestionComercial::select('id', 'description')->get();
    }
} 
