<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto; 
use App\Models\GestionComercial; 
use App\Models\User;  
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; 

class ActualizacionesPresto extends Component
{   
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //Models 
    // public $comercial = 0;
    public $fecha = 'asc';
    public $margen = 'asc';

    // Useful vars
    public $estados = []; 
    // public $comerciales = [];   

    public $rol; 

    public function render()    
    {   
        $filtros = [];
        array_push($filtros, ['cod_cc', '<>', null]);
        if ($this->rol == 1){ array_push($filtros, ['estado_id', '<>', 1]); }
 

        $presupuestos = PresupuestoProyecto::where($filtros)->orderBy('id', $this->fecha)->whereHas('gestion', function (Builder $query){
            $query->where('id_user', Auth::id());
        })->paginate(10);
        
        $registros = 0;  
        $registros = PresupuestoProyecto::where($filtros)->orderBy('id', $this->fecha)->whereHas('gestion', function (Builder $query){
            $query->where('id_user', Auth::id());
        })->count();

        return view('livewire.admin.gestion-comercial.actualizaciones-presto', ['presupuestos' => $presupuestos, 'registros' => $registros]);
    }
    
    public function mount(){
        $this->getEstados();
    }

    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }

    public function cambioEstado($id = null, $estado = null){
        $presupuesto = PresupuestoProyecto::find($id);
        $presupuesto->estado_id = $estado;

        // Aprobado
        $presupuesto->update();

        return redirect()->route('presupuestos-admin')->with('success', 'Cambios guardados exitosamente');
    } 
}
 