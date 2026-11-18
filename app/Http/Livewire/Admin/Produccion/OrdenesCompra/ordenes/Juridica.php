<?php

namespace App\Http\Livewire\Admin\Produccion\OrdenesCompra\ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Traits\Email;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Juridica extends Component{

    use Email;

    // Estados (ajusta a tu tabla; deben ser los mismos que en Natural)
    const ESTADO_RECHAZADA       = 3;
    const ESTADO_APROBADA        = 1;
    const ESTADO_PEND_CONTROLLER = 9;
    const ESTADO_PEND_LIDER      = 10;
    const ESTADO_PEND_GERENCIA   = 11;
    const ESTADO_REVISON_GR = 4;

    // Umbrales
    const UMBRAL_CONTROLLER = 5000000;   // total OC > 5M pasa a líder comercial
    const UMBRAL_LIDER      = 10000000;  // anticipo > 10M pasa a gerencia

    // ===== TEMPORAL: IDs de usuario por rol (quitar al normalizar roles) =====
    const IDS_CONTROLLER = [2, 208, 197, 214, 145, 181, 210, 206];
    const IDS_LIDER      = [192, 9, 17];
    const IDS_GERENCIA   = [10, 8];
    // ========================================================================

    // Recibe el objeto OrdenCompra que pasa el componente padre / vista
    public $orden_compra;
    public $cod_oc;

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
            $this->cod_oc = $this->orden_compra->cod_oc ?: $this->orden_compra->codigoOC();
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
                'justificacion_productor' => $item->justificacion_productor ?? '',
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

        if ((int) $estado === self::ESTADO_REVISON_GR && $this->orden_compra->archivo_remision) {
            return (int) Auth::user()->rol === 1;
        }

        switch ($this->getRolUsuario()) {
            case 'controller':      return $estado == self::ESTADO_PEND_CONTROLLER;
            case 'lider_comercial': return $estado == self::ESTADO_PEND_LIDER;
            case 'gerencia':        return $estado == self::ESTADO_PEND_GERENCIA;
            default:                return false;
        }
    }

    protected function siguienteEstadoAprobacion(): int
    {
        if($this->orden_compra && !empty($this->orden_compra->archivo_remision)){
            return self::ESTADO_REVISON_GR;
        }
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
            if ($this->orden_compra->archivo_remision) {
                return $this->aprobarRemision();
            }
            $nuevoEstado = $this->siguienteEstadoAprobacion();

            if ($nuevoEstado === self::ESTADO_APROBADA) {
                $soloAprobada = false;

                try {
                    DB::transaction(function () use ($nuevoEstado, &$soloAprobada) {
                        $orden = OrdenCompra::lockForUpdate()->findOrFail($this->orden_compra->id);

                        if (!empty($orden->cod_oc)) {
                            $orden->estado_id        = $nuevoEstado;
                            $orden->save();

                            $this->orden_compra = $orden;
                            $soloAprobada = true;
                            return;
                        }

                        $orden->load([
                            'proveedor',
                            'presupuesto.gestion.contacto',
                            'presupuesto.presupuestoItems',
                            'ordenItems',
                        ]);

                        $orden->cod_oc = $orden->codigoOC();
                        $dompdf = new Dompdf(['enable_remote' => true]);
                        $html = View::make('exports.orden_compra_pdf', [
                            'orden' => $orden,
                        ])->render();

                        $dompdf->loadHtml($html);
                        $dompdf->setPaper('A4', 'portrait');
                        $dompdf->render();

                        $pdfOutput = $dompdf->output();

                        $nombreArchivo = 'orden_compra_' . $orden->id . '_' . time() . '.pdf';
                        $rutaAlmacenamiento = 'public/ordenes_compra/' . $nombreArchivo;

                        Storage::put($rutaAlmacenamiento, $pdfOutput);

                        $orden->archivo_orden_helisa = $rutaAlmacenamiento;
                        $orden->estado_id            = $nuevoEstado;
                        $orden->save();

                        $this->orden_compra = $orden;
                    });
                } catch (\Throwable $e) {
                    $this->addError('customError', $e->getMessage());
                    return;
                }

                $mensaje = $soloAprobada
                    ? 'Orden de compra APROBADA con éxito.'
                    : 'Orden de compra APROBADA con éxito, código ' . $this->orden_compra->cod_oc . ' asignado y PDF guardado.';

            } else {
                $this->orden_compra->estado_id = $nuevoEstado;
                $this->orden_compra->update();

                $mensaje = 'Orden aprobada y enviada a la siguiente instancia de aprobación.';
            }

        } elseif ($accion === 'rechazar') {
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:1000',
            ]);

            $this->orden_compra->justificacion_rechazo = $this->justificacion_rechazo;
            $this->orden_compra->estado_id = self::ESTADO_RECHAZADA;
            $this->orden_compra->update();

            $mensaje = 'Orden de compra RECHAZADA.';
        } else {
            abort(400);
        }

        return redirect()->route('aprobaciones-ordenes-compra')->with('success', $mensaje);
    }

    protected function aprobarRemision()
    {
        $ruta = null;
        try {
            DB::transaction(function () use (&$ruta) {
                $orden = OrdenCompra::lockForUpdate()->findOrFail($this->orden_compra->id);
                $this->orden_compra = $orden;
                abort_unless($this->esMiTurno() && $orden->archivo_remision, 403);
                $orden->gr = $orden->codigoGR();
                $pdf = new Dompdf(['enable_remote' => true]);
                $pdf->loadHtml(View::make('exports.good_recive_pdf', ['orden' => $orden])->render());
                $pdf->setPaper('A4', 'portrait');
                $pdf->render();
                $ruta = 'public/good_receives/good_receive_' . $orden->id . '_' . uniqid() . '.pdf';
                if (!Storage::put($ruta, $pdf->output())) {
                    throw new \RuntimeException('No se pudo guardar el PDF del GR.');
                }
                $orden->archivo_gr = $ruta;
                $orden->estado_id = 5;
                $orden->save();
            });
        } catch (\Throwable $e) {
            if ($ruta) {
                Storage::delete($ruta);
            }
            report($e);
            $this->addError('customError', 'No se pudo generar el GR. Intenta de nuevo.');
            return;
        }

        $this->mailGrGenerado($this->orden_compra);
        return redirect()->route('aprobaciones-ordenes-compra')
            ->with('success', '' . $this->orden_compra->gr . ' generado correctamente.');
    }

    public function render(){
        return view('livewire.admin.ordenescompra.ordenes.orden-juridica');
    }

}
