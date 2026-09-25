<?php

namespace App\Services;

use App\Models\ItemPresupuesto;
use App\Models\PresupuestoProyecto;
use Illuminate\Support\Facades\Auth;

/**
 * Reglas comunes a todas las pantallas que aprueban un presupuesto (la del presupuesto y las
 * listas de validaciones, actualizaciones y gestión de presupuestos).
 */
class AprobacionPresupuesto
{
    /**
     * Con margen del proyecto (%) hasta este valor, inclusive, la aprobación es de Gerencia (estado 5).
     * Regla de negocio del 11-sep-2026 ("solo Gerencia aprueba el margen 35 %"), la misma que usan los
     * correos y el filtro de gestión. El código tenía 30 % y dejó pasar 1.293 presupuestos entre 30 y 35.
     */
    const MARGEN_GERENCIA = 35.00;

    public static function requiereGerencia(PresupuestoProyecto $presupuesto): bool
    {
        return (float) $presupuesto->margen_proy <= self::MARGEN_GERENCIA;
    }

    /** Lleva el valor aprobado a la gestión y reparte el presupuesto de cada participante en la base comercial. */
    public static function propagarValores(PresupuestoProyecto $presupuesto): void
    {
        $gestion = $presupuesto->gestion;
        $gestion->presto_cot = $presupuesto->venta_proy;
        $gestion->update();

        // Un reparto por cada participante (posición 1 = responsable), emparejado por id_user:
        // el emparejamiento por posición fallaba en silencio si el orden de las filas no coincidía.
        $porComercial = $gestion->participantes
            ->filter(function ($p) { return !empty($p->user_id); })
            ->mapWithKeys(function ($p) use ($gestion) {
                return [(int) $p->user_id => ($gestion->presto_cot * $p->porcentaje) / 100];
            });

        foreach ($gestion->baseComercial as $base) {
            if ($porComercial->has((int) $base->id_user)) {
                $base->valor_original = $presupuesto->venta_proy;
                $base->valor_proyecto = $porComercial[(int) $base->id_user];
                $base->update();
            }
        }
    }

    /** Al aprobarse, la gestión pasa a Propuesta (4) sin devolver una que ya avanzó (Venta o Perdido). */
    public static function avanzarGestion(PresupuestoProyecto $presupuesto): void
    {
        $gestion = $presupuesto->gestion;
        if ($gestion && !in_array((int) $gestion->id_estado, [4, 5, 6], true)) {
            $gestion->id_estado = 4;
            $gestion->update();
        }
    }

    /**
     * Cambio de estado desde las listas (Validaciones, Actualizaciones, Gestión de presupuestos).
     * Cada lista revisa antes el rol que ve el selector; aquí van las reglas que valen para todas:
     * dentro del alcance del usuario, nunca sobre uno aprobado, aprobar exige centro de costos,
     * y con margen bajo o en validación de Gerencia solo aprueba Gerencia.
     * $notificador es el componente que llama (usa el trait Email). Devuelve el estado aplicado.
     */
    public static function cambiarEstadoDesdeLista(PresupuestoProyecto $presupuesto, $estado, $notificador): int
    {
        $user = Auth::user();
        abort_unless($user->puedeVerGestion($presupuesto->gestion), 403);
        $estado = (int) $estado;
        $actual = (int) $presupuesto->estado_id;
        abort_unless(in_array($estado, [1, 2, 3, 4, 5], true), 422, 'Estado no válido.');
        abort_if($actual === 1, 422, 'El presupuesto ya está aprobado.');

        $esGerencia = $user->can('aprobar-presupuestos') || $user->can('gerente-comercial');
        if ($estado === 1) {
            abort_unless($presupuesto->cod_cc, 422, 'Asigna el centro de costos desde el presupuesto antes de aprobarlo.');
            if ($actual === 5) {
                abort_unless($esGerencia, 403);
            } elseif (self::requiereGerencia($presupuesto) && !$esGerencia) {
                $estado = 5;
            }
        } elseif ($estado === 2 && self::requiereGerencia($presupuesto)) {
            $estado = 5;
        }

        $presupuesto->estado_id = $estado;
        if ($estado === 1) {
            $presupuesto->justificacion_compras = null;
            $presupuesto->justificacion_lider = null;
            $presupuesto->justificacion = null;
            if (!$presupuesto->fecha_cc) {
                $presupuesto->fecha_cc = date('Y-m-d');
            }
        }
        $presupuesto->update();

        if ($estado === 1) {
            ItemPresupuesto::where('presupuesto_id', $presupuesto->id)->update(['actualizado' => false]);
            self::propagarValores($presupuesto);
            self::avanzarGestion($presupuesto);
            $notificador->presupuestoAprobado($presupuesto->gestion->comercial, $presupuesto->gestion, null, $presupuesto->cod_cc);
        } elseif ($estado === 5) {
            $notificador->presupuestoAprobacion($presupuesto, $presupuesto->gestion->comercial);
        } elseif ($estado === 2) {
            $notificador->presupuestoAprobacion($presupuesto, $user);
        } elseif ($estado === 3) {
            $notificador->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, null);
        }

        return $estado;
    }
}
