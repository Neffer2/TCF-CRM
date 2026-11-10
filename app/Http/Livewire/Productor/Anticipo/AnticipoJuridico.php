<?php

namespace App\Http\Livewire\Productor\Anticipo;

use App\Models\Anticipo as ModelAnticipo;
use App\Models\Año;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AnticipoJuridico extends Component
{
    public $orden_compra, $porcentaje_anticipo, $total_anticipo, $justificacion_productor;
    public $orden, $queriedAnticipo;
    public $anticipo_id;
    public $search = '';

    private const TIPO_OC_JURIDICO = 1;   // valor de tipo_oc para jurídico
    private const OC_ESTADO_ANTICIPO = 8; // estado de OC que permite anticipo
    private const ANTICIPO_RECHAZADO = 6;


    public function mount()
    {
        if ($this->anticipo_id) {
            $this->queriedAnticipo = ModelAnticipo::find($this->anticipo_id);
            $this->setData();
        }
    }

    public function render()
    {
        $ordenes = collect();

        // Solo se consulta la lista cuando se está creando (no al aprobar)
        if (! $this->queriedAnticipo) {
            $ultimoAño = Año::orderBy('description', 'desc')->first()->description ?? date('Y');

            $ordenes = $this->queriedAnticipo ? collect() : $this->ordenesDisponibles();
        }

        $totalOrden = $this->orden ? $this->orden->ordenItems->sum('vtotal_oc') : 0;
        $totalAnticiposPrevios = $this->orden ? $this->orden->anticipos->sum('total_anticipo') : 0;

        return view('livewire.productor.anticipos.anticipo-juridico', compact(
            'ordenes', 'totalOrden', 'totalAnticiposPrevios'
        ));
    }

    public function setData()
    {
        if ($this->queriedAnticipo) {
            $this->orden_compra = $this->queriedAnticipo->oc_id;
            $this->orden = $this->cargarOrden($this->orden_compra);
            $this->porcentaje_anticipo = $this->queriedAnticipo->porcentaje_anticipo;
            $this->total_anticipo = $this->queriedAnticipo->total_anticipo;
        }
    }

    private function cargarOrden($id)
    {
        if (! $id) {
            return null;
        }

        return OrdenCompra::with([
            'presupuesto.gestion.contacto',
            'presupuesto.presupuestoItems',
            'proveedor',
            'ordenItems',
            'anticipos',
        ])->find($id);
    }

    public function nuevoAnticipoJuridico()
    {
        $this->validate([
            'orden_compra'        => 'required|exists:ordenes_compra,id|unique:anticipos,oc_id',
            'porcentaje_anticipo' => 'required|numeric|min:1|max:100',
            'justificacion_productor' => 'required|string|max:500',
        ]);

        $productorId = Auth::id();

        // Seguridad: la OC debe pertenecer al productor autenticado
        $orden = OrdenCompra::with('ordenItems')
            ->where('tipo_oc', self::TIPO_OC_JURIDICO)
            ->where('estado_id', self::OC_ESTADO_ANTICIPO)
            ->whereHas('presupuesto', fn ($q) => $q->where('productor', Auth::id()))
            ->findOrFail($this->orden_compra);

        // El total se recalcula en el servidor
        $total = ($orden->ordenItems->sum('vtotal_oc') * $this->porcentaje_anticipo) / 100;

        ModelAnticipo::create([
            'oc_id'               => $orden->id,
            'porcentaje_anticipo' => $this->porcentaje_anticipo,
            'total_anticipo'      => $total,
            'estado_id'           => 2,
            'fecha_solicitud'     => now(),
            'productor_id'        => $productorId,
            'justificacion_productor' => $this->justificacion_productor,
        ]);

        return redirect()->route('lista-anticipos')->with('success', 'Anticipo creado');
    }

    public function actualizarAnticipoJuridico()
    {
        $this->validate([
            'porcentaje_anticipo' => 'required|numeric|min:1|max:100',
        ]);

        if ($this->queriedAnticipo) {
            $orden = OrdenCompra::with('ordenItems')->findOrFail($this->queriedAnticipo->oc_id);
            $total = ($orden->ordenItems->sum('vtotal_oc') * $this->porcentaje_anticipo) / 100;

            $this->queriedAnticipo->update([
                'porcentaje_anticipo' => $this->porcentaje_anticipo,
                'total_anticipo'      => $total,
                'estado_id'           => 1,
                'fecha_aprobacion'    => now(),
            ]);

            return redirect()->route('lista-anticipos-admin')->with('success', 'Anticipo aprobado');
        }
    }

    public function updatedOrdenCompra()
    {
        $this->orden = $this->cargarOrden($this->orden_compra);
        $this->updatedPorcentajeAnticipo();
    }

    public function updatedPorcentajeAnticipo()
    {
        if ($this->orden && $this->porcentaje_anticipo) {
            $this->total_anticipo = ($this->orden->ordenItems->sum('vtotal_oc') * $this->porcentaje_anticipo) / 100;
        } else {
            $this->total_anticipo = null;
        }
    }

    private function ordenesDisponibles()
    {
        return OrdenCompra::with(['presupuesto', 'proveedor'])
            ->where('tipo_oc', self::TIPO_OC_JURIDICO)
            ->where('estado_id', self::OC_ESTADO_ANTICIPO)
            ->whereHas('presupuesto', fn ($q) => $q->where('productor', Auth::id()))
            ->whereDoesntHave('anticipos', fn ($q) => $q->where('estado_id', '!=', self::ANTICIPO_RECHAZADO))
            ->when($this->search, function ($query) {
                $s = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($s) {
                    $q->where('cod_oc', 'like', $s)
                      ->orWhere('gr', 'like', $s)
                      ->orWhereHas('presupuesto', function ($p) use ($s) {
                          $p->where('cod_cc', 'like', $s)
                            ->orWhereHas('gestion', fn ($g) => $g->where('nom_proyecto_cot', 'like', $s));
                      })
                      ->orWhereHas('proveedor', fn ($p) => $p->where('tercero', 'like', $s));
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }
}