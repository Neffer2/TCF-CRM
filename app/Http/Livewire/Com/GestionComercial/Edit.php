<?php

namespace App\Http\Livewire\Com\GestionComercial; 

use Livewire\Component;
use App\Models\Contacto;
use App\Models\GestionComercial;
use Illuminate\Validation\Rules; 
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{ 
    // Models contacto
    public $id_contacto;
    
    // Models oportunidad
    public $tipo_contacto;
    public $desc_contacto;
 
    // Models Cotizacion
    public $presupuesto; 
    public $nom_proyecto; 
    public $fecha;
    public $cotizacionFile;
    public $porcentaje;
    public $com_2; 

    // Models Propuesta
    public $nom_proyecto_prop;
    public $presupuesto_prop;
    public $fecha_prop;
    public $cotizacionUrl_prop; 

    // Models Venta
    public $causa;

    // Useful vars
    public $contactos = []; 
    public $stored;
    public $tiposContacto = ['Llamada', 'Mailing', 'Reunión presencial', 'Reunión virtual', 'Mensaje de texto'];

    public function render()
    { 
        return view('livewire.com.gestion-comercial.edit');
    } 

    public function mount($leadId){
        $this->getContactos();
        $this->getGestion($leadId);  
    } 
 
    public function getContactos(){
        $this->contactos = Contacto::select('id', 'nombre', 'apellido', 'empresa')->where('id_user', Auth::id())->orderBy('id', 'asc')->get();
    } 

    public function getGestion($id){
        $this->stored = GestionComercial::where('id', $id)->orderBy('id', 'asc')->first();

        $this->id_contacto = $this->stored->id_contacto;
        $this->tipo_contacto = $this->stored->tipo_contacto;
        $this->desc_contacto = $this->stored->desc_contacto; 
    }

    // updates
    public function updatedIdContacto(){
        $this->validate([
            'id_contacto' => 'required|numeric'
        ]);
    }

    public function updatedTipoContacto(){
        $this->validate([
            'tipo_contacto' => 'string'
        ]);
    }

    public function updatedDescContacto(){
        $this->validate([
            'desc_contacto' => 'string'
        ]);
    }


    // DB updates
    public function updateContacto(){
        $gestion = $this->stored;
        $gestion->id_contacto = $this->id_contacto;
        $gestion->update();
        
        return redirect()->back()->with('success', 'Contacto actualizado exitosamente')->withInput();
    }

    public function updateOportunidad(){
        $gestion = $this->stored;
        $gestion->tipo_contacto = $this->tipo_contacto;
        $gestion->desc_contacto = $this->desc_contacto; 
        $gestion->update();
        
        return redirect()->back()->with('success', 'Contacto actualizado exitosamente')->withInput();
    }
}
