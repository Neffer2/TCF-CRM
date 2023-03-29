<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules; 
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use Livewire\WithFileUploads;

class PropuestaForm extends Component
{   
    use WithFileUploads;

    // Models
    public $nom_proyecto;
    public $presupuesto; 
    public $fecha;
    public $cotizacionUrl;

    // Useful vars
    // Se decide utilizar "lead" como referencia a los registros del la tabla Gestion Comercial
    public $lead_id = 0;
     
    public function render()
    {
        return view('livewire.com.gestion-comercial.forms.propuesta-form');
    }

    public function updatedPresupuesto (){
        $this->validate([ 
            'presupuesto' => 'required|numeric'
        ]);
    }

    public function updatedNomProyecto (){
        $this->validate([
            'nom_proyecto' => 'required|string'
        ]);
    }

    public function updatedCotizacionUrl (){
        $this->validate([
            'cotizacionUrl' => 'required|string',
        ]);
    }

    public function updatedFecha (){
        $this->validate([
            'fecha' => 'required|date'
        ]);
    }

    public function store (){
        $this->validate([
            'presupuesto' => 'required|numeric',
            'nom_proyecto' => 'required|string',
            'cotizacionUrl' => 'required|max:1024',
            'fecha' => 'required|date'
        ]);
 
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->presto_prop = $this->presupuesto;
        $lead->nom_proyecto_prop = $this->nom_proyecto;
        $lead->fecha_estimada_prop = $this->fecha;
        $lead->propuesta_url = $this->cotizacionUrl;
        $lead->id_estado = 4;
        $lead->update();

        return redirect()->route('gestion-comercial')->with('success', '¡Propuesta registrada exitosamente!');
    }
}
