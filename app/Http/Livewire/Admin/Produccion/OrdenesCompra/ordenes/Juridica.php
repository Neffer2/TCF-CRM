<?php

namespace App\Http\Livewire\Admin\Produccion\OrdenesCompra\Ordenes;

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
            $this->cod_oc = $this->orden_compra->cod_oc ?: $this->siguienteCodigoOC();
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
        
        /*
        if (!$this->esMiTurno()) {
            abort(403, 'No tienes permiso o no es tu turno para gestionar esta orden.');
        }
        */
        $icon = 'success';

        if ($accion === 'aprobar') {
            $nuevoEstado = $this->siguienteEstadoAprobacion();

            if ($nuevoEstado === self::ESTADO_APROBADA) {
                try {
                    $this->orden_compra->load([
                        'proveedor',
                        'presupuesto.gestion.contacto',
                        'presupuesto.presupuestoItems',
                        'ordenItems',
                    ]);

                    $dompdf = new Dompdf(['enable_remote' => true]);
                    $html = View::make('exports.orden_compra_pdf', [
                        'orden' => $this->orden_compra,
                    ])->render();

                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    $pdfOutput = $dompdf->output();

                    // Definir ruta y nombre del archivo
                    $nombreArchivo = 'orden_compra_' . ($this->orden_compra->id) . '_' . time() . '.pdf';
                    $rutaAlmacenamiento = 'public/ordenes_compra/' . $nombreArchivo;

                    // Guardar en Storage
                    Storage::put($rutaAlmacenamiento, $pdfOutput);

                } catch (\Throwable $e) {
                    $this->addError('customError', 'Error al generar el PDF: ' . $e->getMessage());
                    return;
                }
                try {
                    $this->conLockDB('oc_consecutivo', 10, function () use ($rutaAlmacenamiento, $nuevoEstado) {
                        DB::transaction(function () use ($rutaAlmacenamiento, $nuevoEstado) {
                            $orden = OrdenCompra::lockForUpdate()->findOrFail($this->orden_compra->id);

                            if (!empty($orden->cod_oc)) {
                                throw new \RuntimeException('Esta orden ya fue aprobada y tiene código asignado.');
                            }

                            $orden->archivo_orden_helisa  = $rutaAlmacenamiento;
                            $orden->fecha_aprobacion      = now();
                            $orden->cod_oc                = $this->siguienteCodigoOC();
                            $orden->estado_id             = $nuevoEstado;
                            $orden->save();

                            $this->orden_compra = $orden;
                        });
                    });
                } catch (\Throwable $e) {
                    $this->addError('customError', $e->getMessage());
                    return;
                }

                $mensaje = 'Orden de compra APROBADA con éxito, código ' . $this->orden_compra->cod_oc . ' asignado y PDF guardado.';

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

    protected function conLockDB(string $nombre, int $segundos, callable $callback)
    {
        // false = usar el PDO de escritura (importante si tienes réplicas de lectura)
        $r = DB::selectOne('SELECT GET_LOCK(?, ?) AS obtenido', [$nombre, $segundos], false);

        if ((int) $r->obtenido !== 1) {
            throw new \RuntimeException('El sistema está ocupado, intenta de nuevo.');
        }

        try {
            return $callback();
        } finally {
            DB::selectOne('SELECT RELEASE_LOCK(?) AS liberado', [$nombre], false);
        }
    }

    protected function siguienteCodigoOC(): string
    {
         $ultimaOrden = OrdenCompra::whereNotNull('cod_oc')
            ->where('cod_oc', '!=', '')
            ->whereNotNull('fecha_aprobacion')
            ->orderByDesc('fecha_aprobacion')
            ->orderByDesc('id') // desempate si dos tienen la misma fecha
            ->first(['id', 'cod_oc', 'fecha_aprobacion']);

        $ultimo = 0;

        if ($ultimaOrden && preg_match('/(\d+)\s*$/', $ultimaOrden->cod_oc, $m)) {
            $ultimo = (int) $m[1];
        }

        return 'OC ' . str_pad($ultimo + 1, 3, '0', STR_PAD_LEFT);
    }

    public function render(){
        return view('livewire.admin.ordenescompra.ordenes.orden-juridica');
    }

}