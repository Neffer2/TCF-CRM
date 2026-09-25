{{-- Historial de estados de un registro (tabla historial_estados). Uso: @include('components.historial-estados', ['registro' => $orden]) --}}
@php
    $historial = ($registro && method_exists($registro, 'historialEstados')) ? $registro->historialEstados()->with('usuario')->get() : collect();
@endphp
@if ($historial->isNotEmpty())
    <div class="card mt-3 crm-page-card">
        <div class="card-header pb-1">
            <h6 class="mb-0">Historial de estados</h6>
            <p class="text-xs text-muted mb-0">Cada cambio de estado, quién lo hizo y la observación que dejó.</p>
        </div>
        <div class="card-body pt-2 pb-2">
            <ul class="list-unstyled mb-0">
                @foreach ($historial as $h)
                    <li class="text-sm py-1 {{ $loop->last ? '' : 'border-bottom' }}">
                        <span class="text-xs text-muted me-2">{{ optional($h->created_at)->format('Y-m-d H:i') }}</span>
                        @if ($h->estado_anterior !== null)<span class="text-muted">{{ $h->nombreEstado($h->estado_anterior) }}</span> → @endif
                        <b>{{ $h->nombreEstado($h->estado_nuevo) }}</b>
                        @if ($h->usuario) <span class="text-muted">· {{ $h->usuario->name }}</span>
                        @elseif ($h->origen === 'carga inicial') <span class="text-muted">· estado al pasar al historial (25-sep-2026)</span>
                        @endif
                        @if ($h->observacion)<div class="text-xs text-muted ps-3">{{ $h->observacion }}</div>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
