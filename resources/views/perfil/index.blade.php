@extends(Auth::user()->layoutRol())
@section('titulo', 'Mi perfil')
@section('hero-style')
    <div class="position-absolute w-100 min-height-300 top-0 crm-hero crm-hero--photo" style="background-image: url('{{ asset('assets/img/hero-2.jpg') }}')"></div>
@endsection
@section('content')
@php
    $cargo = $u->cargo();
    $foto = $u->avatarUrl();
    $desde = $u->created_at ? $u->created_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : '—';
@endphp
<div class="row">
    <div class="col-12 col-xl-4">
        <div class="card crm-perfil-card" style="--i:0">
            <div class="crm-perfil-card__top">
                <span class="crm-perfil-card__avatar">
                    @if ($foto)<img src="{{ $foto }}" alt="Foto de {{ $u->name }}" onerror="this.remove()">@endif
                    <b>{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</b>
                </span>
                <span class="crm-eyebrow">{{ $cargo }}</span>
                <h1>{{ $u->name }}</h1>
                <p>{{ $u->email }}</p>
            </div>
            <dl class="crm-perfil-card__datos">
                <div><dt>Cargo</dt><dd>{{ $cargo }}</dd></div>
                <div><dt>Teléfono</dt><dd>{{ $u->telefono ?: '—' }}</dd></div>
                <div><dt>Correo</dt><dd>{{ $u->email }}</dd></div>
                <div><dt>En el CRM desde</dt><dd>{{ $desde }}</dd></div>
            </dl>
            <p class="crm-perfil-card__nota">Para cambiar tu foto, tus datos o tu clave, pídeselo al administrador del CRM.</p>
        </div>
    </div>

    <div class="col-12 col-xl-8 mt-4 mt-xl-0">
        <div class="card crm-panel" style="--i:1">
            <div class="card-header">
                <div class="crm-panel__head">
                    <div>
                        <p class="crm-kpi__label">Acceso</p>
                        <h2>Roles y permisos</h2>
                    </div>
                    <span class="crm-kpi__icon"><i class="ni ni-badge" aria-hidden="true"></i></span>
                </div>
            </div>
            <div class="card-body">
                <div class="crm-perfil-bloque">
                    <h3>Roles asignados</h3>
                    <div class="crm-estados">
                        @forelse ($roles as $r)
                            <span class="crm-estado {{ $r->id == $u->rol ? 'is-active tone-neutral' : 'tone-muted' }}"><span class="crm-estado__dot"></span><span class="crm-estado__name">{{ $r->description }}</span>@if ($r->id == $u->rol)<span class="crm-estado__n">activo</span>@endif</span>
                        @empty
                            <span class="crm-kpi__note">Sin roles asignados.</span>
                        @endforelse
                    </div>
                    @if ($roles->count() > 1)
                        <p class="crm-kpi__note">Tienes más de un rol: cámbialo desde el selector de la barra superior.</p>
                    @endif
                </div>
                <div class="crm-perfil-bloque">
                    <h3>Permisos especiales</h3>
                    @forelse ($permisos as $p)
                        <div class="crm-perfil-permiso"><b>{{ $p->clave }}</b><span>{{ $p->descripcion }}</span></div>
                    @empty
                        <p class="crm-kpi__note">Ninguno. Tus accesos son los de tu rol.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @if ($lideres->count() || $equipo->count() || $asisteA->count() || $asistentes->count())
        <div class="card crm-panel mt-4" style="--i:2">
            <div class="card-header">
                <div class="crm-panel__head">
                    <div>
                        <p class="crm-kpi__label">Equipo</p>
                        <h2>Con quién trabajas</h2>
                    </div>
                    <span class="crm-kpi__icon"><i class="ni ni-single-02" aria-hidden="true"></i></span>
                </div>
            </div>
            <div class="card-body">
                @foreach ([['Tu líder comercial', $lideres], ['Comerciales a tu cargo', $equipo], ['Comercial al que asistes', $asisteA], ['Ejecutivos de cuenta que te asisten', $asistentes]] as [$titulo, $lista])
                    @if ($lista->count())
                        <div class="crm-perfil-bloque">
                            <h3>{{ $titulo }}</h3>
                            <div class="crm-perfil-personas">
                                @foreach ($lista as $p)
                                    <span class="crm-perfil-persona"><span class="crm-avatar-mini">{{ mb_strtoupper(mb_substr($p->name, 0, 1)) }}</span>{{ $p->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
