<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\Tercero;

class Natural extends Component
{
    // Models
    public $tercero, $nombre, $apellido, $correo, $cedula, $telefono, $ciudad, $banco,
            $search_nombre, $search_cedula, $search_telefono;

    // Useful vars
    public $terceros, $ciudades, $presupuestos, $items_presupuesto, $cantidad, $valor;

    public function render()
    {
        $this->getTerceros();
        return view('livewire.productor.ordenes.natural');
    }

    public function mount(){
        $this->ciudades = app('ciudades');
    } 
 
    public function getTerceros(){  
        $filtros = [];
        array_push($filtros, ['estado', 1]);

        if ($this->search_cedula){
            array_push($filtros, ['cedula', 'like', '%' . $this->search_cedula . '%']);
        }

        if ($this->search_nombre){
            array_push($filtros, ['nombre', 'like', '%' . $this->search_nombre . '%']);
        }

        if ($this->search_telefono){
            array_push($filtros, ['telefono', 'like', '%' . $this->search_telefono . '%']);
        }

        $this->terceros = Tercero::select('id', 'nombre', 'apellido', 'cedula')->where($filtros)->get();
    }

    // UPDATES
    public function updatedTercero(){
        if ($this->tercero){
            $tercero = $this->terceros->where('id', $this->tercero)->first();
            $this->nombre = $tercero->nombre;
            $this->apellido = $tercero->apellido;
            $this->correo = $tercero->correo;
            $this->cedula = $tercero->cedula;
            $this->telefono = $tercero->telefono;
            $this->ciudad = $tercero->ciudad;
            $this->banco = $tercero->banco;
        }
    }
} 
 