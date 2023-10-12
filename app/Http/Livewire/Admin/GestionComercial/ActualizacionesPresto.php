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
    public $fecha = 'desc';
    public $cod_cc;

    // Useful vars
    public $estados = [];  
    // public $comerciales = [];   

    public $rol; 
  
    public function render()      
    {   
        $filtros = [];

        array_push($filtros, ['cod_cc', '<>', null]);

        if ($this->cod_cc){
            array_push($filtros, ['cod_cc', 'like', "%$this->cod_cc%"]);   
        }

        if ($this->rol == 1){ array_push($filtros, ['estado_id', 2]); }

        // Trae solo los presupuestos del usuario
        if ($this->rol == 2){
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->
                whereHas('gestion', function (Builder $query){
                    $query->where('id_user', Auth::id());
                })->paginate(15);
        }elseif ($this->rol == 5){
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->
                whereHas('gestion', function (Builder $query){
                    $query->where('id_user', Asistente::select('comercial_id')->where('asistente_id', Auth::id())->first()->comercial_id);
                })->paginate(15);
        }

        // Trae todos los presupustos que tengan actualizaciones
        if ($this->rol == 1){ 
            $presupuestos = PresupuestoProyecto::
                where($filtros)->orderBy('id', $this->fecha)->paginate(15);
        }

        return view('livewire.admin.gestion-comercial.actualizaciones-presto', ['presupuestos' => $presupuestos]);
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

        if ($presupuesto->estado_id == 1){
            $presupuesto->justificacion_compras = null;
            $presupuesto->justificacion = null;

            // Re-calcula los valores de la base y gestion comercial
            $this->reCalculate($presupuesto);
            
            $this->presupuestoAprobado($presupuesto->gestion->comercial, $presupuesto->gestion, $presupuesto->cod_cc); 

            // Default indicacion actualiazcion
            ItemPresupuesto::where('presupuesto_id', $id)->get()->map(function ($item){
                $item->actualizado = false;
                $item->update();
            });
        }elseif ($presupuesto->estado_id == 3){
            $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, $presupuesto->cod_cc);            
        }
        
        $presupuesto->update();
        return redirect()->route('actualizaciones')->with('success', 'Cambios guardados exitosamente');
    } 

    public function reCalculate($presupuesto){
        $prestosCom = [];        
        
        // Update Gestion
        $presupuesto->gestion->presto_cot = $presupuesto->venta_proy;
        $presupuesto->gestion->update();

        // Toma el presupuesto y id del usuario creador de la gestión
        array_push($prestosCom, [
            'comercial_id' => $presupuesto->gestion->id_user,
            'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->porcentaje)/100
        ]);

        // Toma el presupuesto y id de los usuarios partiipantes en la gestion
        $i = 2;
        while($i < 5){
            array_push($prestosCom, [   
                'comercial_id' => $presupuesto->gestion->{'comercial_'.$i},
                'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->{'porcentaje_'.$i})/100, 
            ]);
            $i++;
        }

        // Update Base
        foreach ($presupuesto->gestion->baseComercial as $key => $base){
            if ($base->id_user == $prestosCom[$key]['comercial_id']){
                $base->valor_original = $presupuesto->venta_proy;
                $base->valor_proyecto = $prestosCom[$key]['presupuesto'];
                $base->update();
            }
        }
    }
}