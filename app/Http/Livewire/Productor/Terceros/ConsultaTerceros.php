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

        // La orden autorizada viene de la sesión (fijada en mount con el
        // enlace firmado), nunca del valor sincronizado por el navegador.
        $ordenAutorizada = session('portal_terceros_orden');
        if ($ordenAutorizada) {
            // Agrega filtros por id de la orden y tipo de orden (2 = natural)
            array_push($filtro, ['id', $ordenAutorizada]);
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
        // La firma del enlace ya fue validada por el middleware 'signed'.
        // La orden autorizada se fija en sesión: numOrden es una propiedad
        // pública de Livewire (modificable desde el navegador aunque el
        // input esté disabled) y no puede ser la fuente de autorización.
        $ordenFirmada = request()->route('orden') ?? request()->query('orden');
        if ($ordenFirmada) {
            session(['portal_terceros_orden' => $ordenFirmada]);
        }
        $this->numOrden = session('portal_terceros_orden');
    }
}
