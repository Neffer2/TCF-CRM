<?php

namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto; 
use App\Models\GestionComercial; 
use App\Models\Asistente;
use App\Models\User;  
use App\Models\ItemPresupuesto;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; 
use App\Traits\Hablame;

class ActualizacionesPresto extends Component
{   
    use WithPagination, Hablame;
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
        if ($this->rol == 1){ array_push($filtros, ['estado_id', 2]); }

        // Trae solo los presupuestos del usuario
        if ($this->rol == 2){
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->
                whereHas('gestion', function (Builder $query){
                    $query->where('id_user', Auth::id());
                })->paginate(10);
        }elseif ($this->rol == 5){
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->
                whereHas('gestion', function (Builder $query){
                    $query->where('id_user', Asistente::select('comercial_id')->where('asistente_id', Auth::id())->first()->comercial_id);
                })->paginate(10);
        }

        // Trae todos los presupustos que tengan actualizaciones
        if ($this->rol == 1){ 
            $presupuestos = PresupuestoProyecto::
                        where($filtros)->orderBy('id', $this->fecha)->paginate(10);
         }
        
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
        $presupuesto->update();

        if ($presupuesto->estado_id == 1){
            $this->presupuestoAprobado($presupuesto->gestion->comercial, $presupuesto->gestion, $presupuesto->cod_cc);

            // Default indicacion actualiazcion
            ItemPresupuesto::where('presupuesto_id', $id)->get()->map(function ($item){
                $item->actualizado = false;
                $item->update();
            });
        }elseif ($presupuesto->estado_id == 3){
            $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, $presupuesto->cod_cc);            
        }
        
        return redirect()->route('actualizaciones')->with('success', 'Cambios guardados exitosamente');
    } 
}
 