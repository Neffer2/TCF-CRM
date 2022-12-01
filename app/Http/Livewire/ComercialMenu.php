<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ComercialMenu extends Component
{
    public $visible = [true, false, false];
    protected $listeners = ['change_slide' => 'show'];

    public function render()
    {
        return view('livewire.comercial-menu');
    }

    public function show($index)
    {   
        $cont = 0;
        while ($cont < count($this->visible)) {
            $this->visible[$cont] = false;
            $cont++;
        }
        $this->visible[$index] = true;
    }
}
