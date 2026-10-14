<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Empleados extends Component
{
    use WithFileUploads, WithPagination;

    // Variables del formulario de ítems
    public $selected_item, $presupuesto, $item_presupuesto, $cantidad, $dias, $otros, 
           $valor_unitario = 0, $valor_total = 0, $tipo_servicio, $tipo_contrato;

    // Colecciones y límites
    public $items = [], $presupuestos = [], $items_presupuesto = [], 
           $limiteCantidad, $limiteDias, $limiteOtros, $limiteValorUnitario, $limiteValorTotal;

    // Control de Productor y Orden
    public $productor, $orden_id, $queriedOrden;

    public function mount()
    {
        $this->items = collect();

        if ($this->orden_id) {
            $this->queriedOrden = OrdenCompra::where('id', $this->orden_id)->first();
            if ($this->queriedOrden) {
                $this->getData();
            }
        }
    }

    // Obtiene presupuestos filtrando por el proveedor de empleado (ID 4 serializado)
    public function getPresupuestos()
    {
        if (!$this->productor) {
            $this->presupuestos = collect();
            return;
        }

        $this->presupuestos = PresupuestoProyecto::with('presupuestoItems')
            ->select('id', 'cod_cc', 'productor')
            ->where([['productor', $this->productor->id], ['estado_id', 1]])
            ->whereHas('presupuestoItems', function ($item) {
                $item->select('id', 'cantidad', 'dia', 'otros', 'v_unitario', 'v_total', 'proveedor')
                     ->where([['proveedor', 'LIKE', '%s:1:"4"%'], ['disponible', 1]]);
            })
            ->get();
    }

    /**
     * CRUD ITEMS EN CARRITO
     **/
    public function newItem()
    {
        $this->validate([
            'presupuesto' => 'required',
            'item_presupuesto' => 'required',
            'cantidad' => 'required|numeric|min:1|max:' . $this->limiteCantidad,
            'dias' => 'required|numeric|min:1|max:' . $this->limiteDias,
            'otros' => 'required|numeric|min:1|max:' . $this->limiteOtros,
            'valor_unitario' => 'required|numeric|min:1|max:' . $this->limiteValorUnitario,
            'valor_total' => 'required|numeric|min:1|max:' . $this->limiteValorTotal,
            'tipo_servicio' => 'required|string',
            'tipo_contrato' => 'required|string',
        ]);

        $presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first();
        $item = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();

        $newItem = [
            'proyecto' => [
                'id' => $presupuesto->id,
                'nombre' => $presupuesto->gestion->nom_proyecto_cot ?? 'Proyecto',
                'cod_cc' => $presupuesto->cod_cc
            ],
            'item' => [
                'id' => $item->id,
                'nombre' => $item->descripcion,
                'cod_cc' => $presupuesto->cod_cc,
                'display_item' => $item->num_item
            ],
            'tipo_servicio' => $this->tipo_servicio,
            'tipo_contrato' => $this->tipo_contrato,
            'cant' => $this->cantidad,
            'dias' => $this->dias,
            'otros' => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total' => $this->valor_total,
        ];

        $this->items->push($newItem);
        $this->resetFields(['presupuesto', 'item_presupuesto', 'cantidad', 'dias', 'otros', 'valor_unitario', 'valor_total', 'tipo_servicio', 'tipo_contrato']);
    }

    public function getItem($itemId)
    {
        $this->selected_item = $itemId;
        $item = $this->items[$itemId];

        $this->presupuesto = $item['proyecto']['id'];
        $this->updatedPresupuesto();

        $this->item_presupuesto = $item['item']['id'];
        $this->cantidad = $item['cant'];
        $this->dias = $item['dias'];
        $this->otros = $item['otros'];
        $this->valor_unitario = $item['valor_unitario'];
        $this->valor_total = $item['valor_total'];
        $this->tipo_servicio = $item['tipo_servicio'];
        $this->tipo_contrato = $item['tipo_contrato'];
    }

    public function actionEdit()
    {
        $this->validate([
            'presupuesto' => 'required',
            'item_presupuesto' => 'required',
            'cantidad' => 'required|numeric|min:1',
            'dias' => 'required|numeric|min:1',
            'otros' => 'required|numeric|min:1',
            'valor_unitario' => 'required|numeric|min:1',
            'valor_total' => 'required|numeric|min:1',
            'tipo_servicio' => 'required|string',
            'tipo_contrato' => 'required|string',
        ]);

        $presupuesto = $this->presupuestos->where('id', $this->presupuesto)->first();
        $item = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();

        $this->items[$this->selected_item] = [
            'proyecto' => [
                'id' => $presupuesto->id,
                'nombre' => $presupuesto->gestion->nom_proyecto_cot ?? 'Proyecto',
                'cod_cc' => $presupuesto->cod_cc
            ],
            'item' => [
                'id' => $item->id,
                'nombre' => $item->descripcion,
                'cod_cc' => $presupuesto->cod_cc,
                'display_item' => $item->num_item
            ],
            'cant' => $this->cantidad,
            'dias' => $this->dias,
            'otros' => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total' => $this->valor_total,
            'tipo_servicio' => $this->tipo_servicio,
            'tipo_contrato' => $this->tipo_contrato,
        ];

        $this->resetFields(['presupuesto', 'item_presupuesto', 'cantidad', 'dias', 'otros', 'valor_unitario', 'valor_total', 'tipo_servicio', 'tipo_contrato']);
        unset($this->selected_item);
    }

    public function deleteItem($itemId)
    {
        unset($this->items[$itemId]);
        $this->items = $this->items->values();
    }

    /**
     * CREAR Y ACTUALIZAR ORDEN DE COMPRA (PROVEEDOR EMPLEADO ID 4)
     **/
    public function uploadOC()
    {
        if ($this->items->count() <= 0) {
            $this->addError('items-error', 'No se han agregado items a la orden de compra');
            return;
        }

        $orden = OrdenCompra::create([
            'tipo_oc' => 2,
            'estado_id' => 3,
            'presupuesto_id' => null,
            'proveedor_id' => 4,
        ]);

        foreach ($this->items as $item) {
            OcItem::create([
                'oc_id' => $orden->id,
                'item_id' => $item['item']['id'],
                'desc_oc' => $item['item']['nombre'],
                'cant_oc' => $item['cant'],
                'dias_oc' => $item['dias'],
                'otros_oc' => $item['otros'],
                'vunit_oc' => $item['valor_unitario'],
                'vtotal_oc' => $item['valor_total'],
                'tipo_servicio' => $item['tipo_servicio'],
                'tipo_contrato' => $item['tipo_contrato'],
            ]);
        }

        $this->resetFields(['presupuesto', 'item_presupuesto', 'cantidad', 'dias', 'otros', 'valor_unitario', 'valor_total', 'tipo_servicio', 'tipo_contrato']);
        $this->items = collect();

        return redirect()->route('ordenes-prod')->with('success', 'Orden de compra de empleado creada correctamente');
    }

    public function updateOC()
    {
        if ($this->items->count() <= 0) {
            $this->addError('items-error', 'No se han agregado items a la orden de compra');
            return;
        }

        $this->queriedOrden->update([
            'estado_id' => 2,
            'fecha_envio_produccion' => now()
        ]);

        // Reemplazar ítems
        $this->queriedOrden->ordenItems()->delete();

        foreach ($this->items as $item) {
            OcItem::create([
                'oc_id' => $this->queriedOrden->id,
                'item_id' => $item['item']['id'],
                'desc_oc' => $item['item']['nombre'],
                'cant_oc' => $item['cant'],
                'dias_oc' => $item['dias'],
                'otros_oc' => $item['otros'],
                'vunit_oc' => $item['valor_unitario'],
                'vtotal_oc' => $item['valor_total'],
                'tipo_servicio' => $item['tipo_servicio'],
                'tipo_contrato' => $item['tipo_contrato'],
            ]);
        }

        return redirect()->route('ordenes-prod')->with('success', 'Información de orden actualizada correctamente');
    }

    public function deleteOrden()
    {
        if ($this->queriedOrden) {
            $this->queriedOrden->ordenItems()->delete();
            $this->queriedOrden->delete();
        }

        return redirect()->route('ordenes-prod')->with('success', 'Orden de compra eliminada correctamente');
    }

    public function getData()
    {
        $this->productor = $this->queriedOrden->productor ?? null;
        $queriedItems = $this->queriedOrden->ordenItems;

        foreach ($queriedItems as $item) {
            $newItem = [
                'proyecto' => [
                    'id' => $item->itemPresupuesto->presto->id ?? null,
                    'nombre' => $item->itemPresupuesto->presto->gestion->nom_proyecto_cot ?? 'Proyecto',
                    'cod_cc' => $item->itemPresupuesto->presto->cod_cc ?? ''
                ],
                'item' => [
                    'id' => $item->itemPresupuesto->id ?? null,
                    'nombre' => $item->itemPresupuesto->descripcion ?? '',
                    'cod_cc' => $item->itemPresupuesto->presto->cod_cc ?? '',
                    'display_item' => $item->itemPresupuesto->num_item ?? ''
                ],
                'cant' => $item->cant_oc,
                'dias' => $item->dias_oc,
                'otros' => $item->otros_oc,
                'valor_unitario' => $item->vunit_oc,
                'valor_total' => $item->vtotal_oc,
                'tipo_servicio' => $item->tipo_servicio,
                'tipo_contrato' => $item->tipo_contrato,
            ];

            $this->items->push($newItem);
        }
    }

    public function getItemLimite(){
        $item_info = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();
        if (!$item_info) return;

        $excludeId = $this->queriedOrden ? $this->queriedOrden->id : null;
        $consumidos = $item_info->consumidos();
        if ($excludeId) {
            $consumidos->where('oc_id', '!=', $excludeId);
        }

        $sumCant = $consumidos->get()->sum('cant_oc');
        $sumTotal = $consumidos->get()->sum('vtotal_oc');

        $this->limiteCantidad = ($item_info->cantidad - $sumCant);
        $this->limiteValorTotal = ($item_info->v_total - $sumTotal);
        $this->limiteDias = $item_info->dia;
        $this->limiteOtros = $item_info->otros;
        $this->limiteValorUnitario = $item_info->v_unitario;
    }


    public function updatedPresupuesto()
    {
        if ($this->presupuesto) {
            $presupuestoObj = $this->presupuestos->where('id', $this->presupuesto)->first();
            if ($presupuestoObj) {
                $this->items_presupuesto = $presupuestoObj->presupuestoItems->filter(function($item) {
                    return Str::contains($item->proveedor, 's:1:"4"') && $item->disponible == 1;
                });
            }
        } else {
            $this->items_presupuesto = collect();
        }

        $this->resetFields(['item_presupuesto', 'cantidad', 'dias', 'otros', 'valor_unitario', 'valor_total']);
    }

    public function updatedItemPresupuesto()
    {
        foreach ($this->items as $item) {
            if ($item['item']['id'] == $this->item_presupuesto) {
                $this->addError('items-error', 'Este item ya fue agregado en esta orden de compra.');
                return;
            }
        }

        if ($this->item_presupuesto) {
            $this->getItemLimite();
            $this->cantidad = $this->limiteCantidad;
            $this->dias = $this->limiteDias;
            $this->otros = $this->limiteOtros;
            $this->valor_unitario = $this->limiteValorUnitario;
            $this->valor_total = $this->limiteValorTotal;
        }
    }

    public function updatedCantidad() { $this->getValorTotal(); }
    public function updatedDias() { $this->getValorTotal(); }
    public function updatedOtros() { $this->getValorTotal(); }
    public function updatedValorUnitario() { $this->getValorTotal(); }

    public function getValorTotal()
    {
        $this->valor_total = ($this->cantidad * $this->valor_unitario);
    }

    public function resetFields($fields)
    {
        foreach ($fields as $field) {
            $this->$field = '';
        }
    }
    public function render()
    {
        $this->getPresupuestos();
        return view('livewire.productor.ordenes.empleados');
    }
}