{{-- Menú lateral del rol CONTROLLER (rol 11): revisión financiera de presupuestos,
     actualizaciones, consumidos y reportes. No ve producción, equipo ni proveedores. --}}
<ul class="navbar-nav">
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#ctrlInicio" class="nav-link active" aria-controls="ctrlInicio" role="button" aria-expanded="true">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                <i class="ni ni-shop text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Inicio</span>
        </a>
        <div class="collapse show" id="ctrlInicio">
            <ul class="nav ms-4">
                <li @class(['active' => request()->is('dashboard-controller'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('dashboard-controller'), 'nav-link' => true]) href="{{ route('dashboard-controller') }}">
                        <span class="sidenav-mini-icon"> D </span>
                        <span class="sidenav-normal"> Dashboard </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('base-comercial-general*'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('base-comercial-general*'), 'nav-link' => true]) href="{{ route('base-comercial-general') }}">
                        <span class="sidenav-mini-icon"> B </span>
                        <span class="sidenav-normal"> Base comercial general </span>
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
        <a data-bs-toggle="collapse" href="#ctrlPresupuestos" class="nav-link" aria-controls="ctrlPresupuestos" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                <i class="ni ni-badge text-primary text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Presupuestos</span>
        </a>
        <div @class(['show' => request()->is('presupuesto-proyecto') || request()->is('actualizaciones') || request()->is('presupuesto/*'), 'collapse' => true]) id="ctrlPresupuestos">
            <ul class="nav ms-4">
                <li @class(['active' => request()->is('presupuesto-proyecto'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('presupuesto-proyecto'), 'nav-link' => true]) href="{{ route('presupuesto-proyecto') }}">
                        <span class="sidenav-mini-icon"> P </span>
                        <span class="sidenav-normal"> Presupuestos </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('actualizaciones'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('actualizaciones'), 'nav-link' => true]) href="{{ route('actualizaciones') }}">
                        <span class="sidenav-mini-icon"> A </span>
                        <span class="sidenav-normal"> Actualizaciones </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#ctrlControl" class="nav-link" aria-controls="ctrlControl" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                <i class="ni ni-chart-bar-32 text-success text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Control</span>
        </a>
        <div @class(['show' => request()->is('consumidos') || request()->is('consumido/*') || request()->is('reporte-consumidos') || request()->is('reporte-plano-helisa*'), 'collapse' => true]) id="ctrlControl">
            <ul class="nav ms-4">
                <li @class(['active' => request()->is('consumidos'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('consumidos'), 'nav-link' => true]) href="{{ route('consumidos') }}">
                        <span class="sidenav-mini-icon"> C </span>
                        <span class="sidenav-normal"> Consumidos </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('reporte-consumidos'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('reporte-consumidos'), 'nav-link' => true]) href="{{ route('reporte-consumidos') }}">
                        <span class="sidenav-mini-icon"> R </span>
                        <span class="sidenav-normal"> Reporte de consumidos </span>
                    </a>
                </li>
                <li @class(['active' => request()->is('reporte-plano-helisa*'), 'nav-item' => true])>
                    <a @class(['active' => request()->is('reporte-plano-helisa*'), 'nav-link' => true]) href="{{ route('reporte-plano-helisa') }}">
                        <span class="sidenav-mini-icon"> PH </span>
                        <span class="sidenav-normal"> Plano Helisa </span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-bs-toggle="collapse" href="#ctrlAjustes" class="nav-link" aria-controls="ctrlAjustes" role="button" aria-expanded="false">
            <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                <i class="ni ni-settings text-warning text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Ajustes</span>
        </a>
        <div @class(['show' => request()->is('actualizar-perfil-adm'), 'collapse' => true]) id="ctrlAjustes">
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
