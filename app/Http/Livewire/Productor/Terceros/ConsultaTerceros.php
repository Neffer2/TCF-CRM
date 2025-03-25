<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\OrdenCompra;
use Illuminate\Database\Eloquent\Builder;

class ConsultaTerceros extends Component
{
    // Models
    public $numOrden;

    public function render()
    {
        $filtro = [];
        $orden = null;

        if ($this->numOrden) {
            array_push($filtro, ['id', $this->numOrden]);
            array_push($filtro, ['tipo_oc', 2]);
            array_push($filtro, ['estado_id', 3]);

            $orden = OrdenCompra::with('naturalInfo')->where($filtro)->whereHas('naturalInfo', function (Builder $naturalInfo){
                $naturalInfo->where('terminos', NULL);
            })->first();
        }

        return view('livewire.productor.terceros.consulta-terceros', ['orden' => $orden]);
    }

    public function mount()
    {
        (isset($_GET['orden'])) ? $this->numOrden = $_GET['orden'] : $this->numOrden = null;
    }
}
