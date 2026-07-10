<?php

namespace app\Livewire\Com\GestionComercial\Clientes;

use Livewire\Component;
use App\Models\SolicitudContacto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrearSolicitudContacto extends Component
{
    public $CodigoCliente; // Se mantiene por el binding, pero no se procesa para la creación
    public $TipoCliente;
    public $nombreCliente;
    public $apellidoCliente;
    public $RazonSocialCliente;
    public $DireccionCliente;
    public $telefonoCliente;
    public $emailCliente;
    public $DescripcionCliente;

    // 2. Definir las reglas de validación alineadas a tus campos
    protected function rules()
    {
        return [
            'TipoCliente'        => 'required|string|max:11',
            'nombreCliente'      => 'required|string|max:255',
            'apellidoCliente'    => 'required|string|max:255',
            'RazonSocialCliente' => 'required|string|max:255', // Se asume obligatorio si manejan empresas/razón social
            'DireccionCliente'   => 'nullable|string|max:255',
            'telefonoCliente'    => 'nullable|numeric',
            'emailCliente'       => 'required|email|max:255',
            'DescripcionCliente' => 'nullable|string|max:255',
        ];
    }

    // Mensajes personalizados para SweetAlert u errores tradicionales
    protected $messages = [
        'TipoCliente.required'        => 'El tipo de cliente es obligatorio.',
        'nombreCliente.required'      => 'El nombre es obligatorio.',
        'apellidoCliente.required'    => 'El apellido es obligatorio.',
        'RazonSocialCliente.required' => 'La razón social de la empresa es obligatoria.',
        'emailCliente.required'       => 'El correo electrónico es obligatorio.',
        'emailCliente.email'          => 'El formato del correo electrónico no es válido.',
    ];

    /**
     * Procesa el formulario enviado por wire:submit.prevent="store"
     */
    public function store()
    {
        // Ejecuta la validación reactiva
        $this->validate();

        try {

            // RN-001 & RN-003: Creamos la solicitud en estado 'Pendiente'
            // RN-007: Guardamos los datos de la razón social de la empresa como metadata temporal
            $solicitud = SolicitudContacto::create([
                'id_user'             => Auth::id(), // Comercial que realiza la solicitud (RN-008)
                'tipo_cliente'        => $this->TipoCliente,
                'nombre_cliente'      => $this->nombreCliente,
                'apellido_cliente'    => $this->apellidoCliente,
                'direccion_cliente'   => $this->DireccionCliente,
                'telefono_cliente'    => $this->telefonoCliente,
                'email_cliente'       => $this->emailCliente,
                'descripcion_cliente' => $this->DescripcionCliente,
                'estado'              => 'Pendiente', // RN-003

                // Guardamos la información de la empresa de forma temporal (RN-007)
                'nueva_empresa_datos' => json_encode([
                    'nombre'       => $this->RazonSocialCliente,
                    'razon_social' => $this->RazonSocialCliente,
                ]),
            ]);

            // Buscar al Controller para notificarle de inmediato
            $controller = User::where('role', 'Controller')->first();
            if ($controller) {
                $this->mailNuevaSolicitudController($solicitud, $controller->email, $controller->name);
            }

            // Limpiar el formulario tras el éxito
            $this->reset();

            // Emitimos el mensaje de éxito que captura el script de SweetAlert de tu vista
            session()->flash('success', 'La solicitud de creación de contacto ha sido enviada al Controller en estado Pendiente.');

        } catch (\Exception $e) {
            $this->addError('error_proceso', 'Hubo un fallo al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.com.gestion-comercial.clientes.crear-solicitud-contacto');
    }
}


