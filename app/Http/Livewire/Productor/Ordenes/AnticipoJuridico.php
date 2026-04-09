<?php

namespace App\Http\Livewire\Productor\Ordenes;

use App\Models\Anticipo as ModelAnticipo;
use App\Models\Año;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AnticipoJuridico extends Component
{
    // Models Juridico
    public $orden_compra, $porcentaje_anticipo, $total_anticipo;

    // Useful vars Juridico
    public $orden, $ordenes = [], $queriedAnticipo;

    // Filled
    public $anticipo_id;

    public function render()
    {
        return view('livewire.productor.ordenes.anticipo-juridico');
    }

    public function mount() {
        if ($this->anticipo_id) {
            $this->queriedAnticipo = ModelAnticipo::find($this->anticipo_id);
            $this->setData();
        }
        else {
            $this->getData();
        }
    }

    public function getData() {
        // Ordenes de compra del productor autenticado que no tengan anticipos
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
                ['tipo_oc', 1],
                ['created_at', '>=', Año::orderBy('description', 'desc')->first()->description.'-01-01']
            ])
            ->whereDoesntHave('anticipos')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->ordenes = $ordenes;
    }

    public function setData(){
        if($this->queriedAnticipo){
            $this->orden_compra = $this->queriedAnticipo->oc_id;
            $this->orden = OrdenCompra::find($this->orden_compra);
            $this->porcentaje_anticipo = $this->queriedAnticipo->porcentaje_anticipo;
            $this->total_anticipo = $this->queriedAnticipo->total_anticipo;
        }
    }

    public function nuevoAnticipoJuridico() {
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
        return redirect()->route('anticipo-prod')->with('success', 'Anticipo creado');
    }

    public function actualizarAnticipoJuridico() {
        $this->validate([
            'orden_compra' => 'required|unique:anticipos,oc_id,'.$this->anticipo_id,
            'orden' => 'required',
            'porcentaje_anticipo' => 'required|numeric|min:0|max:100',
            'total_anticipo' => 'required|numeric|min:0',
        ]);

        if($this->queriedAnticipo){
            $this->queriedAnticipo->update([
                'oc_id' => $this->orden_compra,
                'porcentaje_anticipo' => $this->porcentaje_anticipo,
                'estado_id' => 1,
                'fecha_aprobacion' => now(),
                'total_anticipo' => $this->total_anticipo,
            ]);

            return redirect()->route('lista-anticipos-admin')->with('success', 'Anticipo aprobado');
        }
    }

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
