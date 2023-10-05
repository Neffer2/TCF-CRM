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
    public $verifyPresupuesto = false; 

    public $id_presupuesto;
    
    public function render() 
    {
        $this->verifyStatus(); 
        return view('livewire.productor.solicitar-recursos'); 
    }
 
    public function mount(){  
        $this->presupuesto = PresupuestoProyecto::find($this->id_presupuesto); 
    }

    public function internoPdf(){  
        return redirect()->route('cotizacion', ['prespuesto' => $this->presupuesto->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 0]);
    }

    public function internoExcel(){   
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->presupuesto->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 0]);
    }

    // Verifica que el presupuesto está aprobado
    public function verifyStatus(){
        if ($this->presupuesto->estado_id == 1){
            $this->verifyPresupuesto = true;
        }
    }
}
