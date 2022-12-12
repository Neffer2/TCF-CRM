<?php

namespace App\Http\Livewire\Com;

use Livewire\Component;
use App\Models\Base_comercial;

class BaseList extends Component 
{
    public $list;   

    public function render()
    {
        return view('livewire.com.base-list');
    }

    public function mount ($user_id){
        $this->list = Base_comercial::where('id_user', $user_id)->get();
    }
}
