<?php

namespace App\Http\Livewire\Com\GestionComercial;
 
use Livewire\Component;
use App\Models\GestionComercial;
use App\Models\EstadoGestionComercial;
use App\Models\PresupuestoProyecto; 
use App\Models\Contacto;
use App\Models\Asistente;
use App\Models\Año;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth; 

class GestionList extends Component 
{ 
    use WithPagination; 
    protected $paginationTheme = 'bootstrap'; 
    // Models
    public $nomProyecto, $contacto, $estado, $año, $order = 'desc';
    
    // Useful vars 
    public $estados = [], $contactos = [], $años = [], $yearInfo;  
    protected $listeners = ['list' => 'getData'];
 
    public function render()   
    {    
        $this->getData();
        $filtros = [];  
        
        if ($this->nomProyecto){ 
            array_push($filtros, ['nom_proyecto_cot', 'like', "%$this->nomProyecto%"]);   
        }

        if ($this->contacto){
            array_push($filtros, ['id_contacto', $this->contacto]);   
        }

        if($this->año){
            array_push($filtros, ['created_at', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['created_at', '<=', $this->yearInfo->meses->last()->f_fin]);
        }
        
        if ($this->estado){
            array_push($filtros, ['id_estado', $this->estado]);   
        }

        if (Auth::user()->rol == 2){ 
            $datos = GestionComercial::select('id', 'id_contacto', 'id_estado', 'nom_proyecto_cot', 'presto_cot')->where(function ($query) {
                $query->where('id_user', Auth::user()->id)
                    ->orWhere('comercial_2', Auth::user()->id)
                    ->orWhere('comercial_3', Auth::user()->id)
                    ->orWhere('comercial_4', Auth::user()->id);
            })->where($filtros)->orderBy('created_at', $this->order)->paginate(15);

        }else if(Auth::user()->rol == 5){
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $datos = GestionComercial::select('id','id_contacto','id_estado', 'nom_proyecto_cot', 'presto_cot')->where(function ($query) use ($asistente) {
                $query->where('id_user', $asistente->comercial_id)
                    ->orWhere('comercial_2', $asistente->comercial_id)
                    ->orWhere('comercial_3', $asistente->comercial_id)
                    ->orWhere('comercial_4', $asistente->comercial_id); 
            })->where($filtros)->orderBy('created_at', $this->order)->paginate(15);                
        }

        return view('livewire.com.gestion-comercial.gestion-list', ['datos' => $datos]);
    }

    public function getData(){ 
        $this->estados = EstadoGestionComercial::select('id', 'description')->get();

        if(Auth::user()->rol == 2){
            $this->contactos = Contacto::where('id_user', Auth::id())->get();
        }else if(Auth::user()->rol == 5){
            $asistente = Asistente::where('asistente_id', Auth::user()->id)->first();
            $this->contactos = Contacto::where('id_user', $asistente->comercial_id)->get();
        }
    }

    public function mount (){
        $this->getAños();
    }

    public function getAños(){
        $this->años = Año::all();
        /* CURRENT YEAR */
        $this->año = $this->años->sortByDesc('description')->first()->id;
        $this->updatedAño();
    }

    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);
        
        $this->yearInfo = Año::find($this->año);
    }
} 
  