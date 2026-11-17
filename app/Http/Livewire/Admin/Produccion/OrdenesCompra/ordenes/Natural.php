<?php

namespace App\Http\Livewire\Admin\Produccion\OrdenesCompra\ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Traits\Email;
use App\Traits\SMS;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;

class Natural extends Component{

use SMS, Email;

    // Estados (ajusta a tu tabla)
    const ESTADO_RECHAZADA       = 12;
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

    public function mount($orden_id = null)
    {
        $this->items = collect();

        if ($orden_id) {
            $this->orden_id = $orden_id;
            $this->queriedOrden = OrdenCompra::find($orden_id);

            if ($this->queriedOrden) {
                $this->loadOrdenData();
                $this->puedeAprobar = $this->esMiTurno();

                // Precarga el código secuencial si la orden aún no lo tiene registrado
                if (empty($this->queriedOrden->cod_oc)) {
                    $this->cod_oc = $this->siguienteCodigoOC();
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

    // Cambiar estado: Aprobar (3) o Rechazar (7) // Si tiene evidencias (2)
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
                $this->queriedOrden->estado_id = 2;
                $this->queriedOrden->update();

                return redirect()->route('aprobaciones-ordenes-compra')->with('success', 'Las evidencias de la orden ' . $this->queriedOrden->id . ' han sido APROBADAS');
            }

            if ($accion === 'aprobar')
            {
                $this->validate(['cod_oc' => 'nullable|string|max:200']);

                $nuevoEstado = $this->siguienteEstadoAprobacion();

                if ($nuevoEstado === self::ESTADO_APROBADA) 
                {
                    $soloAprobada = false;
                    try {
                        $this->conLockDB('oc_consecutivo', 10, function () use (&$nuevoEstado, &$soloAprobada) {
                            DB::transaction(function () use (&$nuevoEstado, &$soloAprobada) {
                                $orden = OrdenCompra::lockForUpdate()->findOrFail($this->queriedOrden->id);

                                // Si ya tiene código OC: solo se aprueba (sin nuevo código ni PDF)
                                if (!empty($orden->cod_oc)) {
                                    $orden->estado_id        = $nuevoEstado;
                                    $orden->save();

                                    $this->queriedOrden = $orden;
                                    $soloAprobada = true;
                                    return;
                                }

                                // --- Flujo normal: sin código asignado ---
                                $orden->cod_oc           = $this->siguienteCodigoOC();

                                $orden->load([
                                    'naturalInfo.tercero',
                                    'naturalInfo.productor',
                                    'ordenItems.itemPresupuesto',
                                ]);

                                $dompdf = new Dompdf(['enable_remote' => true]);
                                $html = View::make('exports.orden_compra_pdf', [
                                    'orden' => $orden,
                                ])->render();

                                $dompdf->loadHtml($html);
                                $dompdf->setPaper('A4', 'portrait');
                                $dompdf->render();

                                $pdfOutput = $dompdf->output();
                                $nombreArchivo = 'orden_natural_' . str_replace(' ', '_', $orden->cod_oc) . '_' . time() . '.pdf';
                                $rutaAlmacenamiento = 'public/ordenes_naturales_helisa/' . $nombreArchivo;

                                Storage::put($rutaAlmacenamiento, $pdfOutput);

                                $orden->archivo_orden_helisa = $rutaAlmacenamiento;
                                $orden->estado_id            = $nuevoEstado;
                                $orden->save();

                                $this->queriedOrden = $orden;
                            });
                        });

                    } catch (\Throwable $e) {
                        $this->addError('customError', $e->getMessage());
                        return;
                    }

                    $this->ocNaturalRevisionContabilidad($this->queriedOrden);
                    return redirect()->route('aprobaciones-ordenes-compra')->with('success', 'Orden de compra #' . $this->queriedOrden->id . ' aprobada Y PDF generado');

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
        return view('livewire.admin.ordenescompra.ordenes.orden-natural');
    }

}
