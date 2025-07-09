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
    // Modelo para el contacto seleccionado
    public $contacto;

    // Variable para almacenar la lista de contactos disponibles
    public $contactos = [];

    // Renderiza la vista del formulario de nuevo prospecto
    public function render()
    {
        $this->getContactos();
        return view('livewire.com.gestion-comercial.nuevo-prospecto');
    }

    // Obtiene los contactos según el rol del usuario (comercial o asistente)
    public function getContactos(){
        if(Auth::user()->rol == 2){
            // Comerciales ven solo sus propios contactos
            $this->contactos = Contacto::where('id_user', Auth::id())->get();
        }else if(Auth::user()->rol == 5){
            // Asistentes ven los contactos del comercial asignado
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $this->contactos = Contacto::where('id_user', $asistente->comercial_id)->get();
        }
    }

    // Valida el campo contacto cuando se actualiza
    public function updatedContacto (){
        $this->validate(['contacto' => ['required', 'numeric']]);
    }

    // Guarda el nuevo prospecto en la base de datos
    public function store(){
        $this->validate([
            'contacto'  => ['required', 'numeric'],
        ]);

        if (Auth::user()->rol == 2){
            // Si es comercial, asigna el prospecto a sí mismo
            $gestiones = new GestionComercial;
            $gestiones->id_contacto = $this->contacto;
            $gestiones->id_user = Auth::id();
            $gestiones->save();
        }else if(Auth::user()->rol == 5){
            // Si es asistente, asigna el prospecto al comercial correspondiente
            $this->comercial_id = Asistente::where('asistente_id', Auth::user()->id)->first()->comercial_id;

            $gestiones = new GestionComercial;
            $gestiones->id_contacto = $this->contacto;
            $gestiones->id_user = $this->comercial_id;
            $gestiones->save();
        }

        // Limpia los campos del formulario y emite evento para actualizar la lista
        $this->limpiar();
        $this->emit('list');
        return redirect()->back()->with('success', 'Prospecto creado exitosamente')->withInput();
    }

    // Limpia los campos del formulario
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
