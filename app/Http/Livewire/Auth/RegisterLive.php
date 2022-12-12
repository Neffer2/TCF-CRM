<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Validation\Rules;

class RegisterLive extends Component
{ 
    public $name = '';
    public $email = '';
    public $telefono = '';
    public $password = '';
    public $passwordConfirmation = '';

    public function render()
    {
        return view('livewire.auth.register-live');
    }
 
    public function updatedName (){
        $this->validate(['name' => ['required', 'string', 'max:255']]);
    }

    public function updatedEmail (){ 
        $this->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:users']]);
    }

    public function updatedTelefono (){
        $this->validate(['telefono' => ['required', 'unique:users', 'numeric']]);
    }

    public function updatedPassword (){
        $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }

    public function updatedPasswordConfirmation (){
        $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }
}
