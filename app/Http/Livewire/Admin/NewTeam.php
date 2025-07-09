<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

/**
 * Componente Livewire para crear nuevos miembros del equipo
 * Permite registrar usuarios con validación en tiempo real
 */
class NewTeam extends Component
{
    // PROPIEDADES DEL MODELO DE USUARIO
    public $name = '';              // Nombre del usuario
    public $email = '';             // Correo electrónico único
    public $telefono = '';          // Número de teléfono único
    public $password = '';          // Contraseña del usuario
    public $rol = '';               // ID del rol asignado
    public $passwordConfirmation = ''; // Confirmación de contraseña

    // VARIABLES AUXILIARES
    public $rolList = '';           // Lista de roles disponibles
    public $random_pass = '';       // Contraseña generada aleatoriamente

    /**
     * Renderiza la vista del componente
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.new-team');
    }

    /**
     * Valida el nombre en tiempo real cuando se actualiza
     * Valida que sea requerido, string y máximo 255 caracteres
     */
    public function updatedName (){
        $data = $this->validate(['name' => ['required', 'string', 'max:255']]);
    }

    /**
     * Valida el email en tiempo real cuando se actualiza
     * Valida formato de email, unicidad en la base de datos y máximo 255 caracteres
     */
    public function updatedEmail (){
        $this->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:users']]);
    }

    /**
     * Valida el teléfono en tiempo real cuando se actualiza
     * Valida que sea requerido, único y numérico
     */
    public function updatedTelefono (){
        $this->validate(['telefono' => ['required', 'unique:users', 'numeric']]);
    }

    /**
     * Valida la contraseña en tiempo real cuando se actualiza
     * Verifica que coincida con la confirmación y cumpla las reglas de seguridad
     */
    public function updatedPassword (){
        $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }

    /**
     * Valida la confirmación de contraseña en tiempo real
     * Verifica que coincida con la contraseña principal
     */
    public function updatedPasswordConfirmation (){
        $this->validate(['password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]]);
    }

    /**
     * Valida la selección de rol en tiempo real
     * Verifica que se haya seleccionado un rol
     */
    public function updatedRol(){
        $this->validate(['rol' => ['required']]);
    }

    /**
     * Inicializa el componente cuando se monta
     * Carga la lista de roles disponibles desde la base de datos
     */
    public function mount (){
        $this->rolList = Rol::all();
    }

    /**
     * Genera una contraseña aleatoria de 8 caracteres
     * Actualiza tanto la contraseña como su confirmación
     */
    public function random_pass (){
        $this->random_pass = bin2hex(openssl_random_pseudo_bytes(4));
        $this->password = $this->random_pass;
        $this->passwordConfirmation = $this->random_pass;
    }

    /**
     * Almacena un nuevo usuario en la base de datos
     * Valida todos los campos, crea el usuario y redirige con mensaje de éxito
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store (){
        // Validación completa de todos los campos del formulario
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['required', 'unique:users', 'numeric'],
            'rol' => ['required'],
            'password' => ['required', 'same:passwordConfirmation', Rules\Password::defaults()]
        ]);

        // Creación del nuevo usuario con los datos validados
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'rol' => $this->rol,
            'password' => Hash::make($this->password) // Hash de la contraseña por seguridad
        ]);

        // Redirección con mensaje de éxito
        return redirect()->route('mi-equpo')->with('success', '¡Nuevo integrante añadido exitosamente!');
    }

}
