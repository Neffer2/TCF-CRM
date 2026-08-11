<?php

namespace App\Http\Livewire\Com\GestionComercial\Contactos;

use App\Models\Contacto;
use App\Models\clientes;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

use Livewire\Component;

class NuevoContacto extends Component
{
    //Models
    public $nombre;    // Nombre del contacto
    public $apellido;  // Apellido del contacto
    public $empresa;   // Empresa del contacto
    public $cargo;     // Cargo del contacto
    public $celular;   // Celular del contacto
    public $correo;    // Correo electrónico del contacto
    public $web;       // Sitio web del contacto
    public $pbx;       // PBX del contacto
    public $ciudad;    // Ciudad del contacto
    public $direccion; // Dirección del contacto
    public $cliente_id; // ID del cliente asociado al contacto
    public $listaClientes; // Colección de clientes para la lista

    // Renderiza la vista principal del formulario de nuevo contacto
    public function render()
    {
        return view('livewire.com.gestion-comercial.contactos.nuevo-contacto');
    }

    // Valida el campo nombre cuando se actualiza
    public function updatedNombre(){
        $this->validate(['nombre' => ['required', 'string']]);
    }

    // Valida el campo apellido cuando se actualiza
    public function updatedApellido(){
        $this->validate(['apellido' => ['required', 'string']]);
    }

    // Valida el campo empresa cuando se actualiza
    public function updatedEmpresa(){
        $this->validate(['empresa' => ['required', 'string']]);
    }

    // Valida el campo cargo cuando se actualiza
    public function updatedCargo(){
        $this->validate(['cargo' => ['string']]);
    }

    // Valida el campo celular cuando se actualiza
    public function updatedCelular(){
        $this->validate(['celular' => ['string']]);
    }

    // Valida el campo correo cuando se actualiza
    public function updatedCorreo(){
        $this->validate(['correo' => ['string', 'email']]);
    }

    // Valida el campo web cuando se actualiza
    public function updatedWeb(){
        $this->validate(['web' => ['nullable', 'string']]);
    }

    // Valida el campo pbx cuando se actualiza
    public function updatedPbx(){
        $this->validate(['pbx' => ['nullable', 'string']]);
    }

    // Valida el campo dirección cuando se actualiza
    public function updatedDireccion(){
        $this->validate(['direccion' => ['string']]);
    }

    // Valida el campo ciudad cuando se actualiza
    public function updatedCiudad(){
        $this->validate(['ciudad' => ['string']]);
    }

    // Validación individual para cliente_id
    public function updatedClienteId()
    {
        $this->validate(['cliente_id' => ['exists:clientes,id']]);
    }

    public function mount()
    {
        // Trae los clientes de la BD (solo id y nombre para optimizar memoria)
        $this->listaClientes = clientes::select('id', 'nombre')->get();
    }

    // Guarda el contacto en la base de datos
    public function store(){
        $this->validate([
            'nombre' => ['required', 'string'],
            'apellido' => ['required', 'string'],
            'empresa' => ['required', 'string'],
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'cargo' => ['string'],
            'celular' => ['string'],
            'correo' => ['string'],
            'pbx' => ['nullable', 'string'],
            'web' => ['nullable', 'string'],
            'direccion' => ['string'],
            'ciudad' => ['string']
        ]);

        // Crea el nuevo contacto y lo asocia al usuario autenticado
        $gestiones = new Contacto;
        $gestiones->nombre = $this->nombre;
        $gestiones->apellido = $this->apellido;
        $gestiones->empresa = $this->empresa;
        $gestiones->id_cliente = $this->cliente_id;
        $gestiones->cargo = $this->cargo;
        $gestiones->correo = $this->correo;
        $gestiones->celular = $this->celular;
        $gestiones->pbx = $this->pbx;
        $gestiones->web = $this->web;
        $gestiones->direccion = $this->direccion;
        $gestiones->ciudad = $this->ciudad;
        $gestiones->id_user = Auth::id();
        $gestiones->save();

        // Limpia los campos del formulario y emite evento para recargar la lista
        $this->limpiar();
        $this->emit('list');
        return redirect()->back()->with('success', 'Contacto creado exitosamente')->withInput();
    }

    // Limpia los campos del formulario
    public function limpiar(){
        $this->nombre = "";
        $this->apellido = "";
        $this->empresa = "";
        $this->cliente_id = "";
        $this->cargo = "";
        $this->celular = "";
        $this->correo = "";
        $this->web = "";
        $this->pbx = "";
        $this->direccion = "";
        $this->ciudad = "";
    }
}
