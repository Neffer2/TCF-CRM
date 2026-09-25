<?php

namespace App\Http\Livewire\Admin\GestionComercial;


use App\Models\clientes;
use App\Models\Año;
use App\Models\SolicitudCliente;
use App\Models\cliente_parametros_cc;

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
        // Antes de escribir nada: que no esté ya aprobada, que el NIT no exista y que haya código de centro de costos.
        $solicitud = SolicitudCliente::find($solicitudId);
        if (!$solicitud || $solicitud->estado === 'Aprobado') {
            $this->addError('error_aprobacion', 'Esta solicitud ya fue aprobada o no existe.');
            return;
        }
        if (clientes::where('nit', $solicitud->nit)->exists()) {
            $this->addError('error_aprobacion', "Ya existe un cliente con el NIT {$solicitud->nit}.");
            return;
        }
        if (clientes::onlyTrashed()->where('nit', $solicitud->nit)->exists()) {
            $this->addError('error_aprobacion', "El cliente con NIT {$solicitud->nit} está en la papelera: restáuralo desde Administración → Papelera en vez de crearlo de nuevo.");
            return;
        }
        $codigoCC = $this->generarCodigoCC($solicitud->nombre);
        if (mb_strlen($codigoCC) > 2) {
            // La columna admite 2 caracteres (letra + dígito) y esa letra ya llegó a 9: hay que definir el formato con Contabilidad.
            $this->addError('error_aprobacion', "No hay código de centro de costos libre para la letra ".mb_substr($codigoCC, 0, 1)
                ." (se agotaron del 1 al 9 y de la A a la Z). Hay que definir con Contabilidad el formato de los siguientes códigos.");
            return;
        }

        try {
            DB::beginTransaction();

            // 1. Encontrar la solicitud origen
            $solicitud = SolicitudCliente::lockForUpdate()->findOrFail($solicitudId);

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


            cliente_parametros_cc::create([
                'cliente_id' => $clienteNuevo->id,
                'nombre_empresa' => $clienteNuevo->razon_social,
                'codigo_cc' => $codigoCC,
            ]);

            // 3. Cambiar el estado de la solicitud en la sala de espera
            $solicitud->update([
                'estado' => 'Aprobado'
            ]);

            DB::commit();

            session()->flash('success', "¡El cliente con NIT {$solicitud->nit} ha sido registrado e inscrito oficialmente!");

        } catch (\Exception $e) {
            DB::rollBack();
            // Antes: dd($e), que tumbaba la pantalla y mostraba el detalle interno
            \Log::error('aprobarSolicitud: '.$e->getMessage(), ['solicitud' => $solicitudId]);
            $this->addError('error_aprobacion', 'No se pudo aprobar la solicitud. Intenta de nuevo o avisa a desarrollo.');
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

    /** Orden de los consecutivos del código: 1-9 y luego A-Z (el código es de 2 caracteres y F9 era el último posible). */
    const SECUENCIA_CC = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private function generarCodigoCC($nombreCliente)
    {
        $nombre = \Illuminate\Support\Str::ascii(trim($nombreCliente));

        // Primera letra (o dígito) del nombre
        $prefijo = strtoupper(substr($nombre, 0, 1)) ?: 'X';

        // Consecutivos ya usados con esa inicial (segundo carácter del código)
        $usados = cliente_parametros_cc::where('codigo_cc', 'LIKE', $prefijo . '_')
            ->pluck('codigo_cc')
            ->map(function ($c) { return strtoupper(substr($c, 1, 1)); })
            ->all();

        foreach (str_split(self::SECUENCIA_CC) as $consecutivo) {
            if (!in_array($consecutivo, $usados, true)) {
                return $prefijo . $consecutivo;
            }
        }
        return $prefijo . '??'; // 3 caracteres: aprobarSolicitud lo detecta y avisa
    }
}
