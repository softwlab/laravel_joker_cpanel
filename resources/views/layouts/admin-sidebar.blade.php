<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" 
                   href="{{ route('admin.users') }}">
                    <i class="fas fa-users"></i> Usuários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.templates*') ? 'active' : '' }}" 
                   href="{{ route('admin.templates.index') }}">
                    <i class="fas fa-landmark"></i> Instituições Bancárias
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.external-apis*') ? 'active' : '' }}" 
                   href="{{ route('admin.external-apis.index') }}">
                    <i class="fas fa-cloud"></i> APIs Externas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dns-records*') ? 'active' : '' }}" 
                   href="{{ route('admin.dns-records.index') }}">
                    <i class="fas fa-globe"></i> Registros DNS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}" 
                   href="{{ route('admin.logs') }}">
                    <i class="fas fa-list-alt"></i> Logs
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.api_keys*') ? 'active' : '' }}" 
                   href="{{ route('admin.api_keys.index') }}">
                    <i class="fas fa-key"></i> API Pública
                </a>
            </li>
        </ul>
    </div>
</nav>