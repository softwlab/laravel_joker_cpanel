<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}" 
                   href="{{ route('cliente.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Painel Principal
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cliente.banks*') ? 'active' : '' }}" 
                   href="{{ route('cliente.banks') }}">
                    <i class="fas fa-file-alt"></i> Templates Bancários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cliente.visitantes*') ? 'active' : '' }}" 
                   href="{{ route('cliente.visitantes.index') }}">
                    <i class="fas fa-users"></i> Acessos de Visitantes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cliente.informacoes*') ? 'active' : '' }}" 
                   href="{{ route('cliente.informacoes.index') }}">
                    <i class="fas fa-money-bill"></i> Dados Bancários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cliente.estatisticas*') ? 'active' : '' }}" 
                   href="{{ route('cliente.estatisticas.index') }}">
                    <i class="fas fa-chart-line"></i> Relatórios e Análises
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('cliente.profile*') ? 'active' : '' }}" 
                   href="{{ route('cliente.profile') }}">
                    <i class="fas fa-user-cog"></i> Minha Conta
                </a>
            </li>
        </ul>
    </div>
</nav>