<?php

namespace App\Http\Livewire\Produccion;

use App\Models\Anticipo;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Services\AnticipoJuridicoFlujo as F;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Ficha única del anticipo jurídico. El productor lo solicita o lo corrige; líder de producción,
 * Controller, Gerencia, Contabilidad y Tesorería ven el mismo anticipo con su recorrido y, cuando
 * les toca, los botones de su paso. Las reglas (quién, desde qué estado, umbral) viven en
 * App\Services\AnticipoJuridicoFlujo: aquí solo se muestran y se llaman.
 */
class AnticipoJuridico extends Component
{
    use WithFileUploads;

    public $anticipo_id;

    // Solicitud o corrección (productor)
    public $modo = 'oc';            // 'oc' = sobre una orden de compra aprobada | 'borrador' = sin orden previa
    public $oc_id = '';
    public $presupuesto_id = '';
    public $proveedor_id = '';
    public $buscarProveedor = '';
    public $valor_base = '';
    public $porcentaje = '';
    public $observacion = '';
    public $soportes = [];
    public $nuevos = [];            // cada selección de archivos se suma a $soportes

    // Decisión de un revisor
    public $obs_decision = '';
    public $cod_causal = '';
    public $comprobante;

    /** Mensaje de lo que acaba de pasar y qué sigue. */
    public $hecho = null;

    protected $messages = [
        'soportes.*.mimes' => 'Los soportes deben ser PDF o imagen (JPG, PNG).',
        'soportes.*.max' => 'Cada soporte puede pesar máximo 10 MB.',
        'comprobante.mimes' => 'El comprobante debe ser un PDF.',
        'comprobante.max' => 'El comprobante puede pesar máximo 10 MB.',
    ];

    public function mount($anticipo_id = null)
    {
        $u = Auth::user();
        $this->anticipo_id = $anticipo_id;
        if ($anticipo_id) {
            $a = Anticipo::find($anticipo_id);
            abort_unless($a && $a->esJuridico(), 404);
            // Un productor solo ve los suyos
            abort_if((int) $u->rol === 7 && (int) $a->productor_id !== (int) $u->id, 403);
            $this->llenarDesde($a);
        } else {
            abort_unless(F::es($u, 'productor'), 403);
        }
        $this->hecho = session('aj_hecho');
    }

    private function llenarDesde(Anticipo $a): void
    {
        $this->modo = $a->oc_id ? 'oc' : 'borrador';
        $this->oc_id = (string) $a->oc_id;
        $this->presupuesto_id = (string) $a->presupuesto_id;
        $this->proveedor_id = (string) $a->proveedor_id;
        $this->valor_base = $a->oc_id ? '' : number_format((float) $a->valor_base, 0, ',', '.');
        $this->porcentaje = rtrim(rtrim(number_format((float) $a->porcentaje_anticipo, 2, '.', ''), '0'), '.');
    }

    /* ---------------- Formulario ---------------- */

    public function updatedModo()
    {
        if ($this->anticipo_id) { return; } // en una corrección el modo no cambia
        $this->reset(['oc_id', 'presupuesto_id', 'proveedor_id', 'buscarProveedor', 'valor_base']);
        $this->resetErrorBag();
    }

    public function elegirProveedor($id)
    {
        $p = Proveedor::where('tipo', 'juridica')->find($id);
        if ($p) {
            $this->proveedor_id = (string) $p->id;
            $this->buscarProveedor = '';
            $this->resetErrorBag('proveedor_id');
        }
    }

    public function quitarProveedor()
    {
        $this->proveedor_id = '';
    }

    public function updatedNuevos()
    {
        $this->validate(['nuevos.*' => 'file|mimes:pdf,jpg,jpeg,png|max:'.F::MAX_SOPORTE_KB], [
            'nuevos.*.mimes' => 'Los soportes deben ser PDF o imagen (JPG, PNG).',
            'nuevos.*.max' => 'Cada soporte puede pesar máximo 10 MB.',
        ]);
        $this->soportes = array_values(array_merge($this->soportes, $this->nuevos));
        $this->nuevos = [];
        $this->resetErrorBag('soportes');
    }

    public function quitarSoporteNuevo($i)
    {
        $soportes = $this->soportes;
        unset($soportes[$i]);
        $this->soportes = array_values($soportes);
    }

    public function quitarSoporte($evidenciaId)
    {
        (new F)->quitarSoporte(Anticipo::findOrFail($this->anticipo_id), Auth::user(), (int) $evidenciaId);
    }

