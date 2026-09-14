{{-- Menú lateral del rol LÍDER COMERCIAL (rol 12): todo acotado a los comerciales
     que tiene a cargo en lider_comercial_user. --}}
<ul class="navbar-nav">
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#lidInicio" class="nav-link active" aria-controls="lidInicio" role="button" aria-expanded="true">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                <i class="ni ni-shop text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Mi equipo</span>
        </a>
        <div class="collapse show" id="lidInicio">
            <ul class="nav ms-4">
                <li @class(['active' => request()->is('dashboard-lider-comercial'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('dashboard-lider-comercial'), 'nav-link' => true]) href="{{ route('dashboard-lider-comercial') }}">
                        <span class="sidenav-mini-icon"> D </span>
                        <span class="sidenav-normal"> Dashboard del equipo </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('base-comercial-general*'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('base-comercial-general*'), 'nav-link' => true]) href="{{ route('base-comercial-general') }}">
                        <span class="sidenav-mini-icon"> B </span>
                        <span class="sidenav-normal"> Base comercial del equipo </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('helisa-general'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('helisa-general'), 'nav-link' => true]) href="{{ route('helisa-general') }}">
                        <span class="sidenav-mini-icon"> H </span>
                        <span class="sidenav-normal"> Helisa general </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#lidPresupuestos" class="nav-link" aria-controls="lidPresupuestos" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                <i class="ni ni-badge text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Presupuestos</span>
        </a>
        <div @class(['show' => request()->is('validaciones') || request()->is('presupuesto-proyecto') || request()->is('presupuesto/*'), 'collapse' => true]) id="lidPresupuestos">
            <ul class="nav ms-4">
                <li @class(['active' => request()->is('validaciones'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('validaciones'), 'nav-link' => true]) href="{{ route('validaciones') }}">
                        <span class="sidenav-mini-icon"> V </span>
                        <span class="sidenav-normal"> Validaciones </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('presupuesto-proyecto'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('presupuesto-proyecto'), 'nav-link' => true]) href="{{ route('presupuesto-proyecto') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal"> Presupuestos del equipo </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#lidAjustes" class="nav-link" aria-controls="lidAjustes" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                <i class="ni ni-settings text-warning text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Ajustes</span>
        </a>
        <div @class(['show' => request()->is('actualizar-perfil-adm'), 'collapse' => true]) id="lidAjustes">
            <ul class="nav ms-4">
                <li @class(['active' => request()->is('actualizar-perfil-adm'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('actualizar-perfil-adm'), 'nav-link' => true]) href="{{ route('actualizar-perfil-adm') }}">
                        <span class="sidenav-mini-icon"> A </span>
                        <span class="sidenav-normal"> Actualizar perfil </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
