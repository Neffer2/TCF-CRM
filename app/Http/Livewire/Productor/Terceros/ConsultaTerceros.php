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

            // Trae ordenes con estado Editable o Evidencias
            $query = OrdenCompra::where($filtro)->whereIn('estado_id', [3, 7])->first();

            if ($query && (!$query->naturalInfo->terminos && $query->estado_id == 3)) {
                $orden = $query;
            }elseif ($query && ($query->naturalInfo->terminos && $query->estado_id == 7)) {
                $orden = $query;
            }else {
                $orden = null;
            }
        }

        return view('livewire.productor.terceros.consulta-terceros', ['orden' => $orden]);
    }

    public function mount()
    {
        (isset($_GET['orden'])) ? $this->numOrden = $_GET['orden'] : $this->numOrden = null;
    }
}
