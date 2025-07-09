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
    use WithFileUploads; // Habilita la subida de archivos con Livewire

    // Variable para almacenar los datos actuales del usuario autenticado
    public $storedUserData = '';

    // Modelos para los campos del formulario de perfil
    public $name = '';
    public $email = '';
    public $telefono = '';
    public $avatar = '';
    public $password = '';
    public $passwordConfirmation = '';

    // Variable auxiliar para contraseña aleatoria
    public $random_pass = '';

    // Renderiza la vista principal del componente de perfil
    public function render()
    {
        return view('livewire.update-profile');
    }

    // Al montar el componente, carga los datos actuales del usuario autenticado
    public function mount (){
        $this->storedUserData = User::where('id', Auth::user()->id)->first();

        $this->name = $this->storedUserData->name;
        $this->email = $this->storedUserData->email;
        $this->telefono = $this->storedUserData->telefono;
        $this->avatar = $this->storedUserData->avatar;
    }

    // Valida el campo nombre cuando se actualiza
    public function updatedName (){
        $data = $this->validate(['name' => ['required', 'string', 'max:255']]);
    }

    // Valida el campo email cuando se actualiza
    public function updatedEmail (){
        $this->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:users']]);
    }

    // Valida el campo teléfono cuando se actualiza
    public function updatedTelefono (){
        $this->validate(['telefono' => ['required', 'unique:users', 'numeric']]);
    }

    // Valida el campo contraseña cuando se actualiza
    public function updatedPassword (){
        $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }

    // Valida la confirmación de contraseña cuando se actualiza
    public function updatedPasswordConfirmation (){
        $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }

    // Genera una contraseña aleatoria y la asigna a los campos de contraseña
    public function random_pass (){
        $this->random_pass = bin2hex(openssl_random_pseudo_bytes(4));
        $this->password = $this->random_pass;
        $this->passwordConfirmation = $this->random_pass;
    }

    // Actualiza los datos del usuario autenticado
    public function update (){
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'telefono' => ['required', 'numeric'],
            // 'avatar' => ['mimes:jpeg,jpg,png,gif|max:1000'],
            'password' => ['same:passwordConfirmation', Rules\Password::defaults()]
        ]);

        // Busca el usuario autenticado
        $user = User::where('id', Auth::user()->id)->first();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->telefono = $this->telefono;

        // Si se cambió el avatar, lo almacena
        if ($this->avatar != $user->avatar){
            $user->avatar = $this->avatar->store('public/photos');
        }

        $user->telefono = $this->telefono;
        // Si se ingresó una nueva contraseña, la actualiza
        if ($this->password != ''){
            $user->password = Hash::make($this->password);
        }

        $user->update();

        // Redirige según el rol del usuario con mensaje de éxito
        if (Auth::user()->rol == 1){
            return redirect()->route('actualizar-perfil-adm')->with('success', '¡Datos actualizados axitosamente!');
        }elseif (Auth::user()->rol == 2) {
            return redirect()->route('actualizar-perfil-com')->with('success', '¡Datos actualizados axitosamente!');
        }elseif (Auth::user()->rol == 3) {
            return redirect()->route('actualizar-perfil-con')->with('success', '¡Datos actualizados axitosamente!');
        }elseif (Auth::user()->rol == 5) {
            return redirect()->route('actualizar-perfil-asis')->with('success', '¡Datos actualizados axitosamente!');
        }
    }
}
