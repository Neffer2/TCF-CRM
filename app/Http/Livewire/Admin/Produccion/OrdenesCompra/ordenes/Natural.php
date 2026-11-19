<?php

namespace App\Http\Livewire\Admin\Produccion\OrdenesCompra\ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Traits\Email;
use App\Traits\SMS;
use Illuminate\Support\Facades\Auth;





class Natural extends Component{

use SMS, Email;
    use \App\Traits\GestionaEvidenciasNatural;

    // Estados (ajusta a tu tabla)
    const ESTADO_RECHAZADA       = 12;
    const ESTADO_APROBADA        = 3;
    const ESTADO_PEND_CONTROLLER = 9;
    const ESTADO_PEND_LIDER      = 10;
    const ESTADO_PEND_GERENCIA   = 11;

    // Umbrales
    const UMBRAL_CONTROLLER = 2000000;   // total OC > 2M pasa a líder comercial
    const UMBRAL_LIDER      = 5000000;  // anticipo > 5M pasa a gerencia

    // ===== TEMPORAL: IDs de usuario por rol (quitar al normalizar roles) =====
    const IDS_CONTROLLER = [2, 208, 197, 214, 145, 181, 210, 206];
    const IDS_LIDER      = [192, 9, 17];
    const IDS_GERENCIA   = [10, 8];

    public $puedeAprobar;

    public $orden_id;
    public $queriedOrden;
    
    // Datos del Tercero
    public $nombre, $apellido, $correo, $cedula, $telefono, $ciudad, $banco;
    public $productor_nombre;

    // Colección de ítems y totales
    public $items;
    public $totalOrden = 0;

    // Control de aprobación / rechazo
    public $justificacion_rechazo;
    public $justificacion_productor;
    public $cod_oc;
    public $gr;
    public $toggleRechazo = false;

    public function mount($orden_id = null)
    {
        $this->items = collect();

        if ($orden_id) {
            $this->orden_id = $orden_id;
            $this->queriedOrden = OrdenCompra::find($orden_id);

            if ($this->queriedOrden) {
                $this->loadOrdenData();
                $this->gr = $this->queriedOrden->codigoGR();
                $this->puedeAprobar = $this->esMiTurno();

                // Precarga el código secuencial si la orden aún no lo tiene registrado
                if (empty($this->queriedOrden->cod_oc)) {
                    $this->cod_oc = $this->queriedOrden->codigoOC();
                }
            }
        }
    }

    protected function getRolUsuario(): ?string
    {
        $id = Auth::id();

        if (in_array($id, self::IDS_CONTROLLER)) return 'controller';
        if (in_array($id, self::IDS_LIDER))      return 'lider_comercial';
        if (in_array($id, self::IDS_GERENCIA))   return 'gerencia';

        return null;
    }

    protected function esMiTurno(): bool
    {
        $estado = $this->queriedOrden->estado_id;

        switch ($this->getRolUsuario()) {
            case 'controller':      return $estado == self::ESTADO_PEND_CONTROLLER;
            case 'lider_comercial': return $estado == self::ESTADO_PEND_LIDER;
            case 'gerencia':        return $estado == self::ESTADO_PEND_GERENCIA;
            default:                return false;
        }
    }

    protected function siguienteEstadoAprobacion(): int
    {
        // Se recalcula desde BD, no desde la propiedad pública de Livewire
        $total = $this->queriedOrden->total_oc;

        switch ($this->getRolUsuario()) {
            case 'controller':
                return $total > self::UMBRAL_CONTROLLER
                    ? self::ESTADO_PEND_LIDER
                    : self::ESTADO_APROBADA;

            case 'lider_comercial':
                return $total > self::UMBRAL_LIDER
                    ? self::ESTADO_PEND_GERENCIA
                    : self::ESTADO_APROBADA;

            default: // gerencia
                return self::ESTADO_APROBADA;
        }
    }

