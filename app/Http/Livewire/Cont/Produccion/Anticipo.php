<?php

namespace App\Http\Livewire\Cont\Produccion;


use Livewire\Component;
use App\Models\OrdenCompra;
use Livewire\WithFileUploads;
use App\Traits\Email;

class Anticipo extends Component
{
    // Habilita la subida de archivos y el trait de envío de emails
    use WithFileUploads, Email;

    // Variables para el formulario
    public $causa_cod, $observacion_causacion;

    // Variable para almacenar la orden de compra
    public $orden;

    // ID de la orden de compra (rellenado al montar el componente)
    public $orden_id;

    // Renderiza la vista asociada al componente
    public function render()
    {
        return view('livewire.cont.produccion.anticipo');
    }

    // Guarda la causación del anticipo
    public function store(){
        // Si ya existe un comprobante de pago, muestra error y redirige
        if ($this->orden->archivo_comprobante_pago){
            $this->addError('error', 'Este anticipo ya fué pagado');
            return redirect()->back();
        }

        // Valida los campos requeridos
        $this->validate([
            'causa_cod' => 'required|numeric',
            'observacion_causacion' => 'nullable|string'
        ]);  

        // Actualiza los datos de la orden
        $this->orden->cod_causal = $this->causa_cod; 
        $this->orden->observacion_causal = $this->observacion_causacion;
        $this->orden->update();

        $this->ocNaturalRevisionTesoreria($this->orden);   

        // Redirige con mensaje de éxito
        return redirect()->route('anticipos-contabilidad')->with('success', 'Orden de compra causada exitósamente.');
    }

    // Rechaza la causación del anticipo y actualiza el estado
    public function rechazar(){
        // Valida que la observación sea obligatoria
        $this->validate([
            'observacion_causacion' => 'required|string'
        ]);

        // Limpia el código causal y actualiza la observación y estado
        $this->orden->cod_causal = null;
        $this->orden->observacion_causal = $this->observacion_causacion;
        $this->orden->estado_id = 2; // Estado: Revisión
        $this->orden->update();

        // $this->ocNaturalContabilidadRechazo($this->orden);

        // Redirige con mensaje de éxito
        return redirect()->route('anticipos-contabilidad')->with('success', 'Orden de compra rechazada exitósamente.');
    }

    // Al montar el componente, carga la orden de compra correspondiente
    public function mount(){
        $this->orden = OrdenCompra::find($this->orden_id);
    }

    /* MÉTODOS DE ACTUALIZACIÓN EN TIEMPO REAL */

    // Valida el campo causa_cod cuando se actualiza
    public function updatedCausaCod(){ 
        $this->validate([
            'causa_cod' => 'required|numeric'
        ]);
    }

    // Valida el campo observacion_causacion cuando se actualiza
    public function updatedObservacionCausacion(){
        $this->validate([
            'observacion_causacion' => 'nullable|string'
        ]);
    }
}
