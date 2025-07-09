<?php

namespace App\Http\Livewire\Productor;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\Proveedor;

class SolicitarRecursos extends Component
{
    // Variables útiles para el formulario y lógica
    public $presupuesto, $proveedores = [], $verifyPresupuesto = false, $id_presupuesto;

    // Escucha el evento 'ordenCreada' y ejecuta el método mount cuando ocurre
    protected $listeners = ['ordenCreada' => 'mount'];

    // Renderiza la vista asociada al componente y verifica el estado del presupuesto
    public function render()
    {
        $this->verifyStatus();
        return view('livewire.productor.solicitar-recursos');
    }

    // Se ejecuta al montar el componente, carga el presupuesto y los proveedores
    public function mount(){
        $this->presupuesto = PresupuestoProyecto::find($this->id_presupuesto);
        $this->proveedores = Proveedor::select('id', 'tercero')->get();
    }

    // Redirige a la ruta para descargar la cotización en PDF
    public function internoPdf(){
        return redirect()->route('cotizacion', [
            'prespuesto' => $this->presupuesto->id_gestion,
            'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot,
            'tipo' => 0
        ]);
    }

    // Redirige a la ruta para descargar la cotización en Excel
    public function internoExcel(){
        return redirect()->route('cotizacionExcel', [
            'prespuesto' => $this->presupuesto->id_gestion,
            'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot,
            'tipo' => 0
        ]);
    }

    // Verifica si el presupuesto está aprobado (estado_id == 1)
    public function verifyStatus(){
        if ($this->presupuesto->estado_id == 1){
            $this->verifyPresupuesto = true;
        }
    }
}
