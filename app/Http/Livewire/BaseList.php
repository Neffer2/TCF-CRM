<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Base_comercial;

class BaseList extends Component 
{
    public $list;   
    protected $listeners = ['live-base' => 'mount'];

    public function render()
    {
        return view('livewire.base-list');
    }

    public function mount ($user_id){
        $this->list = Base_comercial::where('id_user', $user_id)->get();
    }
}
