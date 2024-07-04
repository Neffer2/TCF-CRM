<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\Tercero;

class Natural extends Component
{
    // Models
    public $tercero, $nombre, $cedula, $telefono;

    // Useful vars
    public $terceros;

    public function render()
    {
        $this->getTerceros();
        return view('livewire.productor.ordenes.natural');
    }

    public function mount(){
        
    }

    public function getTerceros(){ 
        $filtros = [];
        array_push($filtros, ['estado', 1]);

        if ($this->cedula){
            array_push($filtros, ['cedula', 'like', '%' . $this->cedula . '%']);
        }

        if ($this->nombre){
            array_push($filtros, ['nombre', 'like', '%' . $this->nombre . '%']);
        }

        if ($this->telefono){
            array_push($filtros, ['telefono', 'like', '%' . $this->telefono . '%']);
        }

        $this->terceros = Tercero::select('id', 'nombre', 'apellido', 'cedula')->where($filtros)->get();
    }
} 
 