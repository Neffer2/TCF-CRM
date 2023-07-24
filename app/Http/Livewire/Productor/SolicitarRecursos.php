<?php

namespace App\Http\Livewire\Productor;

use Livewire\Component;
use App\Models\ItemPresupuesto;
use App\Models\PresupuestoProyecto;
 
class SolicitarRecursos extends Component 
{ 
    // Models

    // Useful vars 
    public $presupuesto;
    public $presupuestoItems; 

    public $id_presupuesto;
    

    public function render()
    {
        return view('livewire.productor.solicitar-recursos');
    }

    public function mount(){
        $this->presupuesto = PresupuestoProyecto::find($this->id_presupuesto);   
        $this->presupuestoItems = ItemPresupuesto::where('presupuesto_id', $this->id_presupuesto)->get();
    }

    public function internoPdf(){  
        return redirect()->route('cotizacion', ['prespuesto' => $this->presupuesto->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 0]);
    }

    public function internoExcel(){   
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->presupuesto->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 0]);
    }
}
