<?php

namespace App\Http\Livewire\Com\GestionComercial;
 
use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\EstadoGestionComercial;
use App\Models\PresupuestoProyecto; 
use Livewire\WithPagination; 
use Illuminate\Support\Facades\Auth; 

class GestionList extends Component 
{ 
    use WithPagination; 
    protected $paginationTheme = 'bootstrap'; 
    // Models
    public $filtro_nom_proyecto; 
    
    // Useful vars
    public $estados = [];
    protected $listeners = ['list' => 'getData'];
 
    public function render()  
    {    
        $this->getData();
        $filtros = [];  
        
        array_push($filtros, ['id_user', Auth::id()]);   
        if ($this->filtro_nom_proyecto){
            array_push($filtros, ['nom_proyecto_cot', 'like', "%$this->filtro_nom_proyecto%"]);   
        }

        $datos = GestionComercial::select('id', 'id_contacto', 'id_estado', 'nom_proyecto_cot', 'presto_cot')->where($filtros)->orWhere('comercial_2', Auth::id())->orWhere('comercial_3', Auth::id())->orWhere('comercial_4', Auth::id())->orderBy('id', 'desc')->paginate(15);

        return view('livewire.com.gestion-comercial.gestion-list', ['datos' => $datos]);
    }

    public function getData(){ 
        $this->estados = EstadoGestionComercial::select('id', 'description')->get();
    }
} 
 