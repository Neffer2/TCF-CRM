<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;

class LoginLive extends Component
{
    public $email;
    public $password;

    public function render()
    {
        return view('livewire.auth.login-live');
    }

    public function updatedEmail (){
        $this->validate(['email' => ['required', 'string', 'email', 'max:255']]); 
    }

    public function updatedPassword (){
        $this->validate(['password' => ['required']]);
    }
}
