<?php

namespace App\Http\Livewire\Com\GestionComercial;

use App\Models\GestionComercial;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
 
use Livewire\Component;

class NuevoProspecto extends Component  
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
    public $direccion;

    public function render() 
    {
        return view('livewire.com.gestion-comercial.nuevo-prospecto');
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
        $this->validate(['cargo' => ['required', 'string']]);
    }

    public function updatedCelular(){
        $this->validate(['cargo' => ['required', 'string']]);
    }

    public function updatedCorreo(){
        $this->validate(['correo' => ['required', 'string']]);
    }
 
    public function updatedWeb(){
        $this->validate(['web' => ['required', 'string']]);
    }

    public function updatedPbx(){
        $this->validate(['pbx' => ['required', 'string']]);
    }

    public function updatedDireccion(){
        $this->validate(['direccion' => ['required', 'string']]);
    }

    public function store(){
        $this->validate([
            'nombre' => ['required', 'string'],
            'apellido' => ['required', 'string'],
            'empresa' => ['required', 'string'],
            'cargo' => ['required', 'string'],
            'celular' => ['required', 'string'],
            'correo' => ['required', 'string'],
            'pbx' => ['required', 'string'],
            'web' => ['required', 'string'],
            'direccion' => ['required', 'string']
        ]);

        $gestiones = new GestionComercial;
        $gestiones->nombre = $this->nombre;
        $gestiones->apellido = $this->apellido;
        $gestiones->empresa = $this->empresa;
        $gestiones->cargo = $this->cargo;
        $gestiones->correo = $this->correo; 
        $gestiones->celular = $this->celular;
        $gestiones->pbx = $this->pbx;
        $gestiones->web = $this->web;
        $gestiones->direccion = $this->direccion;
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
 