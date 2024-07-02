<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\Tercero;

class Personal extends Component
{
    // Useful vars
    public $tercero;
 
    public function render()
    {
        return view('livewire.productor.terceros.personal');
    }

    public function mount()
    {
        $this->tercero = Tercero::all();
    }
}
 