<?php

namespace App\Http\Livewire\Admin\Produccion\Ordenes_Compra;

use App\Models\OrdenCompra;
use Livewire\Component;

class OrdenesReintegros extends Component {

    public $searchCC = '';
    public $reintegroSeleccionado = null; // Para abrir la vista detallada en un modal/desplegable
    public $ordenAnticipo = null;
    public $expandedOcId = null;


    public function render()
    {
        if (!$this->ordenAnticipo) {
            $anticiposPendientes = OrdenCompra::with([
                'presupuesto.productor_info',
                'ordenItems'
            ])
                ->where('tipo_oc', 4)
                ->where('estado_id', 1)
                // Filtro SQL: Trae únicamente las OC cuyo vtotal_oc acumulado sea mayor a monto_legalizado acumulado
                ->whereHas('ordenItems', function ($q) {
                    $q->selectRaw('oc_id, SUM(vtotal_oc) as total_oc, SUM(monto_legalizado) as total_leg')
                        ->groupBy('oc_id')
                        ->havingRaw('SUM(vtotal_oc) < SUM(monto_legalizado)');
                })
                ->when(!empty(trim($this->searchCC)), function ($q) {
                    $q->whereHas('presupuesto', function ($subQ) {
                        $subQ->where('cod_cc', 'like', '%' . trim($this->searchCC) . '%');
                    });
                })
                ->latest()
                ->take(10)
                ->get();
        }

        return view('livewire.admin.produccion.ordenes-compra.orden_compra_reintegros', [
            'anticiposPendientes' => $anticiposPendientes
        ]);
    }

    public function toggleDetalle($ocId)
    {
        // Si se vuelve a presionar la misma fila, se cierra. Si es otra, se abre.
        if ($this->expandedOcId === $ocId) {
            $this->expandedOcId = null;
        } else {
            $this->expandedOcId = $ocId;
        }
    }

    // Método para cargar el detalle de la legalización y los ítems a reembolsar
    public function verDetalleReintegro($ocId)
    {
        $this->reintegroSeleccionado = OrdenCompra::with([
            'presupuesto.productor_info',
            'ordenItems',
            'evidencias'
        ])->find($ocId);

        $this->emit('openModalDetalleReintegro');
    }
}
