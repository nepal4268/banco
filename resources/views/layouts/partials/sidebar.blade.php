<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name', 'Banco') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->nome ?? 'Usuário' }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Clientes -->
                @can('clientes.view')
                <li class="nav-item">
                    <a href="{{ route('admin.clientes.index') }}" class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clientes</p>
                    </a>
                </li>
                @endcan

                <!-- Contas -->
                @can('contas.view')
                <li class="nav-item">
                    <a href="{{ route('admin.contas.index') }}" class="nav-link {{ request()->routeIs('admin.contas.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-piggy-bank"></i>
                        <p>Contas</p>
                    </a>
                </li>
                @endcan

                <!-- Cartões -->
                @can('cartoes.view')
                <li class="nav-item">
                    <a href="{{ route('admin.cartoes.index') }}" class="nav-link {{ request()->routeIs('admin.cartoes.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Cartões</p>
                    </a>
                </li>
                @endcan

                <!-- Transações -->
                @can('transacoes.view')
                <li class="nav-item">
                    <a href="{{ route('admin.transacoes.index') }}" class="nav-link {{ request()->routeIs('admin.transacoes.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>Transações</p>
                    </a>
                </li>
                @endcan

                <!-- Câmbio -->
                @can('admin.view')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>
                            Câmbio
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.cambio.taxas.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Taxas de Câmbio</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.cambio.operacoes.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Operações</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                <!-- Seguros -->
                @can('seguros.view')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>
                            Seguros
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.seguros.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Apólices</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sinistros.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sinistros</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                <!-- Relatórios -->
                @can('relatorios.view')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Relatórios
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.relatorios.clientes') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Clientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.relatorios.transacoes') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transações</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                <!-- Administração -->
                @can('admin.view')
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Administração
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Usuários</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.perfis.index') }}" class="nav-link {{ request()->routeIs('admin.perfis.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Perfis</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.permissoes.index') }}" class="nav-link {{ request()->routeIs('admin.permissoes.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permissões</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.agencias.index') }}" class="nav-link {{ request()->routeIs('admin.agencias.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Agências</p>
                            </a>
                        </li>

                        <!-- Lookups agrupados -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Lookups
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @php
                                    $lookups = [
                                        ['route' => 'admin.tipo-conta.index', 'label' => 'Tipo de Conta', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.tipo-cliente.index', 'label' => 'Tipo de Cliente', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.tipo-cartao.index', 'label' => 'Tipo de Cartão', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.tipo-transacao.index', 'label' => 'Tipo de Transação', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.status-conta.index', 'label' => 'Status de Conta', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.status-transacao.index', 'label' => 'Status de Transação', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.status-cliente.index', 'label' => 'Status de Cliente', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.status-cartao.index', 'label' => 'Status de Cartão', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.tipo-seguro.index', 'label' => 'Tipo de Seguro', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.status-apolice.index', 'label' => 'Status de Apólice', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.status-sinistro.index', 'label' => 'Status de Sinistro', 'icon' => 'fas fa-list'],
                                        ['route' => 'admin.taxa-cambio.index', 'label' => 'Taxas de Câmbio', 'icon' => 'fas fa-percentage'],
                                        ['route' => 'admin.moedas.index', 'label' => 'Moedas', 'icon' => 'fas fa-coins'],
                                    ];
                                @endphp

                                @foreach($lookups as $lk)
                                    @if (Route::has($lk['route']))
                                        <li class="nav-item">
                                            <a href="{{ route($lk['route']) }}" class="nav-link {{ request()->routeIs(str_replace('admin.','admin.', $lk['route']).'*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>{{ $lk['label'] }}</p>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcan
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
