<?php

namespace App\Http\Livewire\Com\GestionComercial\Forms;

use Livewire\Component;
use Illuminate\Validation\Rules; 
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;

class OportunidadForm extends Component 
{    
    // Models
    public $contacto; 
    public $descOportunidad;  
     
    // Useful vars
    public $tiposContacto = ['Llamada', 'Mailing', 'Reunión presencial', 'Reunión virtual', 'Mensaje de texto'];

    // Se decide utilizar "lead" como referencia a los registros del la tabla Gestion Comercial
    public $lead_id = 0;

    public function render() 
    {   
        return view('livewire.com.gestion-comercial.forms.oportunidad-form');
    }
    
    public function updatedContacto(){ 
        $this->validate(['contacto' => ['required', 'string']]);
    } 

    public function updatedDescoportunidad(){
        $this->validate(['descOportunidad' => ['required', 'string', 'max:254']]);
    }

    public function store(){
        $this->validate([
            'contacto' => ['required', 'string'],
            'descOportunidad' => ['required', 'string', 'max:254']
        ]);

        $lead = GestionComercial::where('id', $this->lead_id)->first();
        $lead->tipo_contacto = $this->contacto;
        $lead->desc_contacto = $this->descOportunidad;
        $lead->id_estado = 2;
        $lead->update(); 

        if (Auth::user()->rol == 2){ 
            return redirect()->route('gestion-comercial')->with('success', '¡Propuesta registrada exitosamente!');
        }elseif (Auth::user()->rol == 5){
            return redirect()->route('asis-gestion-comercial')->with('success', '¡Cotización registrada exitosamente!');
        }
    } 
}
