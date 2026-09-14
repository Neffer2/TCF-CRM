@php
    $tono = ['enviado' => 'tone-ok', 'fallido' => 'tone-bad', 'pendiente' => 'tone-warn'];
    $desde = $registros->total() ? $registros->firstItem() : 0; $hasta = $registros->total() ? $registros->lastItem() : 0;
@endphp
<div class="crm-base">
    <div class="card mb-4 crm-page-card">
        <div class="crm-page-head">
            <div class="crm-page-head__title">
                <span class="crm-eyebrow">Desarrollo · Salud de las notificaciones</span>
                <h1>Notificaciones</h1>
                <p>Cada correo y SMS que envía el CRM queda registrado aquí con su resultado. Los fallos definitivos se avisan a {{ implode(', ', $alertas['correos'] ?? []) ?: 'nadie (configura CRM_ALERTA_CORREOS)' }}{{ !empty($alertas['telefonos']) ? ' y por SMS a '.implode(', ', $alertas['telefonos']) : '' }}, máximo una alerta por canal cada {{ $alertas['cada_minutos'] ?? 60 }} minutos.</p>
            </div>
        </div>
        <div class="crm-page-filters">
            <div class="crm-filters">
                <div class="form-group">
                    <label for="nt_canal">Canal</label>
                    <select id="nt_canal" class="form-control {{ $canal ? 'is-filled' : '' }}" wire:model="canal"><option value="">Correo y SMS</option><option value="correo">Correo</option><option value="sms">SMS</option></select>
                </div>
                <div class="form-group">
                    <label for="nt_estado">Estado</label>
                    <select id="nt_estado" class="form-control {{ $estado ? 'is-filled' : '' }}" wire:model="estado"><option value="">Todos</option><option value="fallido">Fallidos</option><option value="enviado">Enviados</option><option value="pendiente">Pendientes</option></select>
                </div>
                <div class="form-group crm-search">
                    <label for="nt_buscar">Buscar</label>
                    <div class="crm-search__box">
                        <svg class="crm-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                        <input id="nt_buscar" type="text" class="form-control" placeholder="Evento, asunto, referencia, destinatario o error" wire:model.debounce.300ms="buscar">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="crm-kpis crm-kpis--mini mb-4">
        <div class="card crm-kpi crm-kpi--mini {{ $salud['fallidas_24h'] ? 'tone-bad' : 'tone-ok' }}" style="--i:0">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Fallidas · últimas 24 h</p>
                <p class="crm-kpi__value"><span data-count="{{ $salud['fallidas_24h'] }}" data-key="nt_f">{{ $salud['fallidas_24h'] }}</span></p>
                <p class="crm-kpi__note">{{ $salud['fallidas_24h'] ? 'Revisa el error y reintenta.' : 'Todo está saliendo.' }}</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-notification-70" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini tone-ok" style="--i:1">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Enviadas · últimas 24 h</p>
                <p class="crm-kpi__value"><span data-count="{{ $salud['enviadas_24h'] }}" data-key="nt_e">{{ $salud['enviadas_24h'] }}</span></p>
                <p class="crm-kpi__note">Correos y SMS entregados al servidor.</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-send" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:2">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">Correo</p>
                <p class="crm-kpi__value">{{ config('crm.notificaciones.correo_modo') === 'log' ? 'Simulado' : 'SMTP' }}</p>
                <p class="crm-kpi__note">{{ config('crm.notificaciones.correo_modo') === 'log' ? 'MAIL_MAILER=log: solo se registra.' : env('MAIL_HOST').':'.env('MAIL_PORT') }} · desde {{ env('MAIL_FROM_ADDRESS') }}</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-email-83" aria-hidden="true"></i></span>
        </div>
        <div class="card crm-kpi crm-kpi--mini tone-neutral" style="--i:3">
            <div class="crm-kpi__head"><div>
                <p class="crm-kpi__label">SMS</p>
                <p class="crm-kpi__value">{{ config('crm.sms.modo') === 'log' ? 'Simulado' : (config('crm.sms.token') ? 'Hablame' : 'Sin token') }}</p>
                <p class="crm-kpi__note">{{ config('crm.sms.modo') === 'log' ? 'SMS_MODO=log: solo se registra.' : (config('crm.sms.token') ? 'API activa.' : 'Falta SMS_TOKEN en el .env.') }}</p>
            </div></div>
            <span class="crm-kpi__icon"><i class="ni ni-mobile-button" aria-hidden="true"></i></span>
        </div>
    </div>

    <div class="card crm-panel crm-panel--tabla" style="--i:1">
        <div class="card-header">
            <div class="crm-panel__head">
                <div>
                    <p class="crm-kpi__label">Últimas 24 h: @foreach ($porCanal as $c => $filas){{ $c }} {{ $filas->map(fn($f) => $f->n.' '.$f->estado)->implode(', ') }}@if(!$loop->last) · @endif @endforeach</p>
                    <h2>Registro de envíos</h2>
                </div>
                <span class="crm-kpi__icon"><i class="ni ni-archive-2" aria-hidden="true"></i></span>
            </div>
        </div>
        <div class="card-body crm-tabla-wrap" wire:loading.class="is-loading">
            <div class="crm-tabla-scroll">
                <table class="crm-table crm-table--data">
                    <thead><tr><th>Fecha</th><th>Canal</th><th>Evento</th><th>Asunto / texto</th><th>Destinatarios</th><th>Estado</th><th class="is-num">Intentos</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($registros as $i => $r)
                            <tr style="--i:{{ $i }}">
                                <td class="is-fecha-corta">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                                <td><span class="crm-chip {{ $r->canal === 'sms' ? 'tone-warn' : 'tone-neutral' }}">{{ strtoupper($r->canal) }}</span></td>
                                <td class="is-cc">{{ $r->evento }}@if ($r->referencia)<br><small class="crm-doc">{{ $r->referencia }}</small>@endif</td>
                                <td class="is-cliente" title="{{ $r->asunto ?: $r->cuerpo }}"><span class="crm-clamp">{{ $r->asunto ?: \Illuminate\Support\Str::limit(strip_tags($r->cuerpo), 90) }}</span></td>
                                <td class="is-proyecto"><span class="crm-clamp">{{ collect($r->destinatarios)->map(fn($d) => is_array($d) ? $d['email'] : $d)->implode(', ') ?: '—' }}</span></td>
                                <td><span class="crm-chip {{ $tono[$r->estado] ?? 'tone-muted' }}">{{ ucfirst($r->estado) }}</span></td>
                                <td class="is-num">{{ $r->intentos }}</td>
                                <td class="is-num" style="white-space: nowrap">
                                    <button type="button" class="crm-link" wire:click="ver({{ $r->id }})">{{ $abierto == $r->id ? 'Ocultar' : 'Ver' }}</button>
                                    @if ($r->estado === 'fallido')· <button type="button" class="crm-link" wire:click="reintentar({{ $r->id }})" wire:loading.attr="disabled">Reintentar</button>@endif
                                </td>
                            </tr>
                            @if ($abierto == $r->id)
                                <tr class="crm-nt-detalle"><td colspan="8">
                                    @if ($r->error)<p class="crm-nt-error"><b>Error:</b> {{ $r->error }}</p>@endif
                                    @if ($r->copias)<p class="crm-kpi__note"><b>Copia:</b> {{ collect($r->copias)->pluck('email')->implode(', ') }}</p>@endif
                                    @if ($r->adjuntos)<p class="crm-kpi__note"><b>Adjuntos:</b> {{ collect($r->adjuntos)->pluck('nombre')->implode(', ') }}</p>@endif
                                    @if ($r->enviado_at)<p class="crm-kpi__note"><b>Enviado:</b> {{ $r->enviado_at->format('d/m/Y H:i:s') }}</p>@endif
                                    <div class="crm-nt-cuerpo">{!! $r->canal === 'correo' ? $r->cuerpo : nl2br(e($r->cuerpo)) !!}</div>
                                </td></tr>
                            @endif
                        @empty
                            <tr class="is-empty"><td colspan="8"><div class="crm-empty"><strong>Sin registros.</strong><span>Aquí aparecerán los correos y SMS a medida que el CRM los envíe.</span></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="crm-pager">
                <div class="crm-pager__info">Mostrando <b>{{ $desde }}–{{ $hasta }}</b> de <b>{{ number_format($registros->total(), 0, '.', ',') }}</b></div>
                <div class="crm-pager__links">{{ $registros->onEachSide(1)->links() }}</div>
            </div>
        </div>
    </div>
</div>
