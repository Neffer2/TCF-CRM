<?php

namespace App\Http\Livewire\LiderProduccion\Anticipos;

use Livewire\Component;
use App\Models\Anticipo;

class ListaAnticipo extends Component
{
    public $search = '';
    public $ordenes = [];

    // Propiedades para la gestión del detalle
    public $selectedAnticipo = null;
    public $showModal = false;

    // Propiedad para la observación de rechazo
    public $justificacion_rechazo = '';

    // Reglas de validación
    protected $rules = [
        'justificacion_rechazo' => 'required|string|min:5|max:500',
    ];

    protected $messages = [
        'justificacion_rechazo.required' => 'La justificación es obligatoria para poder rechazar el anticipo.',
        'justificacion_rechazo.min' => 'La justificación debe tener al menos 5 caracteres.',
        'justificacion_rechazo.max' => 'La justificación no puede exceder 500 caracteres.',
    ];
    
    public function verDetalle($id)
    {
        $this->selectedAnticipo = Anticipo::with([
            'ordenCompra.tipo',
            'ordenCompra.presupuesto',
            'ordenCompra.OrdenItems',
            'estado'
        ])->find($id);

        if ($this->selectedAnticipo) {
            $this->showModal = true;
        }
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->selectedAnticipo = null;
    }

    public function aprobarAnticipo()
    {
        if ($this->selectedAnticipo) {
            $this->selectedAnticipo->update([
                'estado_id' => 3
            ]);

            session()->flash('message', 'Anticipo #' . $this->selectedAnticipo->id . ' aprobado correctamente.');
            $this->cerrarModal();
        }
    }

    public function rechazarAnticipo()
    {
        $this->validate();

        if ($this->selectedAnticipo) {
            $this->selectedAnticipo->update([
                'estado_id'             => 6,
                'justificacion_rechazo' => $this->justificacion_rechazo,
            ]);

            session()->flash('message', 'Anticipo #' . $this->selectedAnticipo->id . ' rechazado correctamente.');
            $this->cerrarModal();
        }
    }

    public function listaAnticipos()
    {
        $this->ordenes = Anticipo::with([
            'ordenCompra.tipo',
            'ordenCompra.presupuesto',
            'estado'
        ])
        ->whereHas('ordenCompra', function ($q) {
            $q->whereIn('tipo_oc', [1, 2]);
        })
        ->whereHas('estado', function ($q) {
            $q->whereIn('id', [2]);
        })
        ->when($this->search, function ($query) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', $s)
                  ->orWhereHas('ordenCompra', fn ($q) => $q->where('id', 'like', $s))
                  ->orWhereHas('ordenCompra.presupuesto', fn ($p) => $p->where('cod_cc', 'like', $s));
            });
        })
        ->orderByDesc('created_at')
        ->take(50)
        ->get();
    }

    public function render()
    {
        $this->listaAnticipos();
        return view('livewire.lider-produccion.anticipos.anticipos');
    }
}