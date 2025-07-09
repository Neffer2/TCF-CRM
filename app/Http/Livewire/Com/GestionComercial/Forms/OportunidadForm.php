<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;

class OportunidadForm extends Component
{
    // Models
    public $contacto; // Tipo de contacto realizado (ej: llamada, reunión, etc.)
    public $descOportunidad; // Descripción de la oportunidad detectada

    // Useful vars
    public $tiposContacto = ['Llamada', 'Mailing', 'Reunión presencial', 'Reunión virtual', 'Mensaje de texto']; // Opciones para el tipo de contacto

    // Se decide utilizar "lead" como referencia a los registros de la tabla Gestion Comercial
    public $lead_id = 0; // ID del registro en Gestión Comercial

    // Renderiza la vista principal del formulario de oportunidad
    public function render()
    {
        return view('livewire.com.gestion-comercial.forms.oportunidad-form');
    }

    // Valida el campo 'contacto' cuando se actualiza
    public function updatedContacto(){
        $this->validate(['contacto' => ['required', 'string']]);
    }

    // Valida el campo 'descOportunidad' cuando se actualiza
    public function updatedDescoportunidad(){
        $this->validate(['descOportunidad' => ['required', 'string', 'max:254']]);
    }

    // Guarda la oportunidad y actualiza el registro en Gestión Comercial
    public function store(){
        $this->validate([
            'contacto' => ['required', 'string'],
            'descOportunidad' => ['required', 'string', 'max:254']
        ]);

        // Busca el registro de gestión comercial (lead) y actualiza los campos
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->tipo_contacto = $this->contacto;
        $lead->desc_contacto = $this->descOportunidad;
        $lead->id_estado = 2; // Estado 2 = oportunidad registrada
        $lead->update();

        // Redirige según el rol del usuario con mensaje de éxito
        if (Auth::user()->rol == 2){
            return redirect()->route('gestion-comercial')->with('success', '¡Propuesta registrada exitosamente!');
        }elseif (Auth::user()->rol == 5){
            return redirect()->route('asis-gestion-comercial')->with('success', '¡Cotización registrada exitosamente!');
        }
    }
}
