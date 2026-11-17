<?php

namespace App\Http\Livewire\LiderProduccion\Ordenes\OrdenesCompra;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Traits\Email;

class OrdenJuridica extends Component {

    use Email;

    // Recibe el objeto OrdenCompra que pasa el componente padre / vista
    public $orden_compra;

    
    public $proveedor_nombre;
    public $justificacion_rechazo;
    public $observaciones_negociacion;

    public $ocItems = [];
    public $totalOrden = 0;

    // Se ejecuta al inicializar el componente
    public function mount($orden_compra = null)
    {
        if ($orden_compra) {
            $this->orden_compra = $orden_compra;
            $this->loadOrdenData();
        }
    }

    // Carga los datos de la orden y sus ítems para visualización
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
                'justificacion_productor' => $item->justificacion_productor ?? '',
                'otros' => $item->otros_oc,
                'vUnit' => $item->vunit_oc,
                'vTotal' => $subtotal
            ]);
        }
    }

    // Cambiar estado: Aprobar (9) o Rechazar (3 (editable nuevamente a productor))
    public function cambiarEstado($estado)
    {
        $mensaje = '';

        if ($estado == 9) {
            
            $this->orden_compra->fecha_aprobacion = now();
            
            $mensaje = 'Orden de compra APROBADA con éxito.';

        } elseif ($estado == 3) {
            // RECHAZAR
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->orden_compra->justificacion_rechazo = $this->justificacion_rechazo;
            
            $mensaje = 'Orden de compra RECHAZADA.';
        }

        $this->orden_compra->estado_id = $estado;
        $this->orden_compra->update();

        return redirect()->route('orden-compra')->with('success', $mensaje);
    }

    public function render(){
        return view('livewire.lider-produccion.ordenes.ordencompras.orden-compra-juridico');
    } 
}
