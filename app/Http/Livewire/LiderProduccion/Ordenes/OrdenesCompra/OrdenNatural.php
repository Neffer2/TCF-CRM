<?php

namespace App\Http\Livewire\LiderProduccion\Ordenes\OrdenesCompra;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Traits\Email;
use App\Traits\SMS;

class OrdenNatural extends Component
{
    use SMS, Email;

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

    public function mount($orden_id = null)
    {
        $this->items = collect();

        // Si no se recibe por parámetro, intentamos obtenerlo de request o propiedad
        if (!$orden_id && property_exists($this, 'orden_id')) {
            $orden_id = $this->orden_id;
        }

        if ($orden_id) {
            $this->orden_id = $orden_id;
            
            // Buscamos la orden de compra
            $this->queriedOrden = OrdenCompra::find($orden_id);

            if ($this->queriedOrden) {
                $this->loadOrdenData();
            }
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
        $this->cod_oc = $this->queriedOrden->cod_oc;
        $this->justificacion_productor = $this->queriedOrden->justificacion_productor;
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

    // Cambiar estado: Aprobar (9) o Rechazar (12)
    public function cambiarEstado($estado)
    {
        if ($estado == 9) {
            $this->validate([
                'cod_oc' => 'nullable|string|max:200',
            ]);

            $this->queriedOrden->fecha_aprobacion = now();
            $this->queriedOrden->cod_oc = $this->cod_oc;
            
            if (method_exists($this, 'ocNaturalRevisionContabilidad')) {
                $this->ocNaturalRevisionContabilidad($this->queriedOrden);
            }

            $this->queriedOrden->estado_id = $estado;
            $this->queriedOrden->update();

            return redirect()->route('orden-compra')->with('success', 'La orden ' . $this->queriedOrden->id . ' a sido APROBADA y enviada Controller');

        } elseif ($estado == 3) {
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->queriedOrden->justificacion_rechazo = $this->justificacion_rechazo;
            
            if (method_exists($this, 'oc_evidencias_rechazadas')) {
                $this->oc_evidencias_rechazadas($this->queriedOrden);
            }

            $this->queriedOrden->estado_id = $estado;
            $this->queriedOrden->update();

            return redirect()->route('orden-compra')->with('error', 'La orden de compra' . $this->queriedOrden->id . 'a sido RECHAZADA y devuelva al productor');
        }
    }
    
    public function render(){
        return view('livewire.lider-produccion.ordenes.ordencompras.orden-compra-natural');
    } 
}
