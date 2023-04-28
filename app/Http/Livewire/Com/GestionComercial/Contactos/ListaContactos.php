<?php

namespace App\Http\Livewire\Com\GestionComercial\Contactos;

use Livewire\Component;
use App\Models\Contacto;
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
        $contactos = Contacto::select('id','nombre','apellido','empresa','cargo','correo','celular','web', 'direccion', 'pbx')->where('id_user', Auth::id())->orderBy('id', 'desc')->paginate(5);
        return view('livewire.com.gestion-comercial.contactos.lista-contactos', ['contactos' => $contactos]);
    }
}
