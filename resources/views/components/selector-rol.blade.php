{{-- Selector de rol activo: visible solo para cuentas con más de un rol
     asignado (tabla role_user). users.rol es el rol activo; cambiarlo
     redirige al dashboard del rol elegido. --}}
@auth
    @php
        $rolesDisponibles = Auth::user()->roles()->get(['roles_user.id', 'roles_user.description']);
    @endphp
    @if($rolesDisponibles->count() > 1)
        <li class="nav-item dropdown d-flex align-items-center pe-3">
            <a href="javascript:;" class="nav-link text-white font-weight-bold px-0 dropdown-toggle" id="selectorRol" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ni ni-badge me-sm-1"></i>
                <span class="d-sm-inline d-none">{{ Auth::user()->user_rol->description ?? 'Rol' }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="selectorRol">
                @foreach($rolesDisponibles as $rolDisponible)
                    <li>
                        @if($rolDisponible->id == Auth::user()->rol)
                            <span class="dropdown-item mb-1 text-bold text-success">✓ {{ $rolDisponible->description }}</span>
                        @else
                            <form action="{{ route('cambiar-rol', $rolDisponible->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item border-radius-md mb-1">
                                    {{ $rolDisponible->description }}
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        </li>
    @endif
@endauth
