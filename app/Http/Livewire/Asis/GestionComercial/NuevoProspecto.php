<?php

namespace App\Http\Livewire\Asis\GestionComercial;

use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\Contacto;
use App\Models\Asistente;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class NuevoProspecto extends Component
{
    //Models 
    public $contacto;

    // Useful vars
    public $contactos = [];
    public $comercial_id;

    public function render() 
    { 
        return view('livewire.asis.gestion-comercial.nuevo-prospecto');
    }
    
    public function mount(){
        $this->comercial_id = Asistente::where('asistente_id', Auth::user()->id)->first()->comercial_id;
        $this->getContactos();
    }

    public function getContactos(){
        $this->contactos = Contacto::where('id_user', $this->comercial_id)->get();
    }
     
    public function updatedContacto (){
        $this->validate(['contacto' => ['required', 'numeric']]);
    }

    public function store(){
        $asistente = 
        $this->validate([
            'contacto'  => ['required', 'numeric'],
        ]);

        $gestiones = new GestionComercial;
        $gestiones->id_contacto = $this->contacto;
        $gestiones->id_user = $this->comercial_id;
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
