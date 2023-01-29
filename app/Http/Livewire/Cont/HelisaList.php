<?php

namespace App\Http\Livewire\Cont;

use Livewire\Component;
use App\Models\Helisa;

class HelisaList extends Component
{
    public $list;

    public function render()
    {
        return view('livewire.cont.helisa-list');
    }

    public function mount(){
        $this->list = Helisa::all();
    }
}
