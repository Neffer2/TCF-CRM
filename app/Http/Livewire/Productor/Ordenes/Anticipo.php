<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\Anticipo as ModelAnticipo;
use Illuminate\Support\Facades\Auth;

class Anticipo extends Component
{
    // Models 
    public $orden_compra, $porcentaje_anticipo, $total_anticipo;

    // Useful vars
    public $orden, $ordenes = [];

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
            ->whereDoesntHave('anticipos')
            ->orderBy('created_at', 'desc')
            ->get();

            $this->ordenes = $ordenes;
    }

    public function nuevoAnticipo(){
        $this->validate([
            'orden_compra' => 'required|unique:anticipos,oc_id',
            'orden' => 'required',
            'porcentaje_anticipo' => 'required|numeric|min:0|max:100',
            'total_anticipo' => 'required|numeric|min:0',
        ]);

        ModelAnticipo::create([
            'oc_id' => $this->orden_compra,
            'porcentaje_anticipo' => $this->porcentaje_anticipo,
            'total_anticipo' => $this->total_anticipo,
            'estado_id' => 2,
            'fecha_solicitud' => now(),
            'productor_id' => Auth::user()->id,
        ]); 

        $this->reset(['orden_compra', 'porcentaje_anticipo', 'total_anticipo', 'orden']);
        return redirect()->back()->with('success', 'Anticipo creado'); 
    }

    // Updates
    public function updatedOrdenCompra(){
        $this->orden = $this->ordenes->find($this->orden_compra);
    }

    public function updatedPorcentajeAnticipo(){
        if($this->orden && $this->porcentaje_anticipo){
            $this->total_anticipo = ($this->orden->ordenItems->sum('vtotal_oc') * $this->porcentaje_anticipo) / 100;
        } else {
            $this->total_anticipo = null;
        }   
    }
}
    