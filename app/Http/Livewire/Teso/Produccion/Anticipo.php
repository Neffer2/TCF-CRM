<?php

namespace App\Http\Livewire\Teso\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use Livewire\WithFileUploads;
use App\Traits\Email;

class Anticipo extends Component
{
    use WithFileUploads, Email; // Habilita la subida de archivos y el uso de funciones de email

    // Modelos para los campos del formulario
    public $comprobante, $observacion_anticipo;

    // Variable para almacenar la orden de compra asociada
    public $orden;

    // Variable para recibir el id de la orden (rellenada al montar el componente)
    public $orden_id;

    // Renderiza la vista principal del componente
    public function render()
    {
        return view('livewire.teso.produccion.anticipo');
    }

    // Guarda el comprobante de pago del anticipo
    public function store(){
        // Si ya existe un comprobante, muestra error y no permite continuar
        if ($this->orden->archivo_comprobante_pago){
            $this->addError('error', 'Este anticipo ya fué pagado');
            return redirect()->back();
        }

        // Valida los campos requeridos
        $this->validate([
            'comprobante' => 'required|file|mimes:pdf|max:10000',
            'observacion_anticipo' => 'nullable|string'
        ]);

        // Guarda el archivo del comprobante y actualiza la orden
        $this->orden->archivo_comprobante_pago = $this->comprobante->store('public/ordenes_juridicas/anticipos');
        $this->orden->update();

        // Envía correo (comentado)
        // $this->mailAnticipoPagado($this->orden, $this->observacion_anticipo);

        // Redirige con mensaje de éxito
        return redirect()->route('anticipos')->with('success', 'Anticipo marcado como pagado exitósamente.');
    }

    // Al montar el componente, busca la orden de compra por id
    public function mount(){
        $this->orden = OrdenCompra::find($this->orden_id);
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
