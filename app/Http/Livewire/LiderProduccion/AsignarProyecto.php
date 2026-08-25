<?php

namespace App\Http\Livewire\LiderProduccion;

use Livewire\Component;
use App\Models\User;
use App\Models\Año;
use App\Models\PresupuestoProyecto;

class AsignarProyecto extends Component
{
    // Modelos y variables principales
    public $proyecto, $productor, $asignado, $comercial;

    // Variables útiles para la vista y lógica
    public $proyectos, $comerciales, $asignados = [];

    // ID del productor seleccionado
    public $id_productor;

    // Renderiza la vista y actualiza los datos de usuarios y proyectos
    public function render()
    { 
        $this->getUsers();
        $this->getProyectos();
        return view('livewire.lider-produccion.asignar-proyecto');
    }

    // Al montar el componente, carga los proyectos asignados al productor
    public function mount(){
        $this->getAsigandos();
    }

    // Obtiene los datos del productor y la lista de comerciales
    public function getUsers(){
        $this->productor = User::select('name', 'avatar')->find($this->id_productor);
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    // Obtiene los proyectos disponibles para asignar según filtros
    public function getProyectos(){
        $año = Año::orderBy('description', 'DESC')->first();
        $this->proyectos = [];
        $filter = [];
        $gestionFilter = [];
        $baseFilter = [];

        // Si hay comercial seleccionado, filtra por ese usuario
        if ($this->comercial){
            array_push($gestionFilter, ['id_user', $this->comercial]);
        }

        // Solo proyectos activos y sin productor asignado
        array_push($filter, ['estado_id', 1]);
        array_push($filter, ['productor', null]);

        // Filtros adicionales por estado y fechas del año actual
        array_push($baseFilter, ['id_estado', '<>', 4]);
        array_push($baseFilter, ['id_estado', '<>', 1]);
        array_push($baseFilter, ['fecha', '>=', $año->meses->first()->f_inicio]);
        array_push($baseFilter, ['fecha', '<=', $año->meses->last()->f_fin]);

        // Consulta los proyectos con los filtros aplicados
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

    // Obtiene los proyectos actualmente asignados al productor
    public function getAsigandos(){
        $this->asignados = [];
        $this->asignados = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')->where('productor', $this->id_productor)->get();
    }

    // Asigna el proyecto seleccionado al productor
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

    // Libera el proyecto seleccionado, quitando el productor asignado
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

    // Validaciones automáticas al actualizar los campos del formulario
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

    // Reinicia la vista y recarga los proyectos y asignaciones
    public function ResetView(){
        $this->proyecto = NULL;
        $this->asignado = NULL;

        $this->getProyectos();
        $this->getAsigandos();
    }
}
