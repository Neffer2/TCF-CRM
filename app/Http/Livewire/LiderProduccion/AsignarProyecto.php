<?php

namespace App\Http\Livewire\LiderProduccion;
 
use Livewire\Component;
use App\Models\User;
use App\Models\Año; 
use App\Models\PresupuestoProyecto;

class AsignarProyecto extends Component 
{
    // Models
    public $proyecto, $productor, $asignado, $comercial; 

    // Useful vars
    public $proyectos, $comerciales, $asignados = [];

    // Filled
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
        $año = Año::orderBy('description', 'DESC')->first();
        $this->proyectos = [];
        $filter = [];
        $gestionFilter = [];
        $baseFilter = [];

        if ($this->comercial){
            array_push($gestionFilter, ['id_user', $this->comercial]);
        }

        array_push($filter, ['estado_id', 1]); 
        array_push($filter, ['productor', null]);

        array_push($baseFilter, ['id_estado', '<>', 4]); 
        array_push($baseFilter, ['id_estado', '<>', 1]);
        array_push($baseFilter, ['fecha', '>=', $año->meses->first()->f_inicio]);
        array_push($baseFilter, ['fecha', '<=', $año->meses->last()->f_fin]);
  
        $this->proyectos = PresupuestoProyecto::with('gestion', 'baseComercial')->select('id', 'id_gestion', 'cod_cc')
                            ->whereHas('gestion', function ($gestion) use ($gestionFilter){
                                $gestion->where($gestionFilter);
                            })
                            ->whereHas('baseComercial', function ($base) use ($baseFilter){
                                $base->where($baseFilter);
                            })
                            ->where($filter)
                            ->orderBy('created_at', 'desc')
                            ->get();
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
