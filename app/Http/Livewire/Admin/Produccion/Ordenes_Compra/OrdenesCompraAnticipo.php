<?php

namespace App\Http\Livewire\Admin\Produccion\Ordenes_Compra;

use App\Http\Livewire\Admin\Produccion\OrdenesCompra;
use App\Models\ItemPresupuesto;
use App\Models\PresupuestoProyecto;
use App\Models\User;
use Livewire\Component;

class OrdenesCompraAnticipo extends Component{

    public $productor_id = null;
    public $presupuesto = null;
    public $item_presupuesto = null;


    public $productores = [];
    public $proyectos_productor = [];
    public $items_presupuesto = [];

    public $cantidad = '';
    public $valor_unitario = '';
    public $valor_total = '';
    public $items = [];

    public $comercial_encargado = '';
    public $concepto_oc = '';
    public $observaciones_comercial = '';


    public $orden_id = null;
    public $queriedOrden = null;

    public function mount($orden_id = null)
    {
        $this->orden_id = $orden_id;
        $this->cargarCatalogosBase();
    }

    public function cargarCatalogosBase()
    {
        $this->productores = User::where('rol', 7)->get();
        $this->proyectos_productor = collect();
        $this->items_presupuesto = collect();
    }

    public function updatedProductorId($value)
    {
        $this->presupuesto = null;
        $this->item_presupuesto = null;
        $this->items_presupuesto = collect();

        if ($value) {
            $this->proyectos_productor = PresupuestoProyecto::where('productor', $value)->get();
        } else {
            $this->proyectos_productor = collect();
        }

    }

    /**
     * HOOK: Se dispara SOLO al cambiar de Proyecto / CC
     */
    public function updatedPresupuesto($value)
    {
        $this->item_presupuesto = null;

        if ($value) {
            $this->items_presupuesto = ItemPresupuesto::where('presupuesto_id', $value)->get();
        } else {
            $this->items_presupuesto = collect();
        }
    }

    public function newItem()
    {
        $this->validate([
            'presupuesto'      => 'required',
            'item_presupuesto' => 'required',
            'cantidad'         => 'required|numeric|gt:0',
            'valor_unitario'   => 'required|numeric|gte:0',
        ]);

        $presuObj = PresupuestoProyecto::find($this->presupuesto);
        $itemObj  = ItemPresupuesto::find($this->item_presupuesto);

        $this->items[] = [
            'presupuesto_id' => $presuObj->id,
            'cod_cc'         => $presuObj->cod_cc,
            'nombre_cc'      => $presuObj->nombre,
            'item_presu_id'  => $itemObj->id,
            'item_nombre'    => $itemObj->nombre ?? $itemObj->descripcion,
            'cantidad'       => $this->cantidad,
            'valor_unitario' => $this->valor_unitario,
            'valor_total'    => (float)$this->cantidad * (float)$this->valor_unitario,
        ];

        $this->reset(['item_presupuesto', 'cantidad', 'valor_unitario', 'valor_total']);
    }

    public function uploadOC()
    {
        return nullValue();
    }

    public function deleteItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function render()
    {
        return view('livewire.admin.produccion.ordenes-compra.orden_compra_anticipo');
    }
}
