<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules; 
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use App\Models\User;
use Livewire\WithFileUploads;

class CotizacionForm extends Component 
{    
    use WithFileUploads;

    // Models 
    public $presupuesto; 
    public $nom_proyecto;
    public $fecha;
    public $cotizacionFile;
    public $porcentaje;
    public $com_2;

    // Useful vars
    // Se decide utilizar "lead" como referencia a los registros del la tabla Gestion Comercial
    public $lead_id = 0;
    public $porcentajes = ['100', '50'];
    public $comerciales = [];

    public function render() 
    {
        $this->getComerciales();
        return view('livewire.com.gestion-comercial.forms.cotizacion-form');
    }

    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->where('id', '<>', Auth::id())->get();
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

    public function updatedCom2 (){
        $this->validate([
            'com_2' => 'required|numeric'
        ]);
    }

    public function updatedPorcentaje (){
        $this->validate([
            'porcentaje' => 'required|numeric'
        ]);
    }

    public function updatedCotizacionFile (){
        $this->validate([
            'cotizacionFile' => 'required|max:1024',
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
            'cotizacionFile' => 'required|max:1024',
            'porcentaje' => 'required|numeric',
            'fecha' => 'required|date'
        ]);
 
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->presto_cot = $this->presupuesto;
        $lead->comercial_2 = $this->com_2;
        $lead->porcentaje = $this->porcentaje;
        $lead->nom_proyecto_cot = $this->nom_proyecto;
        $lead->fecha_estimada_cot = $this->fecha;
        $lead->cotizacion_file = $this->cotizacionFile->store('cotizaciones');
        $lead->id_estado = 3;
        $lead->update();

        return redirect()->route('gestion-comercial')->with('success', '¡Cotización registrada exitosamente!');
    }
}
 