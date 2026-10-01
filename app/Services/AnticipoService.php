<?php

namespace App\Services;

use App\Models\Anticipo;
use App\Models\Aprobaciones_Anticipos;
use Illuminate\Support\Facades\DB;


class AnticipoService{

    //Calcular las etapas de aprobacion del anticipo

    public function obtenerEtapasAprobacion(float $montoAnticipado){

        $etapas = ['LIDER_PRODUCCION', 'CONTROLLER'];

        if($montoAnticipado >= 2000000 && $montoAnticipado < 10000000)
        {
            $etapas[] = 'LIDER_COMERCIAL';
        }else if($montoAnticipado >= 10000000)
        {
            $etapas[] = 'LIDER_COMERCIAL';
            $etapas[] = 'GERENCIA';
        }
        return $etapas;
    }

    public function procesarDecision(Anticipo $anticipo, int $usuarioId, string $decision, ?string $observacion = null)
    {
        return DB::transaction(function () use ($anticipo, $usuarioId, $decision, $observacion) {
            // Buscar la aprobación pendiente con menor orden
            $aprobacionActual = Aprobaciones_Anticipos::where('anticipo_id', $anticipo->id)
                ->where('estado', 'PENDIENTE')
                ->orderBy('orden', 'asc')
                ->first();

            if (!$aprobacionActual) {
                return false;
            }

            // Actualizar la decisión en la tabla aprobaciones_anticipos
            $aprobacionActual->update([
                'estado' => $decision,
                'usuario_decicion_id' => $usuarioId,
                'fecha_decision' => now(),
                'observacion' => $observacion,
            ]);

            // Si es RECHAZADO, regresa al Productor
            if ($decision === 'RECHAZADO') {
                $anticipo->update([
                    'estado_id' => 6, // ID para "DEVUELTO / RECHAZADO"
                    'justificacion_rechazo' => $observacion,
                ]);
                return 'RECHAZADO';
            }

            // Si es APROBADO, verificar si hay más etapas pendientes
            $siguienteEtapa = Aprobaciones_Anticipos::where('anticipo_id', $anticipo->id)
                ->where('estado', 'PENDIENTE')
                ->orderBy('orden', 'asc')
                ->first();

            if ($siguienteEtapa) {
                // Actualizar estado_id al siguiente rol dependiente
                $mapaEstados = [
                    'CONTROLLER' => 3,       // Pendiente Controller
                    'LIDER_COMERCIAL' => 4,  // Pendiente Comercial
                    'GERENCIA' => 5,         // Pendiente Gerencia
                ];

                if (isset($mapaEstados[$siguienteEtapa->etapa])) {
                    $anticipo->update(['estado_id' => $mapaEstados[$siguienteEtapa->etapa]]);
                }

                return 'SIGUIENTE_ETAPA';
            } else {
                // Todas las etapas fueron aprobadas
                $anticipo->update([
                    'estado_id' => 6, // ID para "APROBADO"
                    'fecha_aprobacion' => now(),
                ]);

                return 'APROBADO_FINAL';
            }
        });
    }
}