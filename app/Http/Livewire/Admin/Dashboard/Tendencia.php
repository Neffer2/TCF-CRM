<?php

namespace App\Http\Livewire\Admin\Dashboard;

use App\Models\Año;
use App\Models\Helisa;
use App\Models\Mes;
use App\Models\Presupuesto;
use Livewire\Component;

/**
 * Tendencia mensual del dashboard de gerencia: venta facturada (Helisa)
 * frente al presupuesto, mes a mes del año seleccionado, opcionalmente
 * para un solo comercial. Escucha la señal 'Tendencia' de Filters.
 */
class Tendencia extends Component
{
    public $año_id;
    public $comercial;
    public $comerciales = null; // ids con los que filtrar (un comercial o el equipo de un líder); null = todos
    public $alcance = '';
    public $labels = [];
    public $venta = [];
    public $presupuesto = [];
    public $añoDescripcion = '';

    protected $listeners = ['Tendencia' => 'actualizar'];

    public function mount()
    {
        $año = Año::orderBy('created_at', 'desc')->first();
        $this->año_id = $año ? $año->id : null;
        $this->calcular();
    }

    public function actualizar($filtros = null)
    {
        if ($filtros) {
            $this->año_id = $filtros['año_id'] ?? $this->año_id;
            $this->comercial = $filtros['comercial'] ?? null;
            $this->comerciales = $filtros['comerciales'] ?? ($this->comercial ? [(int) $this->comercial] : null);
            $this->alcance = $filtros['alcance'] ?? '';
        } else {
            $año = Año::orderBy('created_at', 'desc')->first();
            $this->año_id = $año ? $año->id : null;
            $this->comercial = null;
            $this->comerciales = null;
            $this->alcance = '';
        }
        $this->calcular();
    }

    public function calcular()
    {
        $this->labels = [];
        $this->venta = [];
        $this->presupuesto = [];

        $año = $this->año_id ? Año::find($this->año_id) : null;
        if (!$año) {
            return;
        }
        $this->añoDescripcion = $año->description;

        // identifier es texto: ordenar numéricamente (si no, Ene, Oct, Nov, Dic, Feb...)
        $meses = Mes::where('ano_id', $año->id)->orderByRaw('CAST(identifier AS UNSIGNED)')->get();
        foreach ($meses as $mes) {
            // Misma definición que Block1::getVentaFacturada (Helisa.base_factura por fecha)
            $venta = Helisa::where('año', $año->description)
                ->when(is_array($this->comerciales), function ($q) { $q->whereIn('comercial', $this->comerciales); })
                ->whereBetween('fecha', [$mes->f_inicio, $mes->f_fin])
                ->sum('base_factura');

            // Misma definición que Block1::getPresupuesto (presupuestos.valor por mes)
            $presupuesto = Presupuesto::where('ano_id', $año->id)
                ->where('mes_id', $mes->id)
                ->when(is_array($this->comerciales), function ($q) { $q->whereIn('id_user', $this->comerciales); })
                ->sum('valor');

            $this->labels[] = mb_substr($mes->description, 0, 3);
            $this->venta[] = round((float) $venta);
            $this->presupuesto[] = round((float) $presupuesto);
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard.tendencia');
    }
}
