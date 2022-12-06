<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rules;

class RegisterLive extends Component
{ 
    public $name = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';

    public function render()
    {
        return view('livewire.register-live');
    }

    public function updatedName (){
        $data = $this->validate(['name' => ['required', 'string', 'max:255']]);
    }

    public function updatedEmail (){ 
        $data = $this->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:users']]);
    }

    public function updatedPassword (){
        $data = $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }

    public function updatedPasswordConfirmation (){
        $data = $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }
}
