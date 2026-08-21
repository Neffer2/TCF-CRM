<?php

namespace App\Http\Livewire\Tesoreria\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\Anticipo as AnticipoModel;
use Livewire\WithFileUploads;

class Anticipo extends Component
{
    use WithFileUploads;

    // Modelos para los campos del formulario
    public $comprobante, $observacion_anticipo;

    // Variable para almacenar la orden de compra asociada
    public $orden, $anticipo;

    // Filled
    public $anticipo_id;

    public function render()
    {
        return view('livewire.tesoreria.produccion.anticipo');
    }

    // Guarda el comprobante de pago del anticipo
    public function store(){
        // Si ya existe un comprobante, muestra error y no permite continuar
        if ($this->anticipo->archivo_comprobante_pago){
            $this->addError('error', 'Este anticipo ya fué pagado');
            return redirect()->back();
        }

        // Valida los campos requeridos
        $this->validate([
            'comprobante' => 'required|file|mimes:pdf|max:10000',
            'observacion_anticipo' => 'nullable|string'
        ]);

        // Guarda el archivo del comprobante y actualiza la orden 
        $this->anticipo->comprobante_pago = $this->comprobante->store('public/anticipos');
        $this->anticipo->fecha_comprobante_pago = now();
        $this->anticipo->update();

        // $this->mailAnticipoPagado($this->orden, $this->observacion_anticipo);

        // Redirige con mensaje de éxito
        return redirect()->route('lista-anticipos-tesoreria')->with('success', 'Anticipo marcado como pagado exitósamente.');
    }

    // Al montar el componente, busca la orden de compra por id
    public function mount(){
        $this->orden = AnticipoModel::find($this->anticipo_id)->ordenCompra; 
        $this->anticipo = AnticipoModel::find($this->anticipo_id);
    }

    /* ACTUALIZACIONES */

    // Valida el comprobante cuando se actualiza el campo
    public function updatedComprobante(){
        $this->validate([
            'comprobante' => 'required|file|mimes:pdf|max:10000'
        ]);
    }

    // Valida la observación cuando se actualiza el campo
    public function updatedObservacionAnticipo(){
        $this->validate([
            'observacion_anticipo' => 'nullable|string'
        ]);
    }
}
