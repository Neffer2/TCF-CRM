<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Base_comercial;

class BaseList extends Component
{
    public $list;

    public function render()
    {
        $this->list = Base_comercial::all();
        return view('livewire.base-list');
    }
}