    // Carga los datos replicando la estructura exacta del array de tus métodos originales
    public function loadOrdenData()
    {
        // 1. Cargar información de NaturalInfo
        $naturalInfo = $this->queriedOrden->naturalInfo;

        if ($naturalInfo) {
            $this->productor_nombre = $naturalInfo->productor->name ?? 'N/A';
            
            $tercero = $naturalInfo->tercero;
            if ($tercero) {
                $this->nombre = $tercero->nombre;
                $this->apellido = $tercero->apellido;
                $this->correo = $tercero->correo;
                $this->cedula = $tercero->cedula;
                $this->telefono = $tercero->telefono;
                $this->ciudad = $tercero->ciudad;
                $this->banco = $tercero->banco;
            }
        }

        $this->justificacion_rechazo = $this->queriedOrden->justificacion_rechazo;
        $this->justificacion_productor = $this->queriedOrden->justificacion_productor;
        $this->cod_oc = $this->queriedOrden->cod_oc;

        // 2. Cargar ítems (Usando tu lógica original exacta de getData)
        $queriedItems = $this->queriedOrden->ordenItems;

        foreach ($queriedItems as $item) {
            $itemPresto = $item->itemPresupuesto; // Relación en OcItem
            $presto = ($itemPresto && $itemPresto->presto) ? $itemPresto->presto : null;
            $gestion = ($presto && $presto->gestion) ? $presto->gestion : null;

            $subtotal = $item->vtotal_oc;
            $this->totalOrden += $subtotal;

            $newItem = [
                'proyecto' => [
                    'id' => $presto ? $presto->id : null,
                    'nombre' => $gestion ? $gestion->nom_proyecto_cot : 'Proyecto no encontrado',
                    'cod_cc' => $presto ? $presto->cod_cc : 'N/A'
                ],
                'item' => [
                    'id' => $itemPresto ? $itemPresto->id : null,
                    'nombre' => $item->desc_oc ?? ($itemPresto ? $itemPresto->descripcion : 'N/A'),
                    'cod_cc' => $presto ? $presto->cod_cc : 'N/A',
                    'display_item' => $itemPresto ? $itemPresto->num_item : 'N/A'
                ],
                'tipo_servicio' => $item->tipo_servicio,
                'tipo_contrato' => $item->tipo_contrato,
                'cant' => $item->cant_oc,
                'dias' => $item->dias_oc,
                'otros' => $item->otros_oc,
                'valor_unitario' => $item->vunit_oc,
                'valor_total' => $subtotal,
            ];

            $this->items->push($newItem);
        }
    }

    // Cambiar estado: Aprobar (3) o Rechazar (7) // Si tiene evidencias (5)
    public function cambiarEstado($accion)
    {
        // Recargar la orden para no actuar sobre un estado desactualizado
        $this->queriedOrden->refresh();
        
        if (!$this->esMiTurno()) {
            abort(403, 'No tienes permiso o no es tu turno para gestionar esta orden.');
        }

        if ($accion === 'rechazar') {
            $this->validate(['justificacion_rechazo' => 'required|string|max:1000']);
            $this->queriedOrden->justificacion_rechazo = $this->justificacion_rechazo;
            $this->queriedOrden->estado_id = self::ESTADO_RECHAZADA;
            $this->queriedOrden->save();

            return redirect()->route('aprobaciones-ordenes-compra')
                ->with('success', 'Orden de compra natural RECHAZADA.');
        }

        abort_unless($accion === 'aprobar', 400);
    
        $tieneEvidencias = $this->queriedOrden->evidencias && $this->queriedOrden->evidencias->isNotEmpty();

        if ($accion === 'aprobar') {
            
            if ($tieneEvidencias) 
            {
                $this->queriedOrden->estado_id = 5;
                $this->queriedOrden->update();

                return redirect()->route('aprobaciones-ordenes-compra')->with('success', 'Las evidencias de la orden ' . $this->queriedOrden->id . ' han sido APROBADAS');
            }

            if ($accion === 'aprobar')
            {
                $this->validate(['cod_oc' => 'nullable|string|max:200']);

                $nuevoEstado = $this->siguienteEstadoAprobacion();

                if ($nuevoEstado === self::ESTADO_APROBADA) 
                {
                    $this->queriedOrden->estado_id = $nuevoEstado;
                    $this->queriedOrden->save();

                    $this->ocNaturalRevisionContabilidad($this->queriedOrden);
                    return redirect()->route('aprobaciones-ordenes-compra')->with('success', 'Orden de compra #' . $this->queriedOrden->id . ' aprobada');

                } else {
                    // Si pasa a una instancia intermedia (ej. líder comercial o gerencia)
                    $this->queriedOrden->estado_id = $nuevoEstado;
                    $this->queriedOrden->update();
                    $mensaje = 'Orden aprobada y enviada a la siguiente instancia de aprobación.';
                }

            }

            $this->queriedOrden->estado_id = $nuevoEstado;
            $this->queriedOrden->update();

            return redirect()->route('aprobaciones-ordenes-compra')->with('success', $mensaje);
        }
    }

    public function render(){
        return view('livewire.admin.ordenescompra.ordenes.orden-natural');
    }

}
