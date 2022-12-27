<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class NewTeam extends Component  
{
    // MODELS
    public $name = '';
    public $email = '';
    public $telefono = '';
    public $password = '';
    public $rol = '';
    public $passwordConfirmation = ''; 

    // OTHER VARS
    public $rolList = '';
    public $random_pass = '';

    public function render()
    {
        return view('livewire.admin.new-team');
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

    public function updatedRol(){
        $this->validate(['rol' => ['required']]);
    }

    public function mount (){
        $this->rolList = Rol::all(); 
    }

    public function random_pass (){
        $this->random_pass = bin2hex(openssl_random_pseudo_bytes(4));
        $this->password = $this->random_pass;
        $this->passwordConfirmation = $this->random_pass;
    }

    public function store (){
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['required', 'unique:users', 'numeric'],
            'rol' => ['required'],
            'password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'rol' => $this->rol,
            'password' => Hash::make($this->password)
        ]); 

        return redirect()->route('mi-equpo')->with('success', '¡Nuevo integrante añadido exitosamente!');
    }

} 
 