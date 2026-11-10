<?php

namespace App\Http\Livewire\Productor;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use App\Traits\Email;
use Illuminate\Support\Facades\Storage;

class Remision extends Component
{
    use WithFileUploads, Email;

    public $remision, $observaciones;
    public $storedOrden;
    public $orden;
    public $ocItems = [];

    protected $listeners = ['store-signal' => 'store'];

    public function render()
    {
        return view('livewire.productor.remision');
    }

    public function mount()
    {
        $this->getData();
    }

    public function getData()
    {
        $this->storedOrden = OrdenCompra::with('ordenItems.itemPresupuesto')
            ->find($this->orden);

        if ($this->storedOrden && $this->storedOrden->ordenItems) {
            $this->ocItems = $this->storedOrden->ordenItems->map(function ($item) {
                $presu = $item->itemPresupuesto;

                return [
                    'id'       => $item->id,
                    'num_item' => $item->num_item,
                    'cant'     => $item->cant_oc ?? 0,
                    'dias'     => $item->dias_oc ?? 0,
                    'otros'    => $item->otros_oc ?? 0,
                    'desc'     => $item->desc_oc ?? '',
                    'vUnit'    => $item->vunit_oc ?? 0,
                    'vTotal'   => $item->vtotal_oc ?? 0,

                    'max_cant'   => $presu?->cantidad ?? 0,
                    'max_vUnit'  => $presu?->v_unitario ?? 0,
                    'max_vTotal' => $presu?->v_total ?? 0,
                ];
            })->toArray();
        }
    }
    public function updatedOcItems($value, $key)
    {
        $parts = explode('.', $key);

        if (count($parts) !== 2) {
            return;
        }

        [$index, $field] = $parts;

        if (!isset($this->ocItems[$index])) {
            return;
        }

        $item = $this->ocItems[$index];

        $cant  = (float) ($item['cant'] ?? 0);
        $dias  = (float) ($item['dias'] ?? 0);
        $otros = (float) ($item['otros'] ?? 0);
        $vUnit = (float) ($item['vUnit'] ?? 0);

        // Tope de cantidad
        if ($field === 'cant' && $cant > (float) $item['max_cant']) {
            $cant = (float) $item['max_cant'];
            $this->ocItems[$index]['cant'] = $cant;
            $this->addError("ocItems.$index.cant", "La cantidad no puede superar {$item['max_cant']}.");
        }

        // Tope de valor unitario
        if ($field === 'vUnit' && $vUnit > (float) $item['max_vUnit']) {
            $vUnit = (float) $item['max_vUnit'];
            $this->ocItems[$index]['vUnit'] = $vUnit;
            $this->addError("ocItems.$index.vUnit", "El valor unitario no puede superar {$item['max_vUnit']}.");
        }

        $factorDias  = $dias > 0 ? $dias : 1;
        $factorOtros = $otros > 0 ? $otros : 1;

        $vTotal = $cant * $factorDias * $factorOtros * $vUnit;

        // Tope del total
        if ($vTotal > (float) $item['max_vTotal']) {
            $vTotal = (float) $item['max_vTotal'];
            $this->addError("ocItems.$index.vTotal", "El valor total no puede superar {$item['max_vTotal']}.");
        }

        $this->ocItems[$index]['vTotal'] = $vTotal;
    }

    public function updateItem($index)
    {
        if (!isset($this->ocItems[$index])) {
            return;
        }

        $itemData = $this->ocItems[$index];

        $ocItem = OcItem::with('itemPresupuesto')->find($itemData['id']);
        if (!$ocItem) {
            return;
        }

        $presu = $ocItem->itemPresupuesto;
        if (!$presu) {
            $this->addError("ocItems.$index.cant", 'Este ítem no tiene un ítem de presupuesto asociado.');
            return;
        }

        $cant  = (float) $itemData['cant'];
        $dias  = (float) $itemData['dias'];
        $otros = (float) $itemData['otros'];
        $vUnit = (float) $itemData['vUnit'];

        // Recalcular en servidor, sin confiar en el vTotal del cliente
        $vTotal = round(
            $cant * ($dias > 0 ? $dias : 1) * ($otros > 0 ? $otros : 1) * $vUnit,
            2
        );

        // Topes inmutables del presupuesto
        $maxCant   = (float) $presu->cantidad;
        $maxVUnit  = (float) $presu->v_unitario;
        $maxVTotal = (float) $presu->v_total;

        if ($cant > $maxCant) {
            $this->addError("ocItems.$index.cant", "La cantidad no puede superar {$maxCant}.");
            return;
        }

        if ($vUnit > $maxVUnit) {
            $this->addError("ocItems.$index.vUnit", "El valor unitario no puede superar {$maxVUnit}.");
            return;
        }

        if ($vTotal > $maxVTotal) {
            $this->addError("ocItems.$index.vTotal", "El valor total no puede superar {$maxVTotal}.");
            return;
        }

        $ocItem->update([
            'cant_oc'   => $cant,
            'dias_oc'   => $dias,
            'otros_oc'  => $otros,
            'vunit_oc'  => $vUnit,
            'vtotal_oc' => $vTotal,
        ]);

        session()->flash('success_item', "Ítem #{$itemData['num_item']} actualizado y guardado correctamente.");
    }

    public function store($data)
    {
        $this->validate([
            'remision'      => 'required|file|mimes:pdf,xls,xlsx|max:10000',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $orden = OrdenCompra::find($this->orden);
        
        $orden->archivo_remision = $this->remision->store('public/remisiones');
        $orden->archivo_firma    = "public/firmas_produccion/$this->orden.png";
        $orden->observacion_remision = $this->observaciones;
        $orden->estado_id        = 8;

        $data_uri = $data;
        $encoded_image = explode(",", $data_uri)[1];
        $decoded_image = base64_decode($encoded_image);
        file_put_contents("storage/firmas_produccion/$this->orden.png", $decoded_image);

        $orden->save();

        return redirect()->route('dashboard-productor')->with('success', 'Remisión firmada y enviada exitósamente.');
    }

    public function updatedRemision()
    {
        $this->validate([
            'remision' => 'required|file|mimes:pdf,xls,xlsx|max:10000',
        ]);
    }

    public function updatedObservaciones()
    {
        $this->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);
    }
}