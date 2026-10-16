<?php

namespace App\Http\Livewire\Admin\Produccion\OrdenesCompra\Ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Traits\Email;
use Illuminate\Support\Facades\Auth;

class Juridica extends Component{

    use Email;

    // Estados (ajusta a tu tabla; deben ser los mismos que en Natural)
    const ESTADO_RECHAZADA       = 3;
    const ESTADO_APROBADA        = 3;
    const ESTADO_PEND_CONTROLLER = 9;
    const ESTADO_PEND_LIDER      = 10;
    const ESTADO_PEND_GERENCIA   = 11;

    // Umbrales
    const UMBRAL_CONTROLLER = 2000000;   // total OC > 2M pasa a líder comercial
    const UMBRAL_LIDER      = 10000000;  // anticipo > 10M pasa a gerencia

    // ===== TEMPORAL: IDs de usuario por rol (quitar al normalizar roles) =====
    const IDS_CONTROLLER = [2];
    const IDS_LIDER      = [9];
    const IDS_GERENCIA   = [10];
    // ========================================================================

    // Recibe el objeto OrdenCompra que pasa el componente padre / vista
    public $orden_compra;

    public $proveedor_nombre;
    public $justificacion_rechazo;
    public $observaciones_negociacion;

    public $ocItems = [];
    public $totalOrden = 0;

    public $puedeAprobar = false;

    public function mount($orden_compra = null)
    {
        if ($orden_compra) {
            $this->orden_compra = $orden_compra;
            $this->loadOrdenData();
            $this->puedeAprobar = $this->esMiTurno();
        }
    }

    public function loadOrdenData()
    {
        // Relación proveedor
        $this->proveedor_nombre = $this->orden_compra->proveedor->tercero ?? 'N/A';
        $this->justificacion_rechazo = $this->orden_compra->justificacion_rechazo;
        $this->observaciones_negociacion = $this->orden_compra->observaciones_negociacion;

        // Cargar los ítems de la orden
        foreach ($this->orden_compra->ordenItems as $item) {
            $subtotal = $item->vtotal_oc;
            $this->totalOrden += $subtotal;

            array_push($this->ocItems, [
                'num_item' => $item->num_item,
                'desc' => $item->desc_oc,
                'cant' => $item->cant_oc,
                'dias' => $item->dias_oc,
                'otros' => $item->otros_oc,
                'vUnit' => $item->vunit_oc,
                'vTotal' => $subtotal
            ]);
        }
    }

    /** Rol del usuario autenticado (único punto a reemplazar al normalizar) */
    protected function getRolUsuario(): ?string
    {
        $id = Auth::id();

        if (in_array($id, self::IDS_CONTROLLER)) return 'controller';
        if (in_array($id, self::IDS_LIDER))      return 'lider_comercial';
        if (in_array($id, self::IDS_GERENCIA))   return 'gerencia';

        return null;
    }

    /** ¿El usuario logueado es quien debe actuar según el estado actual? */
    protected function esMiTurno(): bool
    {
        $estado = $this->orden_compra->estado_id;

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
        $total = $this->orden_compra->total_oc;

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

    // $accion: 'aprobar' | 'rechazar'
    public function cambiarEstado($accion)
    {
        // Recargar para no actuar sobre un estado desactualizado
        $this->orden_compra->refresh();

        if (!$this->esMiTurno()) {
            abort(403, 'No tienes permiso o no es tu turno para gestionar esta orden.');
        }

        if ($accion === 'aprobar') {
            $nuevoEstado = $this->siguienteEstadoAprobacion();

            if ($nuevoEstado === self::ESTADO_APROBADA) {
                $this->orden_compra->fecha_aprobacion = now();
                $titulo  = '¡Aprobada!';
                $mensaje = 'Orden de compra APROBADA con éxito.';
            } else {
                $titulo  = '¡Aprobada!';
                $mensaje = 'Orden aprobada y enviada a la siguiente instancia de aprobación.';
                // Aquí puedes notificar al siguiente aprobador (Email)
            }
            $icon = 'success';

        } elseif ($accion === 'rechazar') {
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->orden_compra->justificacion_rechazo = $this->justificacion_rechazo;

            $nuevoEstado = self::ESTADO_RECHAZADA; // editable nuevamente por el productor
            $titulo  = '¡Rechazada!';
            $mensaje = 'Orden de compra RECHAZADA.';
            $icon    = 'error';
        } else {
            abort(400);
        }

        $this->orden_compra->estado_id = $nuevoEstado;
        $this->orden_compra->update();

        // Redirección con sesión flash compatible con Livewire v2
        session()->flash('swal_title', $titulo);
        session()->flash('swal_msg', $mensaje);
        session()->flash('swal_icon', $icon);

        return redirect('/orden-compra');
    }

    public function render(){
        return view('livewire.admin.ordenescompra.ordenes.orden-juridica');
    }

}