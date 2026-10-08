<?php

namespace App\Http\Livewire\Admin\Produccion\Anticipos;

use Livewire\Component;
use App\Models\Anticipo;
use Exception;
use Illuminate\Support\Facades\Auth;

class Anticipos extends Component
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
        if (!$this->selectedAnticipo) {
            return;
        }

        $valor_anticipo = $this->selectedAnticipo->total_anticipo;
        $val_controller = 10000000;

        if ($valor_anticipo <= $val_controller) {
            $this->selectedAnticipo->update([
                'estado_id' => 8
            ]);
            session()->flash('message', 'Anticipo #' . $this->selectedAnticipo->id . ' aprobado correctamente.');
            $this->cerrarModal();
        } else {
            $this->selectedAnticipo->update([
                'estado_id' => 5
            ]);
            session()->flash('message', 'Anticipo #' . $this->selectedAnticipo->id . ' aprobado y enviado a gerencia.');
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
        $user = Auth::user();

        $idsController = [2, 208, 197, 214, 145, 181, 210, 206];
        $idsLideresComerciales = [9, 17, 192];
        $idsGerencia = [10, 8];

        $this->ordenes = Anticipo::with([
                'ordenCompra.tipo',
                'ordenCompra.presupuesto',
                'estado'
            ])
            ->whereHas('ordenCompra', function ($q) {
                $q->whereIn('tipo_oc', [1, 2]);
            })
            // Filtro condicional según el grupo de ID del usuario autenticado
            ->where(function ($query) use ($user, $idsController, $idsLideresComerciales, $idsGerencia) {
                if ($user->rol == 1) {
                    if (in_array($user->id, $idsController)) {
                        // Controller solo ve estado_id = 3
                        $query->where('estado_id', 3);
                    } 
                    elseif (in_array($user->id, $idsLideresComerciales)) {
                        // Líder Comercial solo ve estado_id = 4
                        $query->where('estado_id', 4);
                    } 
                    elseif (in_array($user->id, $idsGerencia)) {
                        // Gerencia solo ve estado_id = 5
                        $query->where('estado_id', 5);
                    } 
                    else {
                        // Si es rol 1 pero no está en ninguna lista, no ve nada
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    $query->whereRaw('1 = 0');
                }
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

    public function render(){

        $this->listaAnticipos();
        return view('livewire.admin.produccion.anticipos.lista-anticipos');
    }
}