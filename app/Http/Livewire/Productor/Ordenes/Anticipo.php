<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\OrdenCompra;
use App\Models\Anticipo as ModelAnticipo;
use App\Models\PresupuestoProyecto as CentrosCosto;
use App\Models\Año;
use Illuminate\Support\Facades\Auth;

class Anticipo extends Component
{
    // Models Juridico
    public $orden_compra, $porcentaje_anticipo, $total_anticipo;
    // Models Productor
    public $centro_costo, $item, $valor = 0, $saldo = 0, $selectedItem;

    // Useful vars Juridico
    public $orden, $ordenes = [], $queriedAnticipo;
    // Useful vars Productor
    public $centros_costo = [], $items = [];

    // Filled
    public $anticipo_id;

    public function render()
    {
        return view('livewire.productor.ordenes.anticipo');
    } 

    public function mount(){
        if ($this->anticipo_id) {
            $this->queriedAnticipo = ModelAnticipo::find($this->anticipo_id);
            $this->setData();
        }else {
            $this->getData();
        }
    }

    public function getData(){
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

        // Centros de costo del productor autenticado
        $centros = CentrosCosto::where([
            ['productor', Auth::user()->id],
            ['estado_id', 1], 
            ['fecha_cc', '>=', Año::orderBy('description', 'desc')->first()->description.'-01-01']
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $this->centros_costo = $centros;
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
        return redirect()->route('anticipo-prod')->with('success', 'Anticipo creado'); 
    }

    public function setData(){
        if($this->queriedAnticipo){
            $this->orden_compra = $this->queriedAnticipo->oc_id;
            $this->orden = OrdenCompra::find($this->orden_compra);
            $this->porcentaje_anticipo = $this->queriedAnticipo->porcentaje_anticipo;
            $this->total_anticipo = $this->queriedAnticipo->total_anticipo;
        }
    } 

    public function ActualizarAnticipo(){
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

            return redirect()->route('anticipos-admin')->with('success', 'Anticipo aprobado');
        }
    }

    function getSaldo(){
        $this->saldo = $this->selectedItem->v_total - /* $item->consumidos->sum('vtotal_oc') */ $this->valor;
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

    public function updatedItem(){
        $this->validate([
            'centro_costo' => 'required',
            'item' => 'required',
        ]);

        // Obtiene la información del item
        $this->selectedItem = $this->centros_costo->find($this->centro_costo)->presupuestoItems->find($this->item); 
        $this->getSaldo();
    }

    public function updatedValor(){
        $this->valor = trim($this->valor);
        $this->valor = str_replace(",",'', $this->valor); 

        $this->validate([
            'centro_costo' => 'required', 
            'item' => 'required',
            'valor' => 'required|numeric|min:0',
        ]);

        // TODO: CONSUMIDO ANTICIPO
        if ($this->valor > $this->selectedItem->v_total) {
            return $this->addError('valor', 'El valor no puede ser mayor al valor total del ítem');
        }

        $this->getSaldo();
    }
}
    