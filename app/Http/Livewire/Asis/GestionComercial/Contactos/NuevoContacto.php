<?php

namespace App\Http\Livewire\Asis\GestionComercial\Contactos;

use App\Models\Contacto;
use App\Models\Asistente;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
 
use Livewire\Component; 

class NuevoContacto extends Component
{    
    //Models 
    public $nombre;
    public $apellido;
    public $empresa; 
    public $cargo;
    public $celular; 
    public $correo;
    public $web;
    public $pbx;
    public $ciudad;
    public $direccion;
     
    
    public function render()
    {
        return view('livewire.asis.gestion-comercial.contactos.nuevo-contacto');
    } 

    public function updatedNombre(){
        $this->validate(['nombre' => ['required', 'string']]);
    }

    public function updatedApellido(){
        $this->validate(['apellido' => ['required', 'string']]);
    }

    public function updatedEmpresa(){
        $this->validate(['empresa' => ['required', 'string']]);
    }

    public function updatedCargo(){
        $this->validate(['cargo' => ['string']]);
    }

    public function updatedCelular(){
        $this->validate(['celular' => ['string']]);
    }

    public function updatedCorreo(){
        $this->validate(['correo' => ['string', 'email']]);
    }
 
    public function updatedWeb(){
        $this->validate(['web' => ['string']]);
    }

    public function updatedPbx(){
        $this->validate(['pbx' => ['string']]);
    }  

    public function updatedDireccion(){
        $this->validate(['direccion' => ['string']]);
    }

    public function updatedCiudad(){
        $this->validate(['ciudad' => ['string']]); 
    }

    public function store(){
        $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
        $this->validate([
            'nombre' => ['required', 'string'],
            'apellido' => ['required', 'string'],
            'empresa' => ['required', 'string'],
            'cargo' => ['string'],
            'celular' => ['string'],
            'correo' => ['string'],
            'pbx' => ['string'],
            'web' => ['string'],
            'direccion' => ['string'],
            'ciudad' => ['string']
        ]);

        $gestiones = new Contacto;
        $gestiones->nombre = $this->nombre;
        $gestiones->apellido = $this->apellido;
        $gestiones->empresa = $this->empresa;
        $gestiones->cargo = $this->cargo;
        $gestiones->correo = $this->correo; 
        $gestiones->celular = $this->celular;
        $gestiones->pbx = $this->pbx;
        $gestiones->web = $this->web;
        $gestiones->direccion = $this->direccion;
        $gestiones->ciudad = $this->ciudad;
        $gestiones->id_user = $asistente->comercial_id;
        $gestiones->save();
 
        $this->limpiar();
        $this->emit('list'); 
        return redirect()->back()->with('success', 'Contacto creado exitosamente')->withInput();
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
        $this->ciudad = "";
    }
}
