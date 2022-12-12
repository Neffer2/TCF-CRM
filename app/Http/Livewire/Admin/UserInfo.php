<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

class UserInfo extends Component
{   
    public $userInfo;
    protected $listeners = ['live-info' => 'mount'];

    public function render()
    {
        return view('livewire.admin.user-info');
    }

    public function mount($id_user){
        $this->userInfo = User::where('id', $id_user)->first();
    } 
}
