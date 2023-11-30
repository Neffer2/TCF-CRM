<?php

namespace App\Http\Livewire\Cont\Produccion;


use Livewire\Component;
use App\Models\OrdenCompra;
use Livewire\WithFileUploads;
use App\Traits\Email;

class Anticipo extends Component
{
    use WithFileUploads, Email;

    // Models 
    public $causa_cod, $observacion_causacion;

    // Useful vars
    public $orden;

    // Filled
    public $orden_id;


    public function render()
    {
        return view('livewire.cont.produccion.anticipo');
    }

    public function store(){      
        if ($this->orden->archivo_comprobante_pago){
            $this->addError('error', 'Este anticipo ya fué pagado');
            return redirect()->back();
        }

        $this->validate([ 
            'causa_cod' => 'required|numeric',
            'observacion_causacion' => 'nullable|string'
        ]);  

        $this->orden->cod_causal = $this->causa_cod; 
        $this->orden->observacion_causal = $this->observacion_causacion; 
        $this->orden->update();

        // $this->mailAnticipoPagado($this->orden, $this->observacion_anticipo);
        return redirect()->route('anticipos-contabilidad')->with('success', 'Anticipo causado exitósamente.');
    }

    public function mount(){
        $this->orden = OrdenCompra::find($this->orden_id);
    }

    /* ACTUALIZAIONNES */
    public function updatedCausaCod(){
        $this->validate([
            'causa_cod' => 'required|numeric'
        ]); 
    }

    public function updatedObservacionCausacion(){
        $this->validate([
            'observacion_causacion' => 'nullable|string'
        ]);
    }
    /* *** */
}
