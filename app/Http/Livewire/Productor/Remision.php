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
        $this->storedOrden = OrdenCompra::with('ordenItems')->find($this->orden);

        if ($this->storedOrden && $this->storedOrden->ordenItems) {
            $this->ocItems = $this->storedOrden->ordenItems->map(function ($item, $index) {
                return [
                    'id'       => $item->id,
                    'num_item' => $item->num_item,
                    'cant'     => $item->cant_oc ?? 0,
                    'dias'     => $item->dias_oc ?? 0,
                    'otros'    => $item->otros_oc ?? 0,
                    'desc'     => $item->desc_oc ?? '',
                    'vUnit'    => $item->vunit_oc ?? 0,
                    'vTotal'   => $item->vtotal_oc ?? 0,
                ];
            })->toArray();
        }
    }

    public function updatedOcItems($value, $key)
    {
        // $key viene en formato "index.campo", por ejemplo: "0.cant", "1.dias", etc.
        $parts = explode('.', $key);
        
        if (count($parts) === 2) {
            $index = $parts[0];

            if (isset($this->ocItems[$index])) {
                $item = $this->ocItems[$index];

                $cant  = (float) ($item['cant'] ?? 0);
                $dias  = (float) ($item['dias'] ?? 0);
                $otros = (float) ($item['otros'] ?? 0);
                $vUnit = (float) ($item['vUnit'] ?? 0);

                // Aplicar factores para la fórmula
                $factorDias  = $dias > 0 ? $dias : 1;
                $factorOtros = $otros > 0 ? $otros : 1;

                // Recalcular el vTotal de inmediato
                $this->ocItems[$index]['vTotal'] = $cant * $factorDias * $factorOtros * $vUnit;
            }
        }
    }

    // El botón de guardar fila (💾) ahora solo persiste los cambios ya calculados en la Base de Datos
    public function updateItem($index)
    {
        if (!isset($this->ocItems[$index])) {
            return;
        }

        $itemData = $this->ocItems[$index];

        $ocItem = OcItem::find($itemData['id']);
        if ($ocItem) {
            $ocItem->update([
                'cant_oc'   => $itemData['cant'],
                'dias_oc'   => $itemData['dias'],
                'otros_oc'  => $itemData['otros'],
                'vunit_oc'  => $itemData['vUnit'],
                'vtotal_oc' => $itemData['vTotal'],
            ]);
        }

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