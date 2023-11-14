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
        if ($this->orden->archivo_comprobante_pago){
            $this->addError('error', 'Este anticipo ya fué pagado');
            return redirect()->back();
        }

        $this->validate([
            'comprobante' => 'required|file|mimes:pdf|max:10000',
            'observacion_anticipo' => 'nullable|string'
        ]);  

        $this->orden->archivo_comprobante_pago = $this->comprobante->store('public/ordenes_juridicas/anticipos'); 
        $this->orden->update();

        $this->mailAnticipoPagado($this->orden, $this->observacion_anticipo);
        return redirect()->route('anticipos')->with('success', 'Anticipo marcado como pagado exitósamente.');
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
