<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use Livewire\WithFileUploads;

class DescicionForm extends Component
{
    public $causa; // Variable para almacenar la causa del proyecto perdido

    // Variables útiles
    // Se decide utilizar "lead" como referencia a los registros de la tabla Gestión Comercial
    public $lead_id = 0;

    // Renderiza la vista principal del formulario de decisión
    public function render()
    {
        return view('livewire.com.gestion-comercial.forms.descicion-form');
    }

    // Valida el campo causa cuando se actualiza
    public function updateCausa(){
        $this->validate([
            'causa' => 'required|string'
        ]);
    }

    // Marca el proyecto como perdido y guarda la causa
    public function storePerdido(){
        $this->validate([
            'causa' => 'required|string'
        ]);

        // Busca el registro de gestión comercial (lead) y actualiza los campos
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->causa = $this->causa;
        $lead->id_estado = 6; // Estado 6 = perdido
        $lead->update();

        // Redirige con mensaje de éxito
        return redirect()->route('gestion-comercial')->with('success', '¡Proyecto marcado como perdido exitosamente!');
    }
}
