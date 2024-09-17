<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\EstadoTercero;
use Livewire\WithPagination; 

class Personal extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    // Models
    public $cedula, $nombre, $estado, $telefono;

    // Useful vars 
    public $estados;

    // Listener
    protected $listeners = ['terceroRegistrado' => 'render'];
 
    public function render()
    {
        $filtros = [];
        if ($this->estado){
            array_push($filtros, ['estado', '=', $this->estado]);
        }

        if ($this->cedula){
            array_push($filtros, ['cedula', 'like', '%' . $this->cedula . '%']);
        }

        if ($this->nombre){
            array_push($filtros, ['nombre', 'like', '%' . $this->nombre . '%']);
        }

        if ($this->telefono){
            array_push($filtros, ['telefono', 'like', '%' . $this->telefono . '%']);
        }

        $terceros = Tercero::where($filtros)->paginate(15);
        return view('livewire.productor.terceros.personal', ['terceros' => $terceros]);
    }    

    public function mount(){
        $this->estados = EstadoTercero::select('id', 'descripcion')->get();
    }
}
  