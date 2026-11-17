<?php

namespace App\Http\Livewire\LiderProduccion\Anticipos;

use Livewire\Component;
use App\Models\Anticipo;

class ActualizacionesAnticipo extends Component
{
    public $search = '';
    public $ordenes = [];

    // Propiedades del Modal y Edición
    public $selectedAnticipo = null;
    public $showModal = false;

    // Campos editables del anticipo
    public $porcentaje_anticipo;
    public $total_anticipo;
    public $justificacion_rechazo = '';

    protected $rules = [
        'justificacion_rechazo' => 'required|string|min:5|max:500',
    ];

    protected $messages = [
        'justificacion_rechazo.required' => 'La razón del nuevo rechazo (Anulación) es obligatoria.',
        'justificacion_rechazo.min' => 'Debe ingresar al menos 5 caracteres.',
    ];

    public function verDetalle($id)
    {
        $this->resetValidation();

        $this->selectedAnticipo = Anticipo::with([
            'ordenCompra.tipo',
            'ordenCompra.presupuesto',
            'ordenCompra.ordenItems',
            'estado'
        ])->find($id);

        if ($this->selectedAnticipo) {
            $this->porcentaje_anticipo = $this->selectedAnticipo->porcentaje_anticipo;
            $this->total_anticipo = $this->selectedAnticipo->total_anticipo;
            $this->justificacion_rechazo = '';
            $this->showModal = true;
        }
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->selectedAnticipo = null;
        $this->resetValidation();
    }

    // Método para guardar cambios y solicitar nueva revisión
    public function reEnviarRevision()
    {
        $this->validate([
            'porcentaje_anticipo' => 'required|numeric|min:1|max:100',
            'total_anticipo'      => 'required|numeric|min:0',
        ]);

        if ($this->selectedAnticipo) {
            $this->selectedAnticipo->update([
                'porcentaje_anticipo'   => $this->porcentaje_anticipo,
                'total_anticipo'        => $this->total_anticipo,
                'estado_id'             => 1, // Vuelve a estado inicial o pendiente de revisión
                'justificacion_rechazo' => null // Se limpia para el siguiente ciclo
            ]);

            session()->flash('message', 'Anticipo #' . $this->selectedAnticipo->id . ' actualizado y reenviado a revisión.');
            $this->cerrarModal();
        }
    }

    // Método para Rechazar/Anular definitivamente (Pasa a estado_id = 7)
    public function rechazarAnular()
    {
        $this->validate(); // Valida justificacion_rechazo

        if ($this->selectedAnticipo) {
            $this->selectedAnticipo->update([
                'estado_id'             => 7, // Estado Anulado
                'justificacion_rechazo' => $this->justificacion_rechazo,
            ]);

            session()->flash('message', 'El anticipo #' . $this->selectedAnticipo->id . ' fue Anulado (Estado 7).');
            $this->cerrarModal();
        }
    }

    public function listaAnticiposRechazados()
    {
        $this->ordenes = Anticipo::with([
            'ordenCompra.tipo',
            'ordenCompra.presupuesto',
            'ordenCompra.ordenItems',
            'estado'
        ])
        // Filtro backend: estado_id = 2 y justificacion_rechazo
        ->where('estado_id', 2)
        ->where(function ($q) {
            $q->whereNotNull('justificacion_rechazo');
        })
        ->when($this->search, function ($query) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', $s)
                  ->orWhereHas('ordenCompra', fn ($q) => $q->where('cod_oc', 'like', $s))
                  ->orWhereHas('ordenCompra.presupuesto', fn ($p) => $p->where('cod_cc', 'like', $s));
            });
        })
        ->orderByDesc('created_at')
        ->take(50)
        ->get();
    }

    public function render()
    {
        $this->listaAnticiposRechazados();
        return view('livewire.lider-produccion.anticipos.actualizaciones-anticipos');
    }
}
