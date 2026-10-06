<?php

namespace App\Http\Livewire\Productor\Anticipo;

use App\Models\Anticipo as ModelAnticipo;
use Illuminate\Validation\Rule;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AnticipoNatural extends Component
{
    private const TIPO_OC_NATURAL    = 2;
    private const OC_ESTADO_ANTICIPO = 3;
    private const ANTICIPO_RECHAZADO = 6;

    public $orden_compra, $porcentaje_anticipo, $total_anticipo;
    public $orden, $queriedAnticipo;
    public $anticipo_id;
    public $search = '';

    public function mount()
    {
        if ($this->anticipo_id) {
            $this->queriedAnticipo = ModelAnticipo::with('ordenCompra')->find($this->anticipo_id);

            // Si el anticipo es de otro tipo, este componente no lo carga
            if ($this->queriedAnticipo?->ordenCompra?->tipo_oc != self::TIPO_OC_NATURAL) {
                $this->queriedAnticipo = null;
                return;
            }

            $this->setData();
        }
    }

    public function render()
    {
        $ordenes = $this->queriedAnticipo ? collect() : $this->ordenesDisponibles();

        $this->orden = $this->cargarOrden($this->orden_compra);

        $totalOrden = $this->orden ? $this->orden->ordenItems->sum('vtotal_oc') : 0;

        return view('livewire.productor.anticipos.anticipo-natural', compact('ordenes', 'totalOrden'));
    }

    private function ordenesDisponibles()
    {
        return OrdenCompra::with(['proveedor', 'presupuesto', 'ordenItems.itemPresupuesto.presupuesto', 'naturalInfo'])
            ->where('tipo_oc', self::TIPO_OC_NATURAL)
            ->where('estado_id', self::OC_ESTADO_ANTICIPO)
            ->whereHas('naturalInfo', fn ($n) => $n->where('productor_id', Auth::id()))
            ->whereDoesntHave('anticipos', fn ($q) => $q->where('estado_id', '!=', self::ANTICIPO_RECHAZADO))
            ->when($this->search, function ($query) {
                $s = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($s) {
                    $q->where('cod_oc', 'like', $s)
                      ->orWhere('id', 'like', $s)
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

    private function cargarOrden($id)
    {
        if (! $id) {
            return null;
        }

        return OrdenCompra::with([
            'presupuesto.gestion.contacto',
            'presupuesto.presupuestoItems',
            'proveedor',
            'ordenItems.itemPresupuesto.presupuesto.gestion.contacto',
            'ordenItems.itemPresupuesto.presupuesto.presupuestoItems',
            'anticipos',
            'naturalInfo',
        ])->find($id);
    }

    public function setData()
    {
        $this->orden_compra        = $this->queriedAnticipo->oc_id;
        $this->orden               = $this->cargarOrden($this->orden_compra);
        $this->porcentaje_anticipo = $this->queriedAnticipo->porcentaje_anticipo;
        $this->total_anticipo      = $this->queriedAnticipo->total_anticipo;
    }

    public function nuevoAnticipoNatural()
    {
        $this->validate([
            'orden_compra' => [
                'required',
                'exists:ordenes_compra,id',
                Rule::unique('anticipos', 'oc_id')
                    ->where(fn ($q) => $q->where('estado_id', '!=', self::ANTICIPO_RECHAZADO)),
            ],
            'porcentaje_anticipo' => 'required|numeric|min:1|max:100',
        ]);

        $orden = OrdenCompra::with('ordenItems')
            ->where('tipo_oc', self::TIPO_OC_NATURAL)
            ->where('estado_id', self::OC_ESTADO_ANTICIPO)
            ->whereHas('naturalInfo', fn ($n) => $n->where('productor_id', Auth::id()))
            ->findOrFail($this->orden_compra);

        $total = ($orden->ordenItems->sum('vtotal_oc') * $this->porcentaje_anticipo) / 100;

        ModelAnticipo::create([
            'oc_id'               => $orden->id,
            'porcentaje_anticipo' => $this->porcentaje_anticipo,
            'total_anticipo'      => $total,
            'estado_id'           => 2,
            'fecha_solicitud'     => now(),
            'productor_id'        => Auth::id(),
        ]);

        return redirect()->route('lista-anticipos-prod')->with('success', 'Anticipo creado');
    }

    public function actualizarAnticipoNatural()
    {
        $this->validate(['porcentaje_anticipo' => 'required|numeric|min:1|max:100']);

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
        $this->total_anticipo = ($this->orden && $this->porcentaje_anticipo)
            ? ($this->orden->ordenItems->sum('vtotal_oc') * $this->porcentaje_anticipo) / 100
            : null;
    }
}   