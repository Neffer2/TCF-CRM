<?php

namespace App\Http\Livewire\Teso\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use Livewire\WithFileUploads;
use App\Traits\Email;

class Anticipo extends Component
{
    use WithFileUploads, Email;

    // Models 
    public $comprobante, $observacion_anticipo;

    // Useful vars
    public $orden;

    // Filled
    public $orden_id;

    public function render() 
    {
        return view('livewire.teso.produccion.anticipo');
    }

    public function store(){        
        $this->orden->archivo_comprobante_pago = $this->comprobante->store('public/ordenes_juridicas/anticipos'); 
        $this->orden->update();

        return redirect()->route('anticipos', ['success', 'Anticipo marcado como pagado exitósamente.']);
    }

    public function mount(){
        $this->orden = OrdenCompra::find($this->orden_id);
    }

    /* ACTUALIZAIONNES */
    public function updatedComprobante(){
        $this->validate([
            'comprobante' => 'required|file|mimes:pdf|max:10000'
        ]); 
    }

    public function updatedObservacionAnticipo(){
        $this->validate([
            'observacion_anticipo' => 'nullable|string'
        ]);
    }
    /* *** */
}
