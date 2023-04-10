<?php

namespace App\Http\Livewire\Com\GestionComercial;

use App\Models\GestionComercial;
use App\Models\Contacto;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
 
use Livewire\Component;

class NuevoProspecto extends Component  
{    
    //Models 
    public $contacto;

    // Useful vars
    public $contactos = [];

    public function render() 
    {
        $this->getContactos();
        return view('livewire.com.gestion-comercial.nuevo-prospecto');
    }

    public function getContactos(){
        $this->contactos = Contacto::where('id_user', Auth::id())->get();
    }
    
    public function updatedContacto (){
        $this->validate(['contacto' => ['required', 'numeric']]);
    }

    public function store(){
        $this->validate([
            'contacto'  => ['required', 'numeric'],
        ]);

        $gestiones = new GestionComercial;
        $gestiones->id_contacto = $this->contacto;
        $gestiones->id_user = Auth::id();
        $gestiones->save();

        $this->limpiar();
        $this->emit('list');
        return redirect()->back()->with('success', 'Prospecto creado exitosamente')->withInput();
    }
    
    public function limpiar(){
        $this->nombre = "";
        $this->apellido = "";
        $this->empresa = "";
        $this->cargo = "";
        $this->celular = "";
        $this->correo = "";
        $this->web = "";
        $this->pbx = "";
        $this->direccion = "";
    }
}
 