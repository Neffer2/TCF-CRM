<?php

namespace App\Http\Livewire\Com\GestionComercial;

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

    public function render() 
    {
        $this->getContactos();
        return view('livewire.com.gestion-comercial.nuevo-prospecto');
    }

    public function getContactos(){
        if(Auth::user()->rol == 2){
            $this->contactos = Contacto::where('id_user', Auth::id())->get();
        }else if(Auth::user()->rol == 5){
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $this->contactos = Contacto::where('id_user', $asistente->comercial_id)->get();
        }
    }
    
    public function updatedContacto (){  
        $this->validate(['contacto' => ['required', 'numeric']]);
    }

    public function store(){
        $this->validate([
            'contacto'  => ['required', 'numeric'],
        ]);
        
        if (Auth::user()->rol == 2){
            $gestiones = new GestionComercial;
            $gestiones->id_contacto = $this->contacto;
            $gestiones->id_user = Auth::id();
            $gestiones->save();
        }else if(Auth::user()->rol == 5){
            $this->comercial_id = Asistente::where('asistente_id', Auth::user()->id)->first()->comercial_id;

            $gestiones = new GestionComercial;
            $gestiones->id_contacto = $this->contacto;
            $gestiones->id_user = $this->comercial_id;
            $gestiones->save();
        }

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
 