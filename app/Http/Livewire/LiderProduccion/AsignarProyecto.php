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
    public function getProyectos()
    {
        $this->proyectos = [];

        if (!$this->comercial) {
            return;
        }

        $anio = Año::orderByDesc('description')->first();

        $periodo = $anio?->meses()
            ->selectRaw('MIN(f_inicio) AS fecha_inicio, MAX(f_fin) AS fecha_fin')
            ->first();

        if (!$periodo?->fecha_inicio || !$periodo?->fecha_fin) {
            return;
        }

        $finExclusivo = \Illuminate\Support\Carbon::parse($periodo->fecha_fin)
            ->startOfDay()
            ->addDay();

        $this->proyectos = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')
            ->with('gestion', 'baseComercial')
            ->where('estado_id', 1)
            ->whereNull('productor')
            ->whereHas('gestion', function ($gestion) {
                $gestion->where('id_user', $this->comercial);
            })
            ->whereHas('baseComercial', function ($base) use ($periodo, $finExclusivo) {
                $base->whereNotIn('id_estado', [1, 4])
                    ->where('fecha', '>=', $periodo->fecha_inicio)
                    ->where('fecha', '<', $finExclusivo);
            })
            ->orderByDesc('created_at')
            ->get();
    }

    // Obtiene los proyectos actualmente asignados al productor
    public function getAsigandos()
    {
        $inicioAnio = now()->startOfYear();
        $inicioSiguienteAnio = $inicioAnio->copy()->addYear();

        $this->asignados = PresupuestoProyecto::select('id', 'id_gestion', 'cod_cc')
            ->where('productor', $this->id_productor)
            ->where('fecha_cc', '>=', $inicioAnio)
            ->where('fecha_cc', '<', $inicioSiguienteAnio)
            ->where('id_gestion', '<>', 1)
            ->get();
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
