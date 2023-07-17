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

    // Useful vars
    public $productores = [];
    public $proyectos = [];
    public $comerciales = [];
    public $asignados = [];

    public function render()
    {
        return view('livewire.lider-produccion.asignar-proyecto');
    }

    public function mount(){
        $this->getUsers();
        $this->getProyectos();
    } 
 
    public function getUsers(){
        $this->productores = User::select('id', 'name')->where('rol', 7)->get(); 
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get(); 
    }

    public function getProyectos(){
        $this->proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('estado_id', 1)->where('productor', null)->get(); 
    }

    public function getAsigandos(){
        $this->validate([
            'productor' => ['required', 'string']
        ]);      
 
        $this->asignados = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('productor', $this->productor)->get();
    }

    public function asignar(){
        $this->validate([
            'proyecto' => ['required', 'string'],
            'productor' => ['required', 'string']
        ]);      
 
        $presupuesto = PresupuestoProyecto::find($this->proyecto);
        $presupuesto->productor = $this->productor;
        $presupuesto->update(); 

        $this->ResetView();
        return redirect()->back()->with('success', 'Proyecto asignado exitosamente.'); 
    }

    public function liberar(){
        $this->validate([
            'asignado' => ['required', 'string'],
            'productor' => ['required', 'string']
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

    public function updatedProductor(){
        $this->validate(['productor' => ['required', 'string']]);
        $this->getAsigandos();
    }

    public function updatedAsignado(){
        $this->validate(['asignado' => ['required', 'string']]); 
    }

    public function ResetView(){
        $this->proyecto = null;
        $this->proyectos = [];

        $this->asignado = null;
        $this->asignados = [];

        $this->getProyectos();
        $this->getAsigandos();
    }
}
