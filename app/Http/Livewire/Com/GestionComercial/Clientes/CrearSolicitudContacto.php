<?php

namespace App\Http\Livewire\Com\GestionComercial\Clientes;

use Livewire\Component;
use App\Models\SolicitudCliente;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CrearSolicitudContacto extends Component
{
    use WithFileUploads; // Requerido para procesar el input file (adjuntar_archivos)

    // Las 11 propiedades fijas corporativas requeridas
    public $nombre;
    public $razon_social;
    public $nit;
    public $direccion;
    public $telefono;
    public $numero_telefono;
    public $cargo;
    public $correo;
    public $pagina_web;
    public $correo_recpcion_facturas;
    public $adjuntar_archivos; // Manejará la instancia del archivo cargado temporalmente

    public function render()
    {
        return view('livewire.com.gestion-comercial.clientes.nuevo-cliente');
    }

    // Validaciones en tiempo real para mantener la UX limpia y reactiva en el Blade
    public function updatedNomComercial() {
        $this->validate(['nombre' => ['required', 'string', 'max:255']]);
    }
    public function updatedRazonSocial() {
        $this->validate(['razon_social' => ['required', 'string', 'max:255']]);
    }
    public function updatedNit() {
        $this->validate(['nit' => ['required', 'string', 'max:50']]);
    }
    public function updatedDireccion() {
        $this->validate(['direccion' => ['required', 'string', 'max:255']]);
    }
    public function updatedNumeroTelefono() {
        $this->validate(['numero_telefono' => ['required', 'string', 'max:50']]);
    }
    public function updatedCargo() {
        $this->validate(['cargo' => ['required', 'string', 'max:100']]);
    }
    public function updatedCorreo() {
        $this->validate(['correo' => ['required', 'string', 'email:rfc', 'max:255']]);
    }
    public function updatedCorreoRecpcionFacturas() {
        $this->validate(['correo_recpcion_facturas' => ['required', 'string', 'email:rfc', 'max:255']]);
    }
    public function updatedAdjuntarArchivos() {
        $this->validate(['adjuntar_archivos' => ['nullable', 'file', 'max:10240']]);
    } // Máx 10MB (RUT, Cámara de Comercio, etc.)

    /**
     * Procesa y almacena la nueva solicitud comercial
     */
    public function storage()
    {
        // Validación en bloque antes de ejecutar el insert en BD
        $this->validate([
            'nombre'                   => ['required', 'string', 'max:255'],
            'razon_social'             => ['required', 'string', 'max:255'],
            'nit'                      => ['required', 'string', 'max:50'],
            'direccion'                => ['required', 'string', 'max:255'],
            'telefono'                 => ['nullable', 'string', 'max:50'],
            'numero_telefono'          => ['required', 'string', 'max:50'],
            'cargo'                    => ['required', 'string', 'max:100'],
            'correo'                   => ['required', 'string', 'email:rfc', 'max:255'],
            'pagina_web'               => ['nullable', 'string', 'max:255'],
            'correo_recpcion_facturas' => ['required', 'string', 'email:rfc', 'max:255'],
            'adjuntar_archivos'        => ['nullable', 'file', 'max:10240'],
        ]);

        try {
            DB::beginTransaction();

            // Procesamos el almacenamiento físico del archivo si el comercial cargó uno
            $rutaArchivo = null;
            if ($this->adjuntar_archivos) {
                // Lo guarda en storage/app/public/solicitudes_adjuntos de forma automática y segura
                $rutaArchivo = $this->adjuntar_archivos->store('solicitudes_adjuntos', 'public');
            }
            // Inserción directa en la tabla relacional
            SolicitudCliente::create([
                'id_user'                  => Auth::id(), // Guardamos al comercial solicitante
                'estado'                   => 'Pendiente',
                'nombre'            => $this->nombre,
                'razon_social'             => $this->razon_social,
                'nit'                      => $this->nit,
                'direccion'                => $this->direccion,
                'telefono'                 => $this->telefono,
                'numero_telefono'          => $this->numero_telefono,
                'cargo'                    => $this->cargo,
                'correo'                   => $this->correo,
                'pagina_web'               => $this->pagina_web,
                'correo_recpcion_facturas' => $this->correo_recpcion_facturas,
                'adjuntar_archivos'        => $rutaArchivo,
            ]);

            DB::commit();

            // Despachamos alerta de éxito por sesión para el sweetalert del front
            session()->flash('success', 'La solicitud de contacto corporativo ha sido creada con éxito y enviada a revisión.');

            $this->limpiar();
            $this->emit('list'); // Emite el refresco para los listados si aplica

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('error_proceso', 'No se pudo guardar la solicitud comercial: ' . $e->getMessage());
        }
    }

    /**
     * Limpia el formulario y reinicia los estados del componente
     */
    public function limpiar()
    {
        $this->reset([
            'nombre', 'razon_social', 'nit', 'direccion', 'telefono',
            'numero_telefono', 'cargo', 'correo', 'pagina_web',
            'correo_recpcion_facturas', 'adjuntar_archivos'
        ]);
    }
}


