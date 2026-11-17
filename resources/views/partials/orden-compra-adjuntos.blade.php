@php
    $archivos = [
        'Cotización' => $orden?->archivo_cot,
        'Remisión' => $orden?->archivo_remision,
        'Orden de compra' => $orden?->archivo_orden_helisa,
        'Cuenta de cobro' => $orden?->archivo_cuenta_cobro,
        'GR' => $orden?->archivo_gr,
        'Comprobante de pago' => $orden?->archivo_comprobante_pago,
        'Firma de remisión' => $orden?->archivo_firma,
    ];

    $esLider = (int) (auth()->user()->rol ?? 0) === 6;
    $mostrarOc = filled($archivos['Orden de compra']) || ($mostrarVistaPreviaOc ?? false);
    $adjuntos = collect($archivos)->filter(fn ($ruta) => filled($ruta));
    $anticipos = $orden?->anticipos?->where('estado_id', \App\Models\Anticipo::ESTADO_APROBADO) ?? collect();

    if ($orden && $esLider && $mostrarOc) {
        $adjuntos->forget('Orden de compra');
        $adjuntos->put('Borrador de OC', null);
    }
@endphp

@if ($adjuntos->isNotEmpty() || $anticipos->isNotEmpty())
    <div class="d-flex flex-wrap gap-2" role="group" aria-label="Archivos de la orden de compra">
        @foreach ($adjuntos as $nombre => $ruta)
            @php
                $url = $ruta === null
                    ? route('orden-compra-lider.pdf', $orden)
                    : asset('storage/' . preg_replace('#^/?public/#', '', ltrim($ruta, '/')));
            @endphp
            <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-sm bg-gradient-secondary mb-0">
                <i class="fas fa-file me-1" aria-hidden="true"></i> Ver {{ $nombre }}
            </a>
        @endforeach
        @include('partials.orden-compra-anticipos', ['anticipos' => $anticipos])
    </div>
@endif
