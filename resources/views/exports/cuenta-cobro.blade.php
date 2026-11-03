<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cuenta de cobro {{ $orden->cod_oc }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; text-align: center; margin: 0 0 4px; }
        .sub { text-align: center; color: #666; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        .datos td { padding: 3px 4px; vertical-align: top; }
        .datos .lbl { font-weight: bold; width: 18%; }
        .items { margin-top: 16px; }
        .items th { background: #333; color: #fff; padding: 6px 4px; font-size: 10px; text-align: left; }
        .items td { border-bottom: 1px solid #ddd; padding: 5px 4px; }
        .r { text-align: right; }
        .total td { font-weight: bold; border-top: 2px solid #333; padding-top: 6px; }
        .firma { margin-top: 70px; width: 45%; border-top: 1px solid #333; padding-top: 4px; text-align: center; }
    </style>
</head>
<body>
    <h1>CUENTA DE COBRO</h1>
    <div class="sub">{{ $orden->cod_oc }} &middot; {{ $fecha }}</div>

    <table class="datos">
        <tr>
            <td class="lbl">Nombre:</td>
            <td>{{ $tercero->nombre }} {{ $tercero->apellido }}</td>
            <td class="lbl">Cédula:</td>
            <td>{{ $tercero->cedula }}</td>
        </tr>
        <tr>
            <td class="lbl">Correo:</td>
            <td>{{ $tercero->correo }}</td>
            <td class="lbl">Teléfono:</td>
            <td>{{ $tercero->telefono }}</td>
        </tr>
        <tr>
            <td class="lbl">Ciudad:</td>
            <td>{{ $tercero->ciudad }}</td>
            <td class="lbl">Banco:</td>
            <td>{{ $tercero->banco }}</td>
        </tr>
        <tr>
            <td class="lbl">Num. RUT:</td>
            <td>{{ $tercero->num_rut }}</td>
            <td class="lbl">Servicio:</td>
            <td>{{ $tercero->servicio }}</td>
        </tr>
    </table>

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
                    <td>{{ $i + 1 }}</td>
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

    <div class="firma">
        @if(isset($firmaBase64) && $firmaBase64)
            <img src="{{ $firmaBase64 }}" alt="Firma del Tercero" style="max-height: 70px; display: block; margin-bottom: 5px;">
        @endif
        <div style="border-top: 1px solid #333; padding-top: 4px; text-align: center;">
            {{ $tercero->nombre }} {{ $tercero->apellido }}<br>
            C.C. {{ $tercero->cedula }}
        </div>
    </div>
</body>
</html>