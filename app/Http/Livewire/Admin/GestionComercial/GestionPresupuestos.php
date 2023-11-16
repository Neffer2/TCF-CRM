<?php
 
namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto; 
use App\Models\GestionComercial;
use App\Models\User; 
use App\Traits\Email;
use Livewire\WithPagination; 

class GestionPresupuestos extends Component 
{
    use WithPagination, Email;
    protected $paginationTheme = 'bootstrap';

    //Models 
    public $filter = 0;
    // public $comercial = 0;
    public $fecha = 'asc';
    public $margen = 'asc';

    // Useful vars
    public $estados = []; 
    public $margenOperator;
    public $estadoProyecto;
    // public $comerciales = [];  

    public $rol;
 
    public function render() 
    {   
        $filtros = [];
 
        if ($this->filter != 0){
            array_push($filtros, ['estado_id', $this->filter]);
        } 
        
        if ($this->margen == '<'){
            array_push($filtros, ['margen_proy', '<=', 35]);
        }elseif ($this->margen == '>'){
            array_push($filtros, ['margen_proy', '>=', 35]);
        }
        array_push($filtros, ['cod_cc', null]);
        
        $presupuestos = PresupuestoProyecto::where('estado_id', $this->estadoProyecto)->where($filtros)->orderBy('id', $this->fecha)->paginate(10);

        return view('livewire.admin.gestion-comercial.gestion-presupuestos', ['presupuestos' => $presupuestos]);
    }
 
    public function mount(){
        $this->getEstados();
        if ($this->rol == 1){
            $this->estadoProyecto = 2;
        }
        // $this->getComerciales(); 
    }
 
    public function getEstados(){
        $this->estados = EstadosPresupuesto::select('id', 'description')->where('id', '<>', 3)->get();
    }

    public function cambioEstado($id = null, $estado = null){
        $presupuesto = PresupuestoProyecto::find($id);
        $presupuesto->estado_id = $estado;

        // Aprobado
        if ($estado == 1){ 
            $gestion = GestionComercial::find($presupuesto->id_gestion);
            $gestion->id_estado = 4; 
            $gestion->update();
            $presupuesto->justificacion_compras = null;
            $presupuesto->justificacion = null;
        }
        $presupuesto->update();

        if ($presupuesto->estado_id == 1){
            $this->presupuestoAprobado($presupuesto->gestion->comercial, $presupuesto->gestion, null, $presupuesto->cod_cc);
        }elseif ($presupuesto->estado_id == 3){
            $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, null);
        }

        return redirect()->route('presupuesto-proyecto')->with('success', 'Cambios guardados exitosamente');
    } 
} 
