<?php

namespace App\Http\Livewire\Asis\GestionComercial;

use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\EstadoGestionComercial;
use Livewire\WithPagination; 
use App\Models\Asistente;
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
        $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
        $datos = GestionComercial::select('id','id_contacto','id_estado', 'nom_proyecto_cot')->where('id_user', $asistente->comercial_id)->orWhere('comercial_2', $asistente->comercial_id)->orWhere('comercial_3', $asistente->comercial_id)->orWhere('comercial_4', $asistente->comercial_id)->orderBy('id', 'desc')->paginate(10);
        return view('livewire.asis.gestion-comercial.gestion-list' , ['datos' => $datos]);
    }

    public function getData(){ 
        $this->estados = EstadoGestionComercial::select('id', 'description')->get();
    }
}
