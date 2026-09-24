<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Datos de la gestión comercial por KAM. Un solo lugar para clasificar las gestiones,
 * de modo que la página y el panel de cada comercial digan exactamente lo mismo.
 *
 * Cómo avanza una gestión en el CRM (verificado en el código de los formularios):
 *   1 Prospecto   → al crearla
 *   2 Oportunidad → OportunidadForm
 *   7 Presupuesto → CotizacionForm: el comercial está armando el presupuesto (NO es venta)
 *   4 Propuesta   → el presupuesto se aprobó / se registró la propuesta; falta que el cliente acepte
 *   5 Venta       → NewProyecto::storeVenta: se registra en la base comercial (única etapa ganada)
 *   6 Perdido     → DescicionForm
 *   3 Cotización  → no se usa (asignación comentada en el código)
 *
 * Valor: gestion_comercial.presto_cot. En las ventas de 2026 coincide con la venta del
 * presupuesto aprobado y con la base comercial con menos de 0,5 % de diferencia.
 * Facturación: la tabla helisa del CRM NO es la contabilidad completa (en 2026, 173 ventas
 * marcadas como facturadas o cerradas no aparecen en ella). Por eso el estado de cobro sale
 * del estado de la venta en la base comercial, que es el que la empresa ya usa en el dashboard,
 * y Helisa se muestra como verificación cruzada:
 *   facturada   → FACTURADO o CERRADO: se cuenta como facturada completa.
 *   parcial     → FACTURACION PARCIAL: lo facturado es lo que registra Helisa.
 *   por_facturar→ EJECUCIONXFACTURAR: ya se ejecutó y falta facturar. Es la plata a cobrar ya.
 *   ejecucion   → VENTAEJECUCION o VENTA: vendida, todavía no toca facturar.
 *   revisar     → la base la marca perdida/propuesta/interna o no tiene registro.
 * Sumar helisa por centro no duplica: en 2026 todas sus filas están al 100 %.
 */
class GestionKamDatos
{
    const VENTA = 5;
    const PERDIDA = 6;
    const EN_CURSO = [1, 2, 3, 4, 7];

    /** Etapas en el orden real del embudo, con la explicación que ve el usuario. */
    const ETAPAS = [
        1 => ['nombre' => 'Prospecto',         'explica' => 'Contacto identificado'],
        2 => ['nombre' => 'Oportunidad',       'explica' => 'Hay una necesidad concreta'],
        7 => ['nombre' => 'Cotizando',         'explica' => 'Armando el presupuesto'],
        4 => ['nombre' => 'Propuesta enviada', 'explica' => 'Presupuesto aprobado, esperando al cliente'],
        5 => ['nombre' => 'Venta',             'explica' => 'El cliente aceptó'],
        6 => ['nombre' => 'Perdida',           'explica' => 'No se dio'],
        3 => ['nombre' => 'Cotización',        'explica' => 'Etapa antigua, sin uso'],
    ];

    /** Estado de cobro de una venta, en el orden en que se lee y con su color. */
    const COBRO = [
        'facturada'    => ['Facturada o cerrada',     'is-venta'],
        'parcial'      => ['Facturada en parte',      'is-quieta'],
        'por_facturar' => ['Ejecutada, por facturar', 'is-quieta'],
        'ejecucion'    => ['En ejecución',            'is-curso'],
        'revisar'      => ['Revisar datos',           'is-perdida'],
    ];

    public static function cobro(?string $estadoBase): string
    {
        switch ($estadoBase) {
            case 'FACTURADO': case 'CERRADO':         return 'facturada';
            case 'FACTURACION PARCIAL':               return 'parcial';
            case 'EJECUCIONXFACTURAR':                return 'por_facturar';
            case 'VENTAEJECUCION': case 'VENTA':      return 'ejecucion';
            default:                                  return 'revisar';
        }
    }

    /** Estados de la base comercial dichos en lenguaje de gerencia. */
    const ESTADO_VENTA = [
        'VENTAEJECUCION'      => ['En ejecución', 'is-curso'],
        'EJECUCIONXFACTURAR'  => ['Ejecutada, por facturar', 'is-quieta'],
        'FACTURACION PARCIAL' => ['Facturada en parte', 'is-quieta'],
        'FACTURADO'           => ['Facturada', 'is-venta'],
        'CERRADO'             => ['Cerrada', 'is-venta'],
        'VENTA'               => ['Vendida, sin iniciar', 'is-curso'],
        'PERDIDO'             => ['La base dice perdida', 'is-perdida'],
        'INTERNO'             => ['Interna', 'is-curso'],
        'PROPUESTA'           => ['La base dice propuesta', 'is-curso'],
        'COTIZACION'          => ['La base dice cotización', 'is-curso'],
    ];

