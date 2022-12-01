<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ProfileCard extends Component
{
    public function render()
    {
        return view('livewire.profile-card');
    }

    public function change_slide ($index)
    {   
        $this->emit('change_slide', $index);
    }
}
