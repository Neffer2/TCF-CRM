<?php

namespace App\Http\Livewire\Productor\Anticipo;

use App\Models\Anticipo;
use Livewire\Component;

class ListaAnticipos extends Component
{
    public $search = '';
    public $ordenes = [];
    public $anticipo_id;

    public function render()
    {
        $this->listaAnticipos();

        return view('livewire.productor.anticipos.lista-anticipos');
    }

    public function mount($anticipo_id = null)
    {
        $this->anticipo_id = $anticipo_id;
    }
    
    public function listaAnticipos()
    {
        $this->ordenes = Anticipo::with([
            'ordenCompra.tipo', // Carga anidada del tipo de orden de compra
            'ordenCompra.presupuesto', // Carga anidada del presupuesto
            'estado'
        ])
        ->whereHas('ordenCompra', function ($q) {
            $q->where(function ($sub) {
                // Caso 1: Personas Jurídicas
                $sub->where('tipo_oc', 1)
                    ->where('productor_id', auth()->id());
            })
            ->orWhere(function ($sub) {
                // Caso 2: Personas Naturales
                $sub->where('tipo_oc', 2)
                    ->whereHas('naturalInfo', fn ($n) => $n->where('productor_id', auth()->id()));
            });
        })
        ->when($this->search, function ($query) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', $s)
                  ->orWhereHas('ordenCompra', fn ($q) => $q->where('cod_oc', 'like', $s))
                  // Opcional: Buscar también por Centro de Costos
                  ->orWhereHas('ordenCompra.presupuesto', fn ($p) => $p->where('cod_cc', 'like', $s));
            });
        })
        ->orderByDesc('created_at')
        ->take(50)
        ->get();
    }
}