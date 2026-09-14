<div class="row gy-1">
    <div class="row m-0 p-0 mb-3">
        <div class="col-md-12 mt-0">
            <h3 class="m-0">Portal Contratistas:</h3>
            <p class="text-sm mb-0">Bienvenido, confirma que tu infomación está correctamente diligenciada y acepta los términos y condiciones de tu contrato de prestación de servicios.</p>
        </div>
        <div class="form-group col-md-4 mb-0">
            <label for=""></label>
            <input id="cedula" disabled type="text" wire:model="numOrden" class="form-control" placeholder="#ORDEN">
        </div>
    </div>
    @if (!empty($seguimiento))
        <div class="row m-0 p-0 mb-3">
            <div class="col-12">
                <div class="card crm-portal-estado">
                    <div class="crm-portal-estado__head">
                        <span class="crm-eyebrow">Estado de tu orden</span>
                        <h5>{{ $seguimiento['estado'] }}</h5>
                    </div>
                    <ol class="crm-pasos">
                        @foreach ($seguimiento['pasos'] as $i => [$nombre, $hecho])
                            <li class="{{ $hecho ? 'is-ok' : '' }}"><span>{{ $i + 1 }}</span>{{ $nombre }}</li>
                        @endforeach
                    </ol>
                    <p class="crm-portal-estado__pago"><b>Pago:</b> {{ $seguimiento['pago'] }}</p>
                    @if ($seguimiento['rechazo'])<p class="crm-portal-estado__rechazo"><b>Comentario de Bull sobre tus evidencias:</b> {{ $seguimiento['rechazo'] }}</p>@endif
                </div>
            </div>
        </div>
    @endif
    <div class="row m-0 p-0">
        @isset($orden)
            @livewire('productor.terceros.nuevo-personal', ['tercero' => $orden->naturalInfo->tercero, 'orden' => $orden], key($orden->id))
        @endisset
    </div>
</div>
 