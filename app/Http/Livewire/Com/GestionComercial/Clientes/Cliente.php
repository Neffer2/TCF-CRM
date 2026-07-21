<?php

namespace App\Http\Livewire\Com\GestionComercial\Clientes;

use App\Http\Livewire\Com\GestionComercial\Clientes\CrearSolicitudContacto;
use App\Models\SolicitudCliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use App\Models\clientes;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Cliente extends Component
{
    use WithPagination;
    use WithFileUploads;
    //Model
    public $nombre;
    public $razon_social;
    public $nit;
    public $direccion;
    public $telefono;
    public $numero_telefono;
    public $cargo;
    public $correo;
    public $pagina_web;
    public $correo_recpcion_facturas; // Respetamos el nombre de tu campo de BD
    public $adjuntar_archivos; // Para el manejo de adjuntos temporales

    public function render(){
        return view('livewire.com.gestion-comercial.clientes.nuevo-cliente');
    }

    // 2. Validaciones en tiempo real (Formato: updated + NombreExactoPropiedad)
    public function updatedNombre(){
        $this->validate(['nombre' => ['required', 'string', 'max:255']]);
    }
    public function updatedRazonSocial(){
        $this->validate(['razon_social' => ['required', 'string', 'max:255']]);
    }
    public function updatedNit(){
        $this->validate(['nit' => ['required', 'string', 'max:50']]);
    }
    public function updatedDireccion(){
        $this->validate(['direccion' => ['required', 'string', 'max:255']]);
    }
    public function updatedTelefono(){
        $this->validate(['telefono' => ['nullable', 'string', 'max:50']]);
    }
    public function updatedNumeroTelefono(){
        $this->validate(['numero_telefono' => ['required', 'string', 'max:50']]);
    }
    public function updatedCargo(){
        $this->validate(['cargo' => ['required', 'string', 'max:100']]);
    }
    public function updatedCorreo(){
        $this->validate(['correo' => ['required', 'string', 'email:rfc', 'max:255']]);
    }
    public function updatedPaginaWeb(){
        $this->validate(['pagina_web' => ['nullable', 'string', 'max:255']]);
    }
    public function updatedCorreoRecpcionFacturas(){
        $this->validate(['correo_recpcion_facturas' => ['required', 'string', 'email:rfc', 'max:255']]);
    }

    // 3. Método de guardado adaptado a la nueva matriz de datos corporativos
    public function storage(){
        $this->validate([
            'nombre'            => ['required', 'string', 'max:255'],
            'razon_social'             => ['required', 'string', 'max:255'],
            'nit'                      => ['required', 'string', 'max:50'],
            'direccion'                => ['required', 'string', 'max:255'],
            'telefono'                 => ['nullable', 'string', 'max:50'],
            'numero_telefono'          => ['required', 'string', 'max:50'],
            'cargo'                    => ['required', 'string', 'max:100'],
            'correo'                   => ['required', 'string', 'email:rfc', 'max:255'],
            'pagina_web'               => ['nullable', 'string', 'max:255'],
            'correo_recpcion_facturas' => ['required', 'string', 'email:rfc', 'max:255'],
            'adjuntar_archivos'        => ['nullable'],
        ]);

        try {
            DB::beginTransaction();

            // Mapeamos todo a la sala de espera estructurada

            SolicitudCliente::create([
                'id_user'                  => Auth::id(),
                'estado'                   => 'Pendiente',
                'nombre'                   => $this->nombre,
                'razon_social'             => $this->razon_social,
                'nit'                      => $this->nit,
                'direccion'                => $this->direccion,
                'telefono'                 => $this->telefono,
                'numero_telefono'          => $this->numero_telefono,
                'cargo'                    => $this->cargo,
                'correo'                   => $this->correo,
                'pagina_web'               => $this->pagina_web,
                'correo_recpcion_facturas' => $this->correo_recpcion_facturas,
                'adjuntar_archivos'        => $this->adjuntar_archivos,
            ]);

            DB::commit();

            session()->flash('success', 'La información comercial del cliente ha sido registrada y enviada a validación con éxito.');

            $this->limpiar();
            $this->emit('list');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('error_proceso', 'Fallo al procesar la información: ' . $e->getMessage());
        }
    }

    // 4. Mensajes de error personalizados para los nuevos campos fijos
    protected $messages = [
        'nombre.required'                   => 'El nombre es obligatorio.',
        'razon_social.required'             => 'La razón social de la empresa es obligatoria.',
        'nit.required'                      => 'El NIT es obligatorio para la facturación.',
        'direccion.required'                => 'La dirección de correspondencia es obligatoria.',
        'numero_telefono.required'          => 'El número de teléfono móvil es requerido.',
        'cargo.required'                    => 'El cargo del contacto es obligatorio.',
        'correo.required'                   => 'El correo electrónico es obligatorio.',
        'correo.email'                      => 'El formato del correo electrónico no es válido.',
        'correo_recpcion_facturas.required' => 'El correo de recepción de facturas es obligatorio.',
        'correo_recpcion_facturas.email'    => 'El formato del correo de facturación no es válido.',
    ];

    public function limpiar(){
        $this->nombre = "";
        $this->razon_social = "";
        $this->nit = "";
        $this->direccion = "";
        $this->telefono = "";
        $this->numero_telefono = "";
        $this->cargo = "";
        $this->correo = "";
        $this->pagina_web = "";
        $this->correo_recpcion_facturas = "";
        $this->adjuntar_archivos = null;
    }

}
