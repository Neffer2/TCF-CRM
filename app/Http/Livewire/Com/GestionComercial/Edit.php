<?php

namespace App\Http\Livewire\Com\GestionComercial; 

use Livewire\Component;
use App\Models\Contacto;
use App\Models\User;
use App\Models\GestionComercial;
use Illuminate\Validation\Rules; 
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class Edit extends Component
{ 
    use WithFileUploads;

    // Models contacto
    public $id_contacto;
    
    // Models oportunidad
    public $tipo_contacto;
    public $desc_contacto;
  
    // Models Cotizacion
    public $nom_proyecto; 
    public $presupuesto; 
    public $fecha;
    public $porcentaje;
    public $com_2; 
    public $cotizacionFile;

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
    public $porcentajes = ['100', '50'];
    public $comerciales = [];

    public function render()
    { 
        return view('livewire.com.gestion-comercial.edit');
    } 

    public function mount($leadId){
        $this->getContactos();
        $this->getGestion($leadId);  
        $this->getComerciales();
    } 
 
    public function getContactos(){
        $this->contactos = Contacto::select('id', 'nombre', 'apellido', 'empresa')->where('id_user', Auth::id())->orderBy('id', 'asc')->get();
    }

    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->where('id', '<>', Auth::id())->get();
    }

    public function getGestion($id){
        $this->stored = GestionComercial::where('id', $id)->orderBy('id', 'asc')->first();

        $this->id_contacto = $this->stored->id_contacto;

        $this->tipo_contacto = $this->stored->tipo_contacto;
        $this->desc_contacto = $this->stored->desc_contacto; 

        $this->nom_proyecto = $this->stored->nom_proyecto_cot; 
        $this->presupuesto = $this->stored->presto_cot; 
        $this->fecha = $this->stored->fecha_estimada_cot; 
        $this->porcentaje = $this->stored->porcentaje; 
        $this->com_2 = $this->stored->comercial_2; 
        // $this->cotizacionFile = $this->stored->cotizacion_file; 

        $this->nom_proyecto_prop = $this->stored->nom_proyecto_prop;
        $this->presupuesto_prop = $this->stored->presto_prop;
        $this->fecha_prop = $this->stored->fecha_estimada_prop;
        $this->cotizacionUrl_prop = $this->stored->propuesta_url;
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

    public function updatedNomProyecto(){
        $this->validate([
            'nom_proyecto' => 'required|string'
        ]);
    }

    public function updatedPresupuesto(){
        $this->validate([ 
            'presupuesto' => 'required|numeric'
        ]);
    }

    public function updatedPorcentaje(){
        $this->validate([
            'porcentaje' => 'required|numeric'
        ]);
    }

    public function updatedFecha(){
        $this->validate([
            'fecha' => 'required|date'
        ]);
    }

    public function updatedCom2(){
        $this->validate([
            'com_2' => 'required|numeric'
        ]);
    }

    public function updatedCotizacionFile(){
        $this->validate([
            'cotizacionFile' => 'required|max:2024',
        ]);
    }

    public function updatedNomProyectoProp(){
        $this->validate([
            'nom_proyecto_prop' => 'required|string'
        ]);
    }

    public function updatedPresupuestoProp(){
        $this->validate([
            'presupuesto_prop' => 'required|numeric'
        ]);
    }

    public function updatedFechaProp(){
        $this->validate([
            'fecha_prop' => 'required|date'
        ]);
    }

    public function updatedCotizacionUrlProp(){
        $this->validate([
            'cotizacionUrl_prop' => 'required|string'
        ]);
    }

    // DB updates
    public function updateContacto(){
        $this->validate([
            'id_contacto' => 'required|numeric'
        ]);         
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

    public function updateCotizacion(){
        $this->validate([
            'nom_proyecto' => 'required|string',
            'presupuesto' => 'required|string',
            'fecha' => 'required|date',
            'porcentaje' => 'required|numeric'
        ]);

        $gestion = $this->stored;

        if ($this->porcentaje == 50){
            $this->validate([
                'com_2' => 'required|numeric',
            ]);
            $gestion->comercial_2 = $this->com_2;
        }else {
            $gestion->comercial_2 = null;
        }

        $gestion->nom_proyecto_cot = $this->nom_proyecto;
        $gestion->presto_cot = $this->presupuesto; 
        $gestion->fecha_estimada_cot = $this->fecha;
        $gestion->porcentaje = $this->porcentaje;

        if ($this->cotizacionFile){
            $this->validate([
                'cotizacionFile' => 'required|max:1024'
            ]);
            $gestion->cotizacion_file = $this->cotizacionFile->store('cotizaciones');
        }

        $gestion->update();  

        return redirect()->back()->with('success', 'Contacto actualizado exitosamente')->withInput();
    }

    public function updatePropuesta(){
        $this->validate([
            'nom_proyecto_prop' => 'required|string',
            'presupuesto_prop' => 'required|numeric',
            'fecha_prop' => 'required|date',
            'cotizacionUrl_prop' => 'required|string'
        ]);

        $gestion = $this->stored;
        $gestion->nom_proyecto_prop = $this->nom_proyecto_prop;
        $gestion->presto_prop = $this->presupuesto_prop; 
        $gestion->fecha_estimada_prop = $this->fecha_prop; 
        $gestion->propuesta_url = $this->cotizacionUrl_prop; 
         
        $gestion->update();   

        return redirect()->back()->with('success', 'Contacto actualizado exitosamente')->withInput();
    }

    public function deleteProspecto(){ 
        $gestion = $this->stored;
        $gestion->delete();
    
        if (Auth::user()->rol == 2){ 
            return redirect()->route('gestion-comercial')->with('success', 'Prospecto eliminado exitosamente');
        }elseif (Auth::user()->rol == 5){
            return redirect()->route('asis-gestion-comercial')->with('success', 'Prospecto eliminado exitosamente');
        }
    }
}
