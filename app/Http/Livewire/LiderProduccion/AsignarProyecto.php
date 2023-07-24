<?php

namespace App\Http\Livewire\LiderProduccion;
 
use Livewire\Component;
use App\Models\User;
use App\Models\PresupuestoProyecto;

class AsignarProyecto extends Component 
{
    // Models
    public $proyecto;
    public $productor;
    public $asignado; 
    public $comercial; 

    // Useful vars
    public $proyectos = [];
    public $comerciales = []; 
    public $asignados = [];

    public $id_productor;

    public function render()
    {
        $this->getUsers();
        $this->getProyectos();
        return view('livewire.lider-produccion.asignar-proyecto');
    }

    public function mount(){
        $this->getAsigandos();
    }  
 
    public function getUsers(){
        $this->productor = User::select('name', 'avatar')->find($this->id_productor);
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }  
 
    public function getProyectos(){
        $this->proyectos = [];        
        // Filter
        if ($this->comercial){
            PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('estado_id', 1)->where('productor', null)->get()->map(function ($item){
                if ($item->gestion->id_user == $this->comercial){
                    array_push($this->proyectos, $item);
                }
            });
        }else {
            $this->proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('estado_id', 1)->where('productor', null)->get(); 
        }
    }

    public function getAsigandos(){   
        $this->asignados = []; 
        $this->asignados = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('productor', $this->id_productor)->get();
    }

    public function asignar(){
        $this->validate([
            'proyecto' => ['required', 'string']
        ]);      
 
        $presupuesto = PresupuestoProyecto::find($this->proyecto);
        $presupuesto->productor = $this->id_productor;
        $presupuesto->update(); 

        $this->ResetView();
        return redirect()->back()->with('success', 'Proyecto asignado exitosamente.'); 
    }

    public function liberar(){
        $this->validate([
            'asignado' => ['required', 'string'],
        ]);        
 
        $presupuesto = PresupuestoProyecto::find($this->asignado);
        $presupuesto->productor = null;
        $presupuesto->update(); 

        $this->ResetView();
        return redirect()->back()->with('success', 'Proyecto liberado exitosamente.'); 
    }

    // Updates
    public function updatedProyecto(){
        $this->validate(['proyecto' => ['required', 'string']]);
    }

    public function updatedAsignado(){
        $this->validate(['asignado' => ['required', 'string']]); 
    }

    public function updatedComercial(){
        $this->validate(['comercial' => ['numeric']]);
        $this->getProyectos();
    }

    public function ResetView(){
        $this->proyecto = NULL;
        $this->asignado = NULL;

        $this->getProyectos();
        $this->getAsigandos();
    }
}
