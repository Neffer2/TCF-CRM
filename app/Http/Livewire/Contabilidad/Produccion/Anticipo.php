<?php

namespace App\Http\Livewire\Contabilidad\Produccion;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\Anticipo as AnticipoModel;
 
class Anticipo extends Component
{
    // Variables para el formulario
    public $causa_cod, $observacion_causacion;

    // Variable para almacenar la orden de compra
    public $orden, $anticipo;

    // Filled 
    public $anticipo_id;

    public function render()
    {
        return view('livewire.contabilidad.produccion.anticipo');
    }

    // Guarda la causación del anticipo
    public function store(){ 
        // Si ya existe un comprobante de pago, muestra error y redirige
        if ($this->anticipo->comprobante_pago){
            $this->addError('error', 'Este anticipo ya fué pagado');
            return redirect()->back();
        }

        // Valida los campos requeridos
        $this->validate([
            'causa_cod' => 'required|numeric',
            'observacion_causacion' => 'nullable|string'
        ]);  

        // Actualiza los datos de la orden
        $this->anticipo->cod_causal = $this->causa_cod; 
        $this->anticipo->observacion_causal = $this->observacion_causacion;
        $this->anticipo->fecha_causal = now();
        $this->anticipo->estado_id = 5; // Comprobado
        $this->anticipo->update();

        // $this->ocNaturalRevisionTesoreria($this->orden);   

        // Redirige con mensaje de éxito
        return redirect()->route('lista-anticipos-contabilidad')->with('success', 'Orden de compra causada exitósamente.');
    }

    // Rechaza la causación del anticipo y actualiza el estado
    public function rechazar(){
        // Valida que la observación sea obligatoria
        $this->validate([
            'observacion_causacion' => 'required|string'
        ]);

        // Limpia el código causal y actualiza la observación y estado
        $this->anticipo->cod_causal = null;
        $this->anticipo->observacion_causal = $this->observacion_causacion;
        $this->anticipo->estado_id = 2; // Estado: Revisión
        $this->anticipo->update();

        // $this->ocNaturalContabilidadRechazo($this->orden);

        // Redirige con mensaje de éxito
        return redirect()->route('lista-anticipos-contabilidad')->with('success', 'Orden de compra rechazada exitósamente.');
    }

    // Al montar el componente, carga la orden de compra correspondiente
    public function mount(){
        $this->orden = AnticipoModel::find($this->anticipo_id)->ordenCompra;
        $this->anticipo = AnticipoModel::find($this->anticipo_id);
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
 