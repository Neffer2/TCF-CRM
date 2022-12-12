<?php

namespace App\Http\Livewire\Admin;
use App\Models\Base_comercial;

use Livewire\Component;

class BaseComTeam extends Component 
{

    public $list;    
    protected $listeners = ['live-base' => 'mount'];

    public function render()
    {
        return view('livewire.admin.base-com-team');
    }

    public function mount ($user_id){
        $this->list = Base_comercial::where('id_user', $user_id)->get();
    }
}
