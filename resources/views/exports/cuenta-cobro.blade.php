<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cuenta de cobro {{ $orden->cod_oc }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        p { margin: 0 0 10px; }
        .center { text-align: center; }
        .just { text-align: justify; }
        .ref { text-align: right; font-size: 9px; color: #666; margin-bottom: 6px; }
        .encabezado p { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        .items { margin-top: 10px; }
        .items th { background: #333; color: #fff; padding: 6px 4px; font-size: 10px; text-align: left; }
        .items td { border-bottom: 1px solid #ddd; padding: 5px 4px; }
        .r { text-align: right; }
        .total td { font-weight: bold; border-top: 2px solid #333; padding-top: 6px; }
        .firma { margin-top: 20px; width: 45%; }
        .firma-linea { border-top: 1px solid #333; margin-top: 4px; }
    </style>
</head>
<body>
    @php
        // Total en letras
        $totalLetras = '';
        if (class_exists('NumberFormatter')) {
            $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
            $totalLetras = mb_strtolower($fmt->format($total));
        }

        // Concepto: el servicio del tercero o, si no hay, las descripciones de los items
        $concepto = $tercero->servicio ?: collect($items)->pluck('desc_oc')->filter()->implode('; ');

        // Cédula con puntos
        $soloNum = preg_replace('/\D/', '', $tercero->cedula);
        $cedulaFmt = $soloNum !== '' ? number_format((float) $soloNum, 0, ',', '.') : $tercero->cedula;

        // Fecha en letras: "30 de agosto de 2026"
        try {
            $fechaLetras = \Carbon\Carbon::parse($orden->fecha_aprobacion ?? $fecha)
                ->locale('es')->translatedFormat('j \d\e F \d\e Y');
        } catch (\Throwable $e) {
            $fechaLetras = $fecha;
        }

        $nombreCompleto = $tercero->nombre . ' ' . $tercero->apellido;
    @endphp

    <div class="ref">{{ $orden->cod_oc }}</div>

    <div class="center encabezado">
        <p>CUENTA DE COBRO</p>
        <p>
            BULL MARKETING SAS<br>
            NIT. No. 900.298.176-1
        </p>
        <p>DEBE A:</p>
        <p>{{ mb_strtoupper($nombreCompleto) }}</p>
        <p>CC. {{ $cedulaFmt }}</p>
    </div>

    <p class="center" style="margin-top: 18px;">
        La suma de: {{ $totalLetras }} pesos M/CTE. ${{ number_format($total, 0, ',', '.') }}
    </p>

    <p>Por Concepto de: {{ $concepto }}</p>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Proyecto</th>
                <th>Descripción</th>
                <th class="r">Cant.</th>
                <th class="r">Días</th>
                <th class="r">Otros</th>
                <th class="r">V. unitario</th>
                <th class="r">V. total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
                <tr>
                    <td>{{ $item->itemPresupuesto->num_item }}</td>
                    <td>{{ $item->itemPresupuesto->presto->cod_cc ?? '-' }}</td>
                    <td>{{ $item->desc_oc }}</td>
                    <td class="r">{{ $item->cant_oc }}</td>
                    <td class="r">{{ $item->dias_oc }}</td>
                    <td class="r">{{ $item->otros_oc }}</td>
                    <td class="r">$ {{ number_format($item->vunit_oc, 0, ',', '.') }}</td>
                    <td class="r">$ {{ number_format($item->vtotal_oc, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="7" class="r">TOTAL</td>
                <td class="r">$ {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="just">
        Por favor aplicar la retención en la fuente de acuerdo con lo establecido en los artículos 383 E.T.
        Declaro bajo gravedad de juramento que no solicitare al agente retenedor la aplicación de costos y
        deducciones de conformidad con lo previsto en el numeral 6 del artículo 1.2.4.1.6 del Decreto 2231
        de 2023, y me rigen por lo previsto en el artículo 383 del Estatuto Tributario.
    </p>

    <p class="just">
        No pago aportes a seguridad social de acuerdo con el artículo 1.2.4.1.7 del decreto 1625 de 2016 y
        art 135 ley 1753 de 2015.
    </p>

    <p style="margin-top: 18px;">Cordialmente,</p>

    <div class="firma">
        @if(isset($firmaBase64) && $firmaBase64)
            <img src="{{ $firmaBase64 }}" alt="Firma del Tercero" style="max-height: 70px; display: block; margin-bottom: 5px;">
        @else
            <div style="height: 50px;"></div>
        @endif
        <div class="firma-linea"></div>
    </div>

    <p style="margin-top: 6px;">{{ mb_strtoupper($nombreCompleto) }}</p>
    <p>C.C. {{ $cedulaFmt }} de {{ $tercero->ciudad }}</p>
    <p>Tel: {{ $tercero->telefono }}</p>
    <p>Dirección: {{ $tercero->direccion ?? '' }}</p>
    <p>
        Favor consignar a {{ $tercero->banco }}
        Cta. {{ $tercero->tipo_cuenta ?? 'Ahorros' }}
        No. {{ $tercero->num_cuenta ?? '' }}
        - Titular: {{ ucwords(mb_strtolower($nombreCompleto)) }}
    </p>

    <p style="margin-top: 14px;">{{ $tercero->ciudad }}, {{ $fechaLetras }}</p>
</body>
</html>