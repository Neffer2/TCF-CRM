<?php

namespace App\Http\Livewire\LiderProduccion\Ordenes\OrdenesCompra;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Traits\Email;
use App\Traits\SMS;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $this->cod_oc = $this->queriedOrden->codigoOC();
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
        // Si intentan aprobar (estado 3), validamos si tiene o no evidencias
        if ($estado == 3) {
            abort_unless((int) auth()->user()->rol === 6, 403);
            $this->resetErrorBag('customError');
            $this->queriedOrden->refresh();

            if ($this->queriedOrden->evidencias()->exists()) {
                try {
                    $this->queriedOrden->update([
                        'estado_id' => 9,
                        'fecha_aprobacion' => now(),
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                    $this->addError('customError', 'No se pudo enviar la orden a revisión del Controller. Intenta de nuevo.');
                    return;
                }

                return redirect()->route('orden-compra')->with(
                    'success',
                    'Las evidencias de la orden ' . $this->queriedOrden->id . ' fueron aprobadas y enviadas al Controller.'
                );
            }

            $ruta = null;
            try {
                DB::transaction(function () use (&$ruta) {
                    $orden = OrdenCompra::lockForUpdate()->findOrFail($this->queriedOrden->id);
                    abort_unless((int) $orden->estado_id === 8, 403);
                    $orden->fecha_aprobacion = now();
                    $orden->registrarAprobadorFormato('lider_produccion', auth()->user());
                    $tieneEvidencias = $orden->evidencias()->exists();
                    $orden->estado_id = $tieneEvidencias ? 9 : 3;
                    if (!$tieneEvidencias) {
                        $orden->cod_oc = $orden->codigoOC();
                    }

                    if (!$tieneEvidencias) {
                        $orden->load(['naturalInfo.tercero', 'ordenItems.itemPresupuesto.presupuesto']);
                        $pdf = new Dompdf(['enable_remote' => true]);
                        $pdf->loadHtml(view('exports.orden_compra_pdf', [
                            'orden' => $orden,
                            'esBorrador' => false,
                        ])->render());
                        $pdf->setPaper('A4', 'portrait');
                        $pdf->render();
                        $ruta = 'public/ordenes_naturales_helisa/orden_natural_' . $orden->id . '_' . uniqid() . '.pdf';
                        if (!Storage::put($ruta, $pdf->output())) {
                            throw new \RuntimeException('No se pudo guardar el PDF de la OC.');
                        }
                        $orden->archivo_orden_helisa = $ruta;
                    }
                    $orden->save();
                    $this->queriedOrden = $orden;
                    $this->cod_oc = $orden->cod_oc;
                });
            } catch (\Throwable $e) {
                if ($ruta) {
                    Storage::delete($ruta);
                }
                report($e);
                $this->addError('customError', 'No se pudo aprobar y guardar la OC. Intenta de nuevo.');
                return;
            }
            
            if (method_exists($this, 'ocNaturalRevisionContabilidad')) {
                $this->ocNaturalRevisionContabilidad($this->queriedOrden);
            }

            $mensaje = $this->queriedOrden->estado_id == 3
                ? 'La orden ' . $this->queriedOrden->id . ' ha sido APROBADA' 
                : 'La orden ' . $this->queriedOrden->id . ' pasó a revisión por tener evidencias.';

            return redirect()->route('orden-compra')->with('success', $mensaje);

        } elseif ($estado == 12) {
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->queriedOrden->justificacion_rechazo = $this->justificacion_rechazo;
            
            if (method_exists($this, 'oc_evidencias_rechazadas')) {
                $this->oc_evidencias_rechazadas($this->queriedOrden);
            }

            $this->queriedOrden->estado_id = $estado;
            $this->queriedOrden->update();

            return redirect()->route('orden-compra')->with('error', 'La orden de compra ' . $this->queriedOrden->id . ' ha sido RECHAZADA y devuelta al productor');
        }
    }
    
    public function render(){
        return view('livewire.lider-produccion.Ordenes.ordencompras.orden-compra-natural');
    } 
}
