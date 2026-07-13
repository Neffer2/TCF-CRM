<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use App\Http\Livewire\Com\GestionComercial\Clientes\Cliente;

use App\Models\clientes;
use App\Models\Año;
use App\Models\SolicitudCliente;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ValidacionesCliente extends Component
{
    public $año;
    public $cod_cc;
    public $cod_cliente;
    public $fecha = 'desc';


    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render(){

        // Filtros base: solo cliente con código de centro de costo
        $filtros = [];

        // Filtro por año si se especifica
        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Validamos si el usuario es Lider Comercial
        if (Auth::user()->comerciales()->exists()) {
            array_push($filtros, ['estado_id', 4]);

            // Obtenemos los ids de los comerciales asignados al Lider
            $comerciales_id = Auth::user()->comerciales()->pluck('users.id');

            // Obtenemos el listado de cliente
            $clientes = Cliente::where($filtros)
                ->whereHas('gestion', function ($query) use ($comerciales_id) {
                    $query->whereIn('id_user', $comerciales_id);
                })
                ->orderBy('id', $this->fecha)
                ->paginate(15);
        }
        else {
            $clientes = [];
        }

        $solicitudes = SolicitudCliente::with('comercial')
            ->when($this->cod_cliente, function($query) {
                $query->where('nombre_cliente', 'like', "%{$this->cod_cliente}%")
                    ->orWhere('apellido_cliente', 'like', "%{$this->cod_cliente}%");
            })
            ->orderBy('created_at', $this->fecha)
            ->paginate(15);

        return view('livewire.admin.gestion-comercial.validaciones-cliente', [
            'solicitudes' => $solicitudes,
            'clientes' => $clientes,
            'añosList' => Año::all(),
            'estadosList' => $this->getEstados()]);
    }

    public function aprobarSolicitud($solicitudId)
    {
        try {
            DB::beginTransaction();

            $solicitud = SolicitudCliente::findOrFail($solicitudId);

            $ultimoCliente = clientes::latest('id')->first();
            $siguienteNumero = $ultimoCliente ? ($ultimoCliente->id + 1) : 1;
            $codigoOficial = 'CLI-' .date('Y') . '-' .str_pad($siguienteNumero, 3, '0', STR_PAD_LEFT);

            $datosEmpresa = json_decode($solicitud->datos_empresa, true);
            $razonSocial = $datosEmpresa ['razon_social'] ?? 'Sin razon Social';

            $clienteNuevo = new clientes();
            $clienteNuevo->codigocliente = $codigoOficial;
            $clienteNuevo->tipocliente = $solicitud->tipo_cliente;
            $clienteNuevo->nombreCliente = $solicitud->nombre_cliente;
            $clienteNuevo->apellidoCliente = $solicitud->apellido_cliente;
            $clienteNuevo->razonCliente = $razonSocial;
            $clienteNuevo->direccionCliente = $solicitud->direccion_cliente;
            $clienteNuevo->telefonoCliente = $solicitud->telefono_cliente;
            $clienteNuevo->emailCliente = $solicitud->email_cliente;
            $clienteNuevo->descripcionCliente = $solicitud->descripcion_cliente;
            $clienteNuevo->id_user = $solicitud->id_user;
            $clienteNuevo->estadoCliente = 1;
            $clienteNuevo->save();

            $solicitud->update([
                'estado' => 'Aprobado'
            ]);

            DB::commit();

            session()->flash('success', "Solicitud Aprobada el codigo de cliente es: {$codigoOficial}");
        }
        catch (\Exception $e) {
            DB::rollBack();
            $this->addError('Error de aprobación', 'Error al procesar la aprobacion' . $e->getMessage());
        }
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
        return clientes::select('id', 'DescripcionCliente')->get();
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
