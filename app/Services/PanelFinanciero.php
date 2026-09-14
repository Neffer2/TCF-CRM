<?php

namespace App\Services;

use App\Models\Anticipo;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\DB;

/**
 * Cifras y pendientes para los dashboards de Tesorería (paga) y Contabilidad (causa).
 * Todo sale de ordenes_compra (órdenes con causal / comprobante) y anticipos (estados_anticipo).
 */
class PanelFinanciero
{
    /** Total de una orden = suma de vtotal_oc de sus ítems. */
    private static function conTotal($query)
    {
        return $query->with(['presupuesto.gestion', 'proveedor', 'naturalInfo.tercero', 'tipo'])
            ->withSum('ordenItems as total', 'vtotal_oc');
    }

    public static function tesoreria(): array
    {
        $inicioMes = now()->startOfMonth();
        $inicioAnio = now()->startOfYear();

        $pendientes = OrdenCompra::whereNotNull('cod_causal')->whereNull('archivo_comprobante_pago');
        $valorPendiente = (clone $pendientes)->where('ordenes_compra.created_at', '>=', $inicioAnio)
            ->join('oc_items', 'oc_items.oc_id', '=', 'ordenes_compra.id')->sum('oc_items.vtotal_oc');

        $anticiposPend = Anticipo::where('estado_id', 5)->whereNull('comprobante_pago');

        return [
            'resumen' => [
                'oc_pendientes' => (clone $pendientes)->count(),
                'oc_pendientes_anio' => (clone $pendientes)->where('created_at', '>=', $inicioAnio)->count(),
                'oc_valor_pendiente_anio' => (float) $valorPendiente,
                'oc_pagadas_mes' => OrdenCompra::whereNotNull('archivo_comprobante_pago')->whereRaw('COALESCE(fecha_comprobante_pago, updated_at) >= ?', [$inicioMes])->count(),
                'ant_pendientes' => (clone $anticiposPend)->count(),
                'ant_valor_pendiente' => (float) (clone $anticiposPend)->sum('total_anticipo'),
                'ant_pagados_mes' => Anticipo::where('estado_id', 14)->where('fecha_comprobante_pago', '>=', $inicioMes)->count(),
            ],
            'ordenes' => self::conTotal((clone $pendientes))->orderByDesc('updated_at')->limit(8)->get(),
            'anticipos' => (clone $anticiposPend)->with(['ordenCompra.presupuesto.gestion', 'productor_info'])->orderByDesc('fecha_causal')->limit(6)->get(),
            'pagosMes' => self::pagosPorMes('fecha_comprobante_pago', 'archivo_comprobante_pago'),
        ];
    }

    public static function contabilidad(): array
    {
        $inicioMes = now()->startOfMonth();
        $inicioAnio = now()->startOfYear();

        // Aprobadas (estado 1) que todavía no tienen causal
        $porCausar = OrdenCompra::where('estado_id', 1)->whereNull('cod_causal');
        $valorPorCausar = (clone $porCausar)->where('ordenes_compra.created_at', '>=', $inicioAnio)
            ->join('oc_items', 'oc_items.oc_id', '=', 'ordenes_compra.id')->sum('oc_items.vtotal_oc');

        $anticiposPorCausar = Anticipo::where('estado_id', 1);

        return [
            'resumen' => [
                'oc_por_causar' => (clone $porCausar)->count(),
                'oc_por_causar_anio' => (clone $porCausar)->where('created_at', '>=', $inicioAnio)->count(),
                'oc_valor_por_causar_anio' => (float) $valorPorCausar,
                'oc_causadas_mes' => OrdenCompra::whereNotNull('cod_causal')->whereRaw('COALESCE(fecha_causal, updated_at) >= ?', [$inicioMes])->count(),
                'ant_por_causar' => (clone $anticiposPorCausar)->count(),
                'ant_valor_por_causar' => (float) (clone $anticiposPorCausar)->sum('total_anticipo'),
                'ant_causados_mes' => Anticipo::whereIn('estado_id', [5, 14])->where('fecha_causal', '>=', $inicioMes)->count(),
            ],
            'ordenes' => self::conTotal((clone $porCausar))->orderByDesc('fecha_aprobacion')->orderByDesc('id')->limit(8)->get(),
            'anticipos' => (clone $anticiposPorCausar)->with(['ordenCompra.presupuesto.gestion', 'productor_info'])->orderByDesc('fecha_aprobacion')->limit(6)->get(),
            'pagosMes' => self::pagosPorMes('fecha_causal', 'cod_causal'),
        ];
    }

    /** Últimos 6 meses: cuántas órdenes se procesaron por mes (para la barra de actividad). */
    private static function pagosPorMes(string $campoFecha, string $campoMarca): array
    {
        $desde = now()->subMonths(5)->startOfMonth();
        // Las órdenes antiguas no tienen fecha_causal / fecha_comprobante_pago: se usa la última actualización.
        $filas = OrdenCompra::whereNotNull($campoMarca)->whereRaw("COALESCE($campoFecha, updated_at) >= ?", [$desde])
            ->selectRaw("DATE_FORMAT(COALESCE($campoFecha, updated_at), '%Y-%m') ym, COUNT(*) n")->groupBy('ym')->pluck('n', 'ym');
        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i)->startOfMonth();
            $meses[] = ['label' => ucfirst($m->locale('es')->isoFormat('MMM')), 'n' => (int) ($filas[$m->format('Y-m')] ?? 0)];
        }
        return $meses;
    }

    /** Nombre de quien recibe el pago (proveedor o tercero natural). */
    public static function beneficiario(OrdenCompra $orden): string
    {
        $t = optional($orden->naturalInfo)->tercero;
        if ($t) { return trim($t->nombre.' '.$t->apellido); }
        return $orden->proveedor ? $orden->proveedor->tercero : '—';
    }
}
