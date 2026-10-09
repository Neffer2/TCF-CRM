<?php

namespace App\Http\Livewire\Productor\Anticipo;

use App\Models\Anticipo;
use App\Models\OrdenCompra;
use Livewire\Component;
use App\Exports\AnticipoOrdenes;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListaAnticipos extends Component
{
    public $search = '';
    public $ordenes = [];
    public $anticipo_id;

    // Propiedades para el modal de detalle / edición
    public $selectedAnticipo = null;
    public $orden = null;
    public $porcentaje_anticipo;
    public $total_anticipo;
    public $showModal = false;

    protected $rules = [
        'porcentaje_anticipo' => 'required|numeric|min:1|max:100',
    ];

    public function render()
    {
        $this->listaAnticipos();

        $totalOrden = $this->orden ? $this->orden->ordenItems->sum('vtotal_oc') : 0;

        return view('livewire.productor.anticipos.lista-anticipos', compact('totalOrden'));
    }

    public function mount($anticipo_id = null)
    {
        $this->anticipo_id = $anticipo_id;
    }

    public function listaAnticipos()
    {
        $this->ordenes = Anticipo::with([
                'ordenCompra.tipo',
                'ordenCompra.presupuesto',
                'estado'
            ])
            ->whereHas('ordenCompra', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('tipo_oc', 1)
                        ->where('productor_id', auth()->id());
                })
                ->orWhere(function ($sub) {
                    $sub->where('tipo_oc', 2)
                        ->whereHas('naturalInfo', fn ($n) => $n->where('productor_id', auth()->id()));
                });
            })
            ->when($this->search, function ($query) {
                $s = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($s) {
                    $q->where('id', 'like', $s)
                      ->orWhereHas('ordenCompra', fn ($q) => $q->where('cod_oc', 'like', $s))
                      ->orWhereHas('ordenCompra.presupuesto', fn ($p) => $p->where('cod_cc', 'like', $s));
                });
            })
            ->orderByDesc('created_at')
            ->take(50)
            ->get();
    }

    // Carga los datos del anticipo y la orden de compra asociada con sus items
    public function verDetalle($id)
    {
        $this->selectedAnticipo = Anticipo::with(['ordenCompra', 'estado'])->find($id);

        if ($this->selectedAnticipo) {
            $this->orden = $this->cargarOrden($this->selectedAnticipo->oc_id);
            $this->porcentaje_anticipo = $this->selectedAnticipo->porcentaje_anticipo;
            $this->total_anticipo = $this->selectedAnticipo->total_anticipo;

            $this->showModal = true;
            $this->dispatchBrowserEvent('open-modal-detalle');
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

    // Recalcula en tiempo real según la suma de vtotal_oc de ordenItems
    public function updatedPorcentajeAnticipo()
    {
        if ($this->orden && $this->porcentaje_anticipo && is_numeric($this->porcentaje_anticipo)) {
            $baseCalculo = $this->orden->ordenItems->sum('vtotal_oc');
            $this->total_anticipo = ($baseCalculo * $this->porcentaje_anticipo) / 100;
        } else {
            $this->total_anticipo = null;
        }
    }

    // Reenvía a validación con el cálculo recalculado en servidor
    public function reenviarValidacion()
    {
        $this->validate();

        if ($this->selectedAnticipo && $this->selectedAnticipo->estado_id == 6) {
            // Se recalcura el total en servidor por seguridad antes de actualizar
            $orden = OrdenCompra::with('ordenItems')->find($this->selectedAnticipo->oc_id);
            $baseCalculo = $orden ? $orden->ordenItems->sum('vtotal_oc') : 0;
            $totalCalculado = ($baseCalculo * $this->porcentaje_anticipo) / 100;

            $this->selectedAnticipo->update([
                'porcentaje_anticipo' => $this->porcentaje_anticipo,
                'total_anticipo'      => $totalCalculado,
                'estado_id'           => 2,
            ]);

            $this->cerrarModal();
            session()->flash('message', 'El anticipo fue actualizado y reenviado a lider de produccion');
        }
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->selectedAnticipo = null;
        $this->orden = null;
        $this->porcentaje_anticipo = null;
        $this->total_anticipo = null;
        $this->dispatchBrowserEvent('close-modal-detalle');
    }

    /**
     * Método invocado desde la vista para descargar la factura/orden de anticipo.
     * 
     * @param int $anticipoId
     * @return StreamedResponse
     */
    public function exportarAnticipoPdf(int $anticipoId)
    {
        return AnticipoOrdenes::downloadPdf($anticipoId);
    }
}