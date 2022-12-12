<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;


class UpdateProfileCom extends Component
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

    public function render()
    {
        return view('livewire.update-profile-com');
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

    public function update (){
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telefono' => ['required', 'numeric'],
            // 'avatar' => ['mimes:jpeg,jpg,png,gif|max:1000'],
            'password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]
        ]);

        $user = User::where('id', Auth::user()->id)->first();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->telefono = $this->telefono;
        $user->avatar = $this->avatar->store('public/photos');
        $user->telefono = $this->telefono; 
        $user->password = Hash::make($this->password);

        $user->update();

        return redirect()->route('actualizar-perfil-com')->with('success', '¡Datos actualizados axitosamente!');
    }
}
 