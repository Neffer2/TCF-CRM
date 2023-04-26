<?php

namespace App\Http\Livewire\Asis\GestionComercial\Contactos;

use Livewire\Component;
use App\Models\Contacto;
use App\Models\Asistente;
use App\Models\EstadoGestionComercial; 
use Livewire\WithPagination;  
use Illuminate\Support\Facades\Auth; 

class ListaContactos extends Component
{
    use WithPagination; 
    protected $paginationTheme = 'bootstrap'; 
    
    // Useful vars
    protected $listeners = ['list' => 'render'];
     
    public function render()
    {
        $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
        $contactos = Contacto::select('id','nombre','apellido','empresa','cargo','correo','celular','web')->where('id_user', $asistente->comercial_id)->orderBy('id', 'asc')->paginate(5);
        return view('livewire.asis.gestion-comercial.contactos.lista-contactos', ['contactos' => $contactos]);
    }
} 
