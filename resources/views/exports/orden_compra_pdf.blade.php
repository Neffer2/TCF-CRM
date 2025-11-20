<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .meta td { padding: 6px 8px; border: 1px solid #ccc; }
        .items { width: 100%; border-collapse: collapse; }
        .items th, .items td { border: 1px solid #ccc; padding: 6px 8px; }
        .items th { background: #f2f2f2; font-weight: bold; }
        .right { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Orden de Compra</div>
        <div>OC: {{ $orden->cod_oc ?? 'N/A' }}</div>
    </div>

    <table class="meta">
        <tr>
            <td><strong>Cliente</strong></td>
            <td>{{ optional(optional($orden->presupuesto)->gestion)->contacto->empresa ?? 'N/A' }}</td>
            <td><strong>Proveedor</strong></td>
            <td>{{ optional($orden->proveedor)->tercero ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Proyecto</strong></td>
            <td>{{ optional(optional($orden->presupuesto)->gestion)->nom_proyecto_cot ?? 'N/A' }}</td>
            <td><strong>Centro de Costos</strong></td>
            <td>{{ optional($orden->presupuesto)->cod_cc ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Ciudad</strong></td>
            <td>{{ optional(optional($orden->presupuesto)->presupuestoItems->first())->ciudad ?? 'N/A' }}</td>
            <td><strong>Fecha</strong></td>
            <td>{{ optional($orden->created_at)->format('Y-m-d') }}</td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="center">No. ITEM</th>
                <th class="center">CANT</th>
                <th class="center">DIAS</th>
                <th class="center">OTROS</th>
                <th>CARACTERÍSTICAS</th>
                <th class="right">V. UNI</th>
                <th class="right">V. TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orden->ordenItems as $it)
                <tr>
                    <td class="center">{{ $it->display_item }}</td>
                    <td class="center">{{ $it->cant_oc }}</td>
                    <td class="center">{{ $it->dias_oc }}</td>
                    <td class="center">{{ $it->otros_oc }}</td>
                    <td>{{ $it->desc_oc }}</td>
                    <td class="right">{{ number_format($it->vunit_oc, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($it->vtotal_oc, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="right">Total</th>
                <th class="right">
                    {{ number_format($orden->ordenItems->sum('vtotal_oc'), 0, ',', '.') }}
                </th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
