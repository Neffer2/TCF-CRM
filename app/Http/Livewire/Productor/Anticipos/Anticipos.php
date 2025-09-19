<?php

namespace App\Http\Livewire\Productor\Anticipos;

use Livewire\Component;
use App\Models\Anticipo;
use Illuminate\Support\Facades\Auth;

class Anticipos extends Component
{
    // Models

    public function render()
    {
        $anticipos = Anticipo::where('productor_id', Auth::user()->id)->paginate(15);
        return view('livewire.productor.anticipos.anticipos', compact('anticipos'));
    }
}  
 