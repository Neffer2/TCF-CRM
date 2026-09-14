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
        // Estado de la orden y del pago para mostrárselo al tercero en cualquier estado
        $seguimiento = null;
        if ($ordenAutorizada) {
            $o = OrdenCompra::with(['estado_oc', 'naturalInfo', 'anticipos'])->where('id', $ordenAutorizada)->where('tipo_oc', 2)->first();
            if ($o) {
                $pasos = [
                    ['Datos y contrato', (bool) optional($o->naturalInfo)->terminos],
                    ['Evidencias enviadas', $o->evidencias()->exists()],
                    ['Revisión de Bull', in_array($o->estado_id, [2, 1, 5, 4])],
                    ['Aprobada para pago', in_array($o->estado_id, [1, 5, 4])],
                ];
                $anticipo = $o->anticipos->sortByDesc('id')->first();
                if ($o->estado_id == 6) { $pago = 'La orden fue anulada.'; }
                elseif ($anticipo && $anticipo->estado_id == 14) { $pago = 'Anticipo pagado el '.optional($anticipo->fecha_comprobante_pago)->format('d/m/Y').'. El saldo se paga con la cuenta de cobro.'; }
                elseif (in_array($o->estado_id, [5, 4])) { $pago = 'Orden aprobada y en proceso de pago por contabilidad y tesorería.'; }
                elseif ($o->estado_id == 1) { $pago = 'Orden aprobada. Cuando se reciban tus evidencias y cuenta de cobro pasa a pago.'; }
                elseif ($o->estado_id == 2) { $pago = 'Tu orden está en revisión por el equipo de Bull.'; }
                elseif ($o->estado_id == 7) { $pago = 'Falta que adjuntes las evidencias del trabajo y tu cuenta de cobro.'; }
                else { $pago = 'Completa tus datos y acepta los términos para continuar.'; }
                $seguimiento = ['estado' => optional($o->estado_oc)->description ?: 'Sin estado', 'pasos' => $pasos, 'pago' => $pago, 'rechazo' => $o->estado_id == 7 ? $o->justificacion_rechazo : null];
            }
        }
        return view('livewire.productor.terceros.consulta-terceros', ['orden' => $orden, 'seguimiento' => $seguimiento]);
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