    public static function grupo(int $estado): string
    {
        if ($estado === self::VENTA) { return 'venta'; }
        if ($estado === self::PERDIDA) { return 'perdida'; }
        return 'curso';
    }

    /** Código limpio del centro de costos (Helisa trae a veces tabuladores o saltos de línea). */
    public static function sqlCentro(string $col): string
    {
        return "TRIM(REPLACE(REPLACE(REPLACE($col, CHAR(9), ''), CHAR(13), ''), CHAR(10), ''))";
    }

    /**
     * Una fila por gestión del año (con filtros), con todo lo que necesitan la página y el panel.
     * @param int[]|null $comerciales  ids a incluir (null = todos)
     */
    public static function gestiones(int $anio, ?array $comerciales = null, ?int $soloComercial = null): Collection
    {
        $q = DB::table('gestion_comercial as g')
            ->join('gestion_participantes as gp', function ($j) {
                $j->on('gp.gestion_id', '=', 'g.id')->where('gp.posicion', 1);
            })
            ->leftJoin('contactos as c', 'c.id', '=', 'g.id_contacto')
            ->leftJoin('presupuesto_proyecto as pp', 'pp.id_gestion', '=', 'g.id')
            // estado de la venta en la base comercial: el registro más reciente de la gestión
            ->leftJoin(DB::raw('(SELECT b.id_gestion, ec.description estado_base
                                  FROM base_comerciales b
                                  JOIN estados_cuenta ec ON ec.id = b.id_estado
                                  WHERE b.id IN (SELECT MAX(id) FROM base_comerciales GROUP BY id_gestion)) bc'),
                       'bc.id_gestion', '=', 'g.id')
            ->whereYear('g.created_at', $anio)
            ->selectRaw('g.id, gp.user_id, g.id_estado, g.presto_cot valor, g.nom_proyecto_cot proyecto, g.causa,
                         g.created_at abierta, g.updated_at movida,
                         DATEDIFF(NOW(), g.updated_at) dias_quieta,
                         c.empresa cliente, c.nombre contacto,
                         pp.id presupuesto_id, pp.cod_cc, bc.estado_base,
                         '.self::sqlCentro("SUBSTRING_INDEX(pp.cod_cc, ' -', 1)").' centro');

        if ($soloComercial) { $q->where('gp.user_id', $soloComercial); }
        elseif ($comerciales !== null) { $q->whereIn('gp.user_id', $comerciales); }

        $filas = $q->orderByDesc('g.created_at')->get();

        // Facturación real, en una sola consulta para todos los centros de la lista
        $centros = $filas->pluck('centro')->filter()->unique()->values();
        $facturado = $centros->isEmpty() ? collect() : DB::table('helisa')
            ->selectRaw(self::sqlCentro('centro').' c, SUM(base_factura) total')
            ->whereIn(DB::raw(self::sqlCentro('centro')), $centros->all())
            ->groupBy('c')->pluck('total', 'c');

        return $filas->map(function ($f) use ($facturado) {
            $f->id_estado = (int) $f->id_estado;
            $f->valor = (float) $f->valor;
            $f->grupo = self::grupo($f->id_estado);
            $f->dias_quieta = (int) $f->dias_quieta;
            $f->helisa = ($f->grupo === 'venta' && $f->centro) ? (float) ($facturado[$f->centro] ?? 0) : 0.0;
            $f->cobro = $f->grupo === 'venta' ? self::cobro($f->estado_base) : null;

            // Lo facturado de la venta: completa si la base la da por facturada; si no, lo que registre Helisa
            if ($f->cobro === 'facturada') { $f->facturado = $f->valor; }
            elseif ($f->grupo === 'venta') { $f->facturado = min($f->valor, $f->helisa); }
            else { $f->facturado = 0.0; }

            // Lo que hay que facturar YA: ejecutada sin facturar, o el saldo de una facturada en parte
            $f->por_facturar = in_array($f->cobro, ['por_facturar', 'parcial']) ? max(0, $f->valor - $f->facturado) : 0.0;
            $f->pct_facturado = ($f->grupo === 'venta' && $f->valor > 0) ? round($f->facturado / $f->valor * 100) : null;

            // Datos que no cuadran, con el motivo en palabras
            $f->motivo_revisar = null;
            if ($f->grupo === 'venta') {
                if ($f->cobro === 'revisar') {
                    $f->motivo_revisar = $f->estado_base
                        ? 'La base comercial la marca como "'.mb_strtolower($f->estado_base).'" aunque la gestión dice venta'
                        : 'Es venta pero no tiene registro en la base comercial';
                } elseif ($f->cobro === 'facturada' && $f->centro && $f->helisa <= 0) {
                    $f->motivo_revisar = 'Marcada como facturada, pero Helisa no registra nada en su centro de costos';
                } elseif (!$f->centro) {
                    $f->motivo_revisar = 'Es venta pero su presupuesto no tiene centro de costos';
                }
            }
            $f->revisar = $f->motivo_revisar !== null;
            return $f;
        });
    }

    /** Indicadores de un conjunto de gestiones. */
    public static function resumen(Collection $g, int $diasQuieta): array
    {
        $ventas = $g->where('grupo', 'venta');
        $curso = $g->where('grupo', 'curso');
        $quietas = $curso->filter(fn ($f) => $f->dias_quieta > $diasQuieta);
        $vendido = $ventas->sum('valor');
        $facturado = $ventas->sum('facturado');
        $porFacturar = $ventas->sum('por_facturar');
        $ejecucion = $ventas->where('cobro', 'ejecucion')->sum(fn ($f) => $f->valor - $f->facturado);
        $revisarV = $ventas->where('cobro', 'revisar')->sum(fn ($f) => $f->valor - $f->facturado);

        return [
            'gestiones'     => $g->count(),
            'ventas'        => $ventas->count(),
            'perdidas'      => $g->where('grupo', 'perdida')->count(),
            'en_curso'      => $curso->count(),
            'quietas'       => $quietas->count(),
            'conversion'    => $g->count() ? round($ventas->count() / $g->count() * 100, 1) : 0.0,
            'vendido'       => $vendido,
            'facturado'     => $facturado,
            'pct_facturado' => $vendido > 0 ? round($facturado / $vendido * 100) : 0,
            'por_facturar'  => $porFacturar,          // ejecutado y sin facturar: cobrable ya
            'ejecucion'     => $ejecucion,            // vendido que todavía no toca facturar
            'revisar_valor' => $revisarV,
            'n_por_facturar'=> $ventas->where('por_facturar', '>', 0)->count(),
            'helisa'        => $ventas->sum('helisa'),
            'sin_helisa'    => $ventas->filter(fn ($f) => $f->cobro === 'facturada' && $f->centro && $f->helisa <= 0)->count(),
            'en_juego'      => $curso->sum('valor'),
            'revisar'       => $g->where('revisar', true)->count(),
        ];
    }

    /**
     * Líder(es) de cada comercial: [comercial_id => 'Lady Ortiz']. Si un comercial tiene un
     * líder real y otro de pruebas (@local.test), se muestra solo el real.
     */
    public static function lideres(): array
    {
        $filas = DB::table('lider_comercial_user as lc')->join('users as u', 'u.id', '=', 'lc.lider_id')
            ->orderBy('u.name')->get(['lc.comercial_id', 'u.name', 'u.email']);
        $out = [];
        foreach ($filas->groupBy('comercial_id') as $cid => $ls) {
            $reales = $ls->filter(fn ($l) => !str_ends_with((string) $l->email, '@local.test'));
            $out[$cid] = ($reales->isNotEmpty() ? $reales : $ls)->pluck('name')->unique()->implode(' y ');
        }
        return $out;
    }

    /** Reparto de lo vendido por estado de cobro, en % del total (para la barra apilada). */
    public static function reparto(array $r): array
    {
        $v = $r['vendido'];
        if ($v <= 0) { return []; }
        return [
            'is-venta'   => round($r['facturado'] / $v * 100, 1),
            'is-quieta'  => round($r['por_facturar'] / $v * 100, 1),
            'is-curso'   => round($r['ejecucion'] / $v * 100, 1),
            'is-perdida' => round($r['revisar_valor'] / $v * 100, 1),
        ];
    }

    /** "$2.264 M" para cifras grandes; el valor completo va en el title. */
    public static function millones($v): string
    {
        $v = (float) $v;
        if (abs($v) >= 1000000) { return '$'.number_format($v / 1000000, 0, ',', '.').' M'; }
        return '$'.number_format($v, 0, ',', '.');
    }
}
