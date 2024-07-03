<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\Tercero;
use Livewire\WithPagination; 

class Personal extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Listener
    protected $listeners = ['terceroRegistrado' => 'render'];
 
    public function render()
    {
        $terceros = Tercero::paginate(15);
        return view('livewire.productor.terceros.personal', ['terceros' => $terceros]);
    }
}
 