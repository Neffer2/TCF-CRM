<?php
 
namespace App\Http\Livewire\Admin\GestionComercial;

use Livewire\Component;
use App\Models\PresupuestoProyecto;

class GestionPresupuestos extends Component
{
    public function render()
    {   
        $presupuestos = PresupuestoProyecto::where('margen_proy', '<', 35)->where('estado_id', 2)->paginate(10);
        return view('livewire.admin.gestion-comercial.gestion-presupuestos', ['presupuestos' => $presupuestos]);
    }
} 
