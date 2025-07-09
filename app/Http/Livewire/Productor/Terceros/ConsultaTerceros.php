<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\OrdenCompra;
use Illuminate\Database\Eloquent\Builder;

class ConsultaTerceros extends Component
{
    // Variable para almacenar el número de orden ingresado por el usuario
    public $numOrden;

    // Renderiza la vista principal del componente
    public function render()
    {
        $filtro = []; // Filtros para la consulta
        $orden = null; // Variable para almacenar la orden encontrada

        // Si se ingresó un número de orden
        if ($this->numOrden) {
            // Agrega filtros por id de la orden y tipo de orden (2 = natural)
            array_push($filtro, ['id', $this->numOrden]);
            array_push($filtro, ['tipo_oc', 2]);

            // Busca la orden con estado Editable (3) o Evidencias (7)
            $query = OrdenCompra::where($filtro)->whereIn('estado_id', [3, 7])->first();

            // Si la orden está en estado Editable y no tiene términos aceptados
            if ($query && (!$query->naturalInfo->terminos && $query->estado_id == 3)) {
                $orden = $query;
            // Si la orden está en estado Evidencias y ya tiene términos aceptados
            }elseif ($query && ($query->naturalInfo->terminos && $query->estado_id == 7)) {
                $orden = $query;
            }else {
                $orden = null;
            }
        }

        // Retorna la vista con la orden encontrada (o null si no hay coincidencia)
        return view('livewire.productor.terceros.consulta-terceros', ['orden' => $orden]);
    }

    // Método que se ejecuta al montar el componente
    public function mount()
    {
        // Si existe el parámetro GET 'orden', lo asigna a la variable numOrden
        (isset($_GET['orden'])) ? $this->numOrden = $_GET['orden'] : $this->numOrden = null;
    }
}
