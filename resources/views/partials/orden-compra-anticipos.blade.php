@foreach ($anticipos as $anticipo)
    <a href="{{ route('anticipos.pdf', $anticipo->id) }}" target="_blank" rel="noopener" class="{{ $claseBoton ?? 'btn btn-sm bg-gradient-secondary mb-2' }}">
        <i class="fas fa-file-pdf me-1" aria-hidden="true"></i> Ver anticipo {{ $anticipo->cod_orden_anticipo ?? '#'.$anticipo->id }}
    </a>
@endforeach
