<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use Livewire\WithFileUploads;

class PropuestaForm extends Component
{
    use WithFileUploads; // Habilita la subida de archivos con Livewire

    // Models
    public $nom_proyecto; // Nombre del proyecto
    public $presupuesto;  // Presupuesto de la propuesta
    public $fecha;        // Fecha estimada de la propuesta
    public $cotizacionUrl; // URL de la propuesta/cotización

    // Useful vars
    // Se decide utilizar "lead" como referencia a los registros de la tabla Gestion Comercial
    public $lead_id = 0; // ID del registro en Gestión Comercial

    // Renderiza la vista principal del formulario de propuesta
    public function render()
    {
        return view('livewire.com.gestion-comercial.forms.propuesta-form');
    }

    // Valida el campo presupuesto cuando se actualiza
    public function updatedPresupuesto (){
        $this->validate([
            'presupuesto' => 'required|numeric'
        ]);
    }

    // Valida el campo nombre de proyecto cuando se actualiza
    public function updatedNomProyecto (){
        $this->validate([
            'nom_proyecto' => 'required|string'
        ]);
    }

    // Valida el campo URL de la cotización cuando se actualiza
    public function updatedCotizacionUrl (){
        $this->validate([
            'cotizacionUrl' => 'required|string',
        ]);
    }

    // Valida el campo fecha cuando se actualiza
    public function updatedFecha (){
        $this->validate([
            'fecha' => 'required|date'
        ]);
    }

    // Guarda la propuesta y actualiza el registro en Gestión Comercial
    public function store (){
        $this->validate([
            'presupuesto' => 'required|numeric',
            'nom_proyecto' => 'required|string',
            'cotizacionUrl' => 'required|max:1024',
            'fecha' => 'required|date'
        ]);

        // Busca el registro de gestión comercial (lead) y actualiza los campos
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->presto_prop = $this->presupuesto;
        $lead->nom_proyecto_prop = $this->nom_proyecto;
        $lead->fecha_estimada_prop = $this->fecha;
        $lead->propuesta_url = $this->cotizacionUrl;
        $lead->id_estado = 4; // Estado 4 = propuesta registrada
        $lead->update();

        // Redirige según el rol del usuario con mensaje de éxito
        if (Auth::user()->rol == 2){
            return redirect()->route('gestion-comercial')->with('success', '¡Propuesta registrada exitosamente!');
        }elseif (Auth::user()->rol == 5){
            return redirect()->route('asis-gestion-comercial')->with('success', '¡Cotización registrada exitosamente!');
        }
    }
}
