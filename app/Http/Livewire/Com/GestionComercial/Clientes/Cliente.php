<?php

namespace App\Http\Livewire\Com\GestionComercial\Clientes;

use App\Models\solicitudcliente;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use App\Models\clientes;

class Cliente extends Component
{
    //Model
    public $CodigoCliente;
    public $TipoCliente;
    public $nombreCliente;
    public $apellidoCliente;
    public $RazonSocialCliente;
    public $DireccionCliente;
    public $telefonoCliente;
    public $emailCliente;
    public $DescripcionCliente;

    public function render(){
        return view('livewire.com.gestion-comercial.clientes.nuevo-cliente');
    }

    public function updatedCodigoCliente()
    {
        $this->validate(['CodigoCliente' => ['nullable', 'string']]); // Nullable por RN-006
    }
    public function updatedTipoCliente()
    {
        $this->validate(['TipoCliente' => ['string', 'required']]);
    }
    public function updatedNombreCliente()
    {
        $this->validate(['nombreCliente' => ['string', 'required']]);
    }
    public function updatedApellidoCliente()
    {
        $this->validate(['apellidoCliente' => ['string', 'required']]);
    }
    public function updatedRazonSocialCliente()
    {
        $this->validate(['RazonSocialCliente' => ['string', 'required']]);
    }
    public function updatedDireccionCliente()
    {
        $this->validate(['DireccionCliente' => ['string', 'nullable']]);
    }
    public function updatedTelefonoCliente()
    {
        $this->validate(['telefonoCliente' => ['string', 'nullable']]);
    }
    public function updatedEmailCliente()
    {
        $this->validate(['emailCliente' => ['string', 'required', 'email']]);
    }
    public function updatedDescripcionCliente()
    {
        $this->validate(['DescripcionCliente' => ['string', 'nullable']]);
    }

    public function storage()
    {
        // Validamos usando exactamente los mismos nombres de las propiedades públicas
        $this->validate([
            'CodigoCliente'      => ['nullable', 'string'],
            'TipoCliente'        => ['required', 'string'],
            'nombreCliente'      => ['required', 'string'],
            'apellidoCliente'    => ['required', 'string'],
            'RazonSocialCliente' => ['required', 'string'],
            'DireccionCliente'   => ['nullable', 'string'],
            'telefonoCliente'    => ['nullable', 'string'],
            'emailCliente'       => ['required', 'string', 'email'],
            'DescripcionCliente' => ['nullable', 'string'],
        ]);

        try {

            // RN-001, RN-003 y RN-007: Se crea como solicitud Pendiente guardando la empresa temporalmente
            $solicitud = solicitudcliente::create([
                'id_user'             => Auth::id(), // Comercial (RN-008)
                'tipo_cliente'        => $this->TipoCliente,
                'nombre_cliente'      => $this->nombreCliente,
                'apellido_cliente'    => $this->apellidoCliente,
                'direccion_cliente'   => $this->DireccionCliente,
                'telefono_cliente'    => $this->telefonoCliente,
                'email_cliente'       => $this->emailCliente,
                'descripcion_cliente' => $this->DescripcionCliente,
                'estado'              => 'Pendiente', // RN-003
                'nueva_empresa_datos' => json_encode([
                    'nombre'       => $this->RazonSocialCliente,
                    'razon_social' => $this->RazonSocialCliente,
                ]),
            ]);

            // Notificación limpia al Controller (RN-010 / HU-001)
            $controller = User::where('role', 'Controller')->first();
            if ($controller && method_exists($this, 'mailNuevaSolicitudController')) {
                $this->mailNuevaSolicitudController($solicitud, $controller->email, $controller->name);
            }

            session()->flash('success', 'La solicitud de creación de contacto ha sido enviada al Controller en estado Pendiente.');

            $this->limpiar();
            $this->emit('list');

        } catch (\Exception $e) {
            $this->addError('error_proceso', 'Hubo un fallo al procesar la solicitud: ' . $e->getMessage());
        }
    }

    // 4. Tus mensajes personalizados mapeados a las propiedades correctas
    protected $messages = [
        'TipoCliente.required'        => 'El tipo de cliente es obligatorio.',
        'nombreCliente.required'      => 'El nombre es obligatorio.',
        'apellidoCliente.required'    => 'El apellido es obligatorio.',
        'RazonSocialCliente.required' => 'La razón social de la empresa es obligatoria.',
        'emailCliente.required'       => 'El correo electrónico es obligatorio.',
        'emailCliente.email'          => 'El formato del correo electrónico no es válido.',
    ];

    public function limpiar(){
        $this->CodigoCliente = "";
        $this->TipoCliente = "";
        $this->nombreCliente = "";
        $this->apellidoCliente = "";
        $this->RazonSocialCliente = "";
        $this->DireccionCliente = "";
        $this->telefonoCliente = "";
        $this->emailCliente = "";
        $this->DescripcionCliente = "";
    }
}
