{{-- Fila de un anticipo jurídico en las listas de cada rol.
     Parámetros: $anticipo, $ruta (detalle según el rol) y $boton (texto del botón). --}}
@php
    $manos = \App\Services\AnticipoJuridicoFlujo::enManosDe($anticipo);
    $manosTxt = $manos ? (\App\Services\AnticipoJuridicoFlujo::ACTORES[$manos] ?? null) : null;
    $devuelto = in_array((int) $anticipo->estado_id, [11, 12, 13, 16], true);
@endphp
<tr class="aj-fila-lista" @if ($devuelto) style="background-color: #FBE5E1" @endif>
    <td style="width: 16rem;">
        <div class="d-flex px-2 py-1">
            <div>
                <img src="https://www.bullmarketing.com.co/wp-content/uploads/2022/04/cropped-favicon-bull-192x192.png" class="avatar avatar-sm me-3" alt="">
            </div>
            <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-xs">Anticipo #{{ $anticipo->id }} <span class="aj-etiqueta">Jurídico</span></h6>
                <p class="text-xs text-secondary mb-0">
                    {{ $anticipo->oc_id ? 'Orden de compra '.(optional($anticipo->ordenCompra)->cod_oc ?: '#'.$anticipo->oc_id) : 'Sin orden de compra' }}{{ $anticipo->cod_orden_anticipo ? ' · '.$anticipo->cod_orden_anticipo : '' }}
                </p>
            </div>
        </div>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">Proveedor</p>
        <span class="text-xs text-secondary mb-0">{{ optional($anticipo->proveedor)->tercero ?: '—' }}</span>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">Centro de costos</p>
        <span class="text-xs text-secondary mb-0">{{ optional($anticipo->presupuesto)->cod_cc ?: '—' }}</span>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">Valor anticipo</p>
        <p class="text-xs text-secondary mb-0">${{ number_format((float) $anticipo->total_anticipo, 0, ',', '.') }} <span title="Porcentaje sobre el valor base">({{ rtrim(rtrim(number_format((float) $anticipo->porcentaje_anticipo, 2, ',', '.'), '0'), ',') }} % de ${{ number_format((float) $anticipo->valor_base, 0, ',', '.') }})</span></p>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">Productor</p>
        <p class="text-xs text-secondary mb-0">{{ optional($anticipo->productor_info)->name ?: '—' }}</p>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">Estado</p>
        <p class="text-xs text-secondary mb-0">{{ optional($anticipo->estado)->description }}{{ $manosTxt ? ' · lo tiene '.$manosTxt : '' }}</p>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">Solicitado</p>
        <p class="text-xs text-secondary mb-0">{{ $anticipo->fecha_solicitud ? $anticipo->fecha_solicitud->format('d/m/Y') : '—' }}</p>
    </td>
    <td>
        <a href="{{ route($ruta, ['anticipo_id' => $anticipo->id]) }}" class="btn bg-gradient-primary mb-0" title="Ver el anticipo y su recorrido">{{ $boton }}</a>
    </td>
</tr>
