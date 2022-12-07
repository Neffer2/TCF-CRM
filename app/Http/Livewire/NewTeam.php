<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class NewTeam extends Component 
{
    public $name = '';
    public $email = '';
    public $telefono = '';
    public $password = '';
    public $passwordConfirmation = '';

    public function render()
    {
        return view('livewire.new-team');
    }
 
    public function updatedName (){
        $data = $this->validate(['name' => ['required', 'string', 'max:255']]);
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

    public function store (){
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['required', 'unique:users', 'numeric'],
            'password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]
        ]);

        User::create([
            'name' => $name,
            'email' => $email,
            'telefono' => $telefono,
            'password' => Hash::make($password)
        ]);
    }
}
 