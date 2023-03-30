<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules; 
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use Livewire\WithFileUploads;

class DescicionForm extends Component
{
    public $causa;

    // Useful vars
    // Se decide utilizar "lead" como referencia a los registros del la tabla Gestion Comercial
    public $lead_id = 0;

    public function render()
    {
        return view('livewire.com.gestion-comercial.forms.descicion-form');
    }
 
    public function updateCausa(){
        $this->validate([
            'causa' => 'required|string'
        ]);
    }
     
    public function storePerdido(){
        $this->validate([
            'causa' => 'required|string'
        ]);
        
        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->causa = $this->causa;
        $lead->id_estado = 6;
        $lead->update();

        return redirect()->route('gestion-comercial')->with('success', '¡Proyecto marcado como perdido exitosamente!');
    }
}
