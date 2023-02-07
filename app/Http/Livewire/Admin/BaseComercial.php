<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Base_comercial;

class BaseComercial extends Component
{   
    public $baseComercial = '';

    public function render()
    {
        return view('livewire.admin.base-comercial');
    }

    public function mount (){ 
        $this->baseComercial = Base_comercial::all();
    }
}
