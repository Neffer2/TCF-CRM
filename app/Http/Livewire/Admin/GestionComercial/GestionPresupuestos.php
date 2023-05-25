<?php
 
namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\EstadosPresupuesto;

class GestionPresupuestos extends Component
{
    //Models
    public $filter = 0;

    // Useful vars
    public $estados = []; 

    public function render()
    {   
        if ($this->filter == 0){
            $presupuestos = PresupuestoProyecto::where('estado_id', '<>', 3)->where('margen_proy', '<', 35)->paginate(10);
        }else {
            $presupuestos = PresupuestoProyecto::where('estado_id', '<>', 3)->where('margen_proy', '<', 35)->where('estado_id', $this->filter)->paginate(10);
        }

        return view('livewire.admin.gestion-comercial.gestion-presupuestos', ['presupuestos' => $presupuestos]);
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

        return redirect()->route('presupuesto-proyecto')->with('success', 'Cambios guardados exitosamente');
    }
} 
 