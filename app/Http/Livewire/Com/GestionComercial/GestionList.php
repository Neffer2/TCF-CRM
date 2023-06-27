<?php

namespace App\Http\Livewire\Com\GestionComercial;
 
use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\EstadoGestionComercial;
use Livewire\WithPagination; 
use Illuminate\Support\Facades\Auth; 

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
        $datos = GestionComercial::select('id','id_contacto','id_estado', 'nom_proyecto_cot')->where('id_user', Auth::id())->orWhere('comercial_2', Auth::id())->orWhere('comercial_3', Auth::id())->orWhere('comercial_4', Auth::id())->orderBy('id', 'desc')->paginate(15);
        return view('livewire.com.gestion-comercial.gestion-list', ['datos' => $datos]);
    }

    public function getData(){ 
        $this->estados = EstadoGestionComercial::select('id', 'description')->get();
    }
} 
