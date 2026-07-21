<?php

namespace App\Http\Livewire\Admin\GestionComercial;


use App\Http\Livewire\Com\GestionComercial\Clientes\CrearSolicitudContacto;
use App\Models\clientes;
use App\Models\Año;
use App\Models\SolicitudCliente;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ValidacionesCliente extends Component
{
    use WithPagination;

    public $año;
    public $fecha = 'desc';
    public $buscar;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['refreshTable' => '$refresh'];

    public function aprobarSolicitud($solicitudId)
    {
        try {
            DB::beginTransaction();

            // 1. Encontrar la solicitud origen
            $solicitud = SolicitudCliente::findOrFail($solicitudId);

            // 2. Traspasar los 11 campos fijos de manera robusta usando la instancia del modelo oficial
            $clienteNuevo = new clientes();
            $clienteNuevo->id_user                  = $solicitud->id_user; // Mantiene el dueño comercial original
            $clienteNuevo->estado_id                = 1; // Estado Activo por defecto en la tabla clientes
            $clienteNuevo->nombre                   = $solicitud->nombre;
            $clienteNuevo->razon_social             = $solicitud->razon_social;
            $clienteNuevo->nit                      = $solicitud->nit;
            $clienteNuevo->direccion                = $solicitud->direccion;
            $clienteNuevo->telefono                 = $solicitud->telefono;
            $clienteNuevo->numero_telefono          = $solicitud->numero_telefono;
            $clienteNuevo->cargo                    = $solicitud->cargo;
            $clienteNuevo->correo                   = $solicitud->correo;
            $clienteNuevo->pagina_web               = $solicitud->pagina_web;
            $clienteNuevo->correo_recpcion_facturas = $solicitud->correo_recpcion_facturas;
            $clienteNuevo->adjuntar_archivos        = $solicitud->adjuntar_archivos;
            $clienteNuevo->save();

            // 3. Cambiar el estado de la solicitud en la sala de espera
            $solicitud->update([
                'estado' => 'Aprobado'
            ]);

            DB::commit();

            session()->flash('success', "¡El cliente con NIT {$solicitud->nit} ha sido registrado e inscrito oficialmente!");

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            //$this->addError('error_aprobacion', 'Error al procesar la aprobación: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $solicitudes = SolicitudCliente::with('comercial')
            ->when($this->buscar, function($query) {
                $query->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('razon_social', 'like', "%{$this->buscar}%")
                    ->orWhere('nit', 'like', "%{$this->buscar}%");
            })
            ->orderBy('created_at', $this->fecha)
            ->paginate(15);

        return view('livewire.admin.gestion-comercial.validaciones-cliente', [
            'solicitudes' => $solicitudes
        ]);
    }

    public function mount(){
        $this->getEstados();
        $this->getAños();
    }

    /**
     * Obtiene la lista de estados de presupuesto disponibles
     */
    public function getEstados()
    {
        return clientes::select('id', 'estado_id')->get();
    }

    /**
     * Obtiene la lista de años disponibles
     * Establece el año actual como seleccionado por defecto
     */
    public function getAños(){
        $this->años = Año::all();
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    /**
     * Se ejecuta cuando cambia el año seleccionado
     * Valida el año y obtiene su información detallada
     */
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        // Obtiene la información completa del año incluyendo sus meses
        $this->yearInfo = Año::find($this->año);
    }
}
