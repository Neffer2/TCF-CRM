<?php

namespace App\Http\Livewire\Productor\Anticipos;

use Livewire\Component;
use App\Models\Anticipo;
use Illuminate\Support\Facades\Auth;

class Anticipos extends Component
{
    // Models

    // Useful vars
    public $anticipos = [];

    public function render()
    {
        return view('livewire.productor.anticipos.anticipos');
    }

    public function mount(){
        $this->getAnticipos();   
    }

    public function getAnticipos(){
        $this->anticipos = Anticipo::where('productor_id', Auth::user()->id)->get();
    }
}  
