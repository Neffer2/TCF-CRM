<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;


class UpdateProfile extends Component
{
    use WithFileUploads;

    public $storedUserData = '';

    // Models
    public $name = '';
    public $email = '';
    public $telefono = '';
    public $avatar = '';
    public $password = '';
    public $passwordConfirmation = ''; 

    // OTHER  VARS
    public $random_pass = '';

    public function render()
    {
        return view('livewire.update-profile');
    }

    public function mount (){
        $this->storedUserData = User::where('id', Auth::user()->id)->first();

        $this->name = $this->storedUserData->name;
        $this->email = $this->storedUserData->email;
        $this->telefono = $this->storedUserData->telefono;
        $this->avatar = $this->storedUserData->avatar;
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

    public function random_pass (){
        $this->random_pass = bin2hex(openssl_random_pseudo_bytes(4));
        $this->password = $this->random_pass;
        $this->passwordConfirmation = $this->random_pass;
    }

    public function update (){
        $this->validate([ 
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telefono' => ['required', 'numeric'],
            // 'avatar' => ['mimes:jpeg,jpg,png,gif|max:1000'],
            'password' => ['same:passwordConfirmation', Rules\Password::defaults()]
        ]);

        $user = User::where('id', Auth::user()->id)->first();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->telefono = $this->telefono;
        
        if ($this->avatar != $user->avatar){
            $user->avatar = $this->avatar->store('public/photos');
        }

        $user->telefono = $this->telefono; 
        if ($this->password != ''){
            $user->password = Hash::make($this->password);
        }

        $user->update();

        if (Auth::user()->rol == 1){
            return redirect()->route('actualizar-perfil-adm')->with('success', '¡Datos actualizados axitosamente!');
        }elseif (Auth::user()->rol == 2) {
            return redirect()->route('actualizar-perfil-com')->with('success', '¡Datos actualizados axitosamente!');
        }elseif (Auth::user()->rol == 3) {
            return redirect()->route('actualizar-perfil-con')->with('success', '¡Datos actualizados axitosamente!');
        }
    }
}
 