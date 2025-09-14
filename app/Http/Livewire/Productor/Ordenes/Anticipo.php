<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\Auth;

class Anticipo extends Component
{
    // Models 
    public $orden_compra;

    // Useful vars
    public $ordenes = [];

    public function render()
    {
        return view('livewire.productor.ordenes.anticipo');
    }

    public function mount(){
        $this->getOrdenes();
    }

    public function getOrdenes(){
        $ordenes = OrdenCompra::where(function($query) {
                $query->whereHas('presupuesto', function ($presupuesto) {
                    $presupuesto->where('productor', Auth::user()->id);
                })
                // ->orWhereHas('naturalInfo', function ($natural) {
                //     $natural->where('productor_id', $this->productor);
                // })
            ;})
            ->where([
                ['estado_id', 1],
                ['tipo_oc', 1]
            ])
            ->orderBy('created_at', 'desc')
            ->get();

            $this->ordenes = $ordenes;
    }
}
    