    /** El productor envía la solicitud nueva o la corrección. */
    public function enviar()
    {
        $this->validate([
            'soportes.*' => 'file|mimes:pdf,jpg,jpeg,png|max:'.F::MAX_SOPORTE_KB,
            'observacion' => 'nullable|string|max:1000',
        ]);
        $datos = [
            'modo' => $this->modo, 'oc_id' => $this->oc_id, 'presupuesto_id' => $this->presupuesto_id,
            'proveedor_id' => $this->proveedor_id, 'valor_base' => $this->valor_base,
            'porcentaje' => $this->porcentaje, 'observacion' => $this->observacion,
        ];

        if (!$this->anticipo_id) {
            $a = (new F)->solicitar(Auth::user(), $datos, $this->soportes);
            session()->flash('aj_hecho', 'Solicitud registrada con el número #'.$a->id.'. Ahora la revisa el líder de producción; te llegará un correo si te la devuelven.');
            return redirect()->route('anticipo-prod', ['anticipo_id' => $a->id]);
        }

        (new F)->reenviar(Anticipo::findOrFail($this->anticipo_id), Auth::user(), $datos, $this->soportes);
        $this->reset(['soportes', 'observacion']);
        $this->hecho = 'Corrección enviada. Vuelve a empezar por el líder de producción.';
        $this->dispatchBrowserEvent('aj-hecho');
    }

    /* ---------------- Decisiones ---------------- */

    public function decidir($accion)
    {
        $u = Auth::user();
        $a = Anticipo::findOrFail($this->anticipo_id);
        $f = new F;
        $obs = $this->obs_decision;

        switch ($accion) {
            case 'aprobar_lider':         $a = $f->aprobarLider($a, $u, $obs); break;
            case 'rechazar_lider':        $a = $f->rechazarLider($a, $u, $obs); break;
            case 'aprobar_controller':    $a = $f->aprobarController($a, $u, $obs); break;
            case 'rechazar_controller':   $a = $f->rechazarController($a, $u, $obs); break;
            case 'aprobar_gerencia':      $a = $f->aprobarGerencia($a, $u, $obs); break;
            case 'rechazar_gerencia':     $a = $f->rechazarGerencia($a, $u, $obs); break;
            case 'causar':                $a = $f->causar($a, $u, $this->cod_causal, $obs); break;
            case 'devolver_contabilidad': $a = $f->devolverContabilidad($a, $u, $obs); break;
            case 'pagar':
                $this->validate(['comprobante' => 'required|file|mimes:pdf|max:'.F::MAX_SOPORTE_KB], ['comprobante.required' => 'Adjunta el comprobante de pago en PDF.']);
                $a = $f->pagar($a, $u, $this->comprobante, $obs);
                break;
            default: return;
        }

        $this->reset(['obs_decision', 'cod_causal', 'comprobante']);
        $this->hecho = $this->queSigue($a);
        $this->dispatchBrowserEvent('aj-hecho'); // sube a mostrar el mensaje y el recorrido actualizado
    }

    private function queSigue(Anticipo $a): string
    {
        switch ((int) $a->estado_id) {
            case F::REV_CONTROLLER:    return 'Aprobado. Ahora lo revisa el Controller.';
            case F::REV_GERENCIA:      return 'Aprobado. Como supera $'.number_format(F::umbral(), 0, ',', '.').', ahora lo aprueba Gerencia.';
            case F::APROBADO:          return 'Aprobado. Se generó la orden de anticipo '.$a->cod_orden_anticipo.' y pasó a Contabilidad para causarlo.';
            case F::CAUSADO:           return 'Causado. Ahora Tesorería lo paga.';
            case F::PAGADO:            return 'Pago registrado. El anticipo quedó cerrado y se avisó al productor, a compras y al proveedor.';
            case F::RECH_CONTABILIDAD: return 'Devuelto al Controller con tu observación.';
            default:                   return 'Devuelto al productor con tu observación.';
        }
    }

    /* ---------------- Vista ---------------- */

    public function render()
    {
        $u = Auth::user();
        $a = $this->anticipo_id
            ? Anticipo::with(['estado', 'proveedor', 'ordenCompra', 'presupuesto.gestion.contacto', 'productor_info', 'pasos.usuario', 'evidencias'])->find($this->anticipo_id)
            : null;

        $acciones = $a ? F::acciones($a, $u) : [];
        $editando = !$a || in_array('reenviar', $acciones, true);

        $ordenes = collect();
        $presupuestos = collect();
        $proveedores = collect();
        if (!$a && $this->modo === 'oc') { $ordenes = F::ordenesDisponibles($u); }
        if (!$a && $this->modo === 'borrador') { $presupuestos = F::presupuestosDisponibles($u); }
        $t = trim($this->buscarProveedor);
        if ($editando && $this->modo === 'borrador' && mb_strlen($t) >= 2) {
            $proveedores = Proveedor::where('tipo', 'juridica')
                ->where(fn ($q) => $q->where('tercero', 'like', "%{$t}%")->orWhere('documento', 'like', "{$t}%"))
                ->orderBy('tercero')->limit(8)->get(['id', 'tercero', 'documento', 'ciudad', 'anticipo']);
        }
        $proveedor = $this->proveedor_id ? Proveedor::find($this->proveedor_id) : null;

        // Vista previa del cálculo mientras se edita
        $orden = null;
        if ($this->modo === 'oc' && $this->oc_id) {
            $orden = $ordenes->firstWhere('id', (int) $this->oc_id)
                ?? OrdenCompra::with('proveedor')->withSum('ordenItems as valor_orden', 'vtotal_oc')->find($this->oc_id);
            if ($orden) { $proveedor = $orden->proveedor; }
        }
        $base = $this->modo === 'oc' ? (float) optional($orden)->valor_orden : F::pesos($this->valor_base);
        $pct = is_numeric($this->porcentaje) ? (float) $this->porcentaje : 0;
        $total = $editando ? round($base * $pct / 100, 2) : (float) optional($a)->total_anticipo;

        return view('livewire.produccion.anticipo-juridico', [
            'a' => $a,
            'acciones' => $acciones,
            'editando' => $editando,
            'ordenes' => $ordenes,
            'presupuestos' => $presupuestos,
            'proveedores' => $proveedores,
            'proveedor' => $proveedor,
            'orden' => $orden,
            'base' => $base,
            'total' => $total,
            'umbral' => F::umbral(),
            'recorrido' => $this->recorrido($a, $total),
        ]);
    }

    /**
     * Las etapas del flujo con su situación: 'hecho', 'actual', 'devuelto', 'pendiente' o 'no_aplica'
     * (Gerencia cuando el total no supera el umbral). Para la barra de pasos de la ficha.
     */
    private function recorrido(?Anticipo $a, float $total): array
    {
        $gerencia = F::requiereGerencia($total);
        $etapas = [
            'productor'    => ['Productor', 'Solicita'],
            'lider'        => ['Líder de producción', 'Revisa'],
            'controller'   => ['Controller', 'Revisa'],
            'gerencia'     => ['Gerencia', $gerencia ? 'Aprueba (supera $'.number_format(F::umbral() / 1000000, 0).' M)' : 'No aplica: $'.number_format(F::umbral() / 1000000, 0).' M o menos'],
            'contabilidad' => ['Contabilidad', 'Causa'],
            'tesoreria'    => ['Tesorería', 'Paga'],
        ];
        $orden = array_keys($etapas);
        $estado = $a ? (int) $a->estado_id : null;
        $manos = $a ? F::enManosDe($a) : 'productor';
        $posActual = $estado === F::PAGADO ? count($orden) : array_search($manos, $orden, true);
        $devuelto = $a && in_array($estado, array_merge(F::CORREGIBLES, [F::RECH_CONTABILIDAD]), true);

        // Última persona que actuó en cada etapa (de la ronda vigente)
        $quien = [];
        if ($a) {
            $etapaDe = ['solicitar' => 'productor', 'reenviar' => 'productor', 'aprobar_lider' => 'lider', 'aprobar_controller' => 'controller',
                        'aprobar_gerencia' => 'gerencia', 'causar' => 'contabilidad', 'pagar' => 'tesoreria'];
            foreach ($a->pasos as $p) {
                if (in_array($p->accion, ['solicitar', 'reenviar'], true)) { $quien = []; }
                if (isset($etapaDe[$p->accion])) { $quien[$etapaDe[$p->accion]] = $p; }
            }
        }

        $salida = [];
        foreach ($orden as $i => $clave) {
            [$titulo, $que] = $etapas[$clave];
            if ($clave === 'gerencia' && !$gerencia) { $sit = 'no_aplica'; }
            elseif ($posActual !== false && $i < $posActual) { $sit = 'hecho'; }
            elseif ($posActual !== false && $i === $posActual) { $sit = $devuelto ? 'devuelto' : 'actual'; }
            else { $sit = 'pendiente'; }
            // Contabilidad devolvió al Controller: el Controller está "actual" y contabilidad sigue pendiente
            $salida[$clave] = ['titulo' => $titulo, 'que' => $que, 'sit' => $sit, 'paso' => $quien[$clave] ?? null];
        }
        return $salida;
    }
}
