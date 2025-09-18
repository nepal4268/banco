<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Sistema Bancário') | AdminLTE 3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">Início</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    {{ auth()->user()->nome ?? 'Usuário' }}
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item-title">
                        {{ auth()->user()->nome ?? 'Usuário' }}
                        <br>
                        <small class="text-muted">{{ auth()->user()->perfil->nome ?? 'Perfil' }}</small>
                    </span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Perfil
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Sair
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                 style="opacity: .8">
            <span class="brand-text font-weight-light">Sistema Bancário</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
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
                    <li class="nav-item {{ request()->routeIs('admin.clientes.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Clientes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.clientes.index') }}" class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listar Clientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.clientes.create') }}" class="nav-link {{ request()->routeIs('admin.clientes.create') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Novo Cliente</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Contas -->
                    <li class="nav-item {{ request()->routeIs('admin.contas.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.contas.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-university"></i>
                            <p>
                                Contas
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.contas.index') }}" class="nav-link {{ request()->routeIs('admin.contas.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Listar Contas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.contas.findByBi.form') }}" class="nav-link {{ request()->routeIs('admin.contas.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Nova Conta</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Cartões removed from sidebar per request --}}

                    <!-- Transações -->
                    <li class="nav-item {{ request()->routeIs('transacoes.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('transacoes.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-exchange-alt"></i>
                            <p>
                                Transações
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('transacoes.index') }}" class="nav-link {{ request()->routeIs('transacoes.index') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Listar Transações</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('transacoes.byConta') }}" class="nav-link {{ request()->routeIs('transacoes.byConta') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Por Conta</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('transacoes.deposito') }}" class="nav-link {{ request()->routeIs('transacoes.deposito') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Depósito</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('transacoes.levantamento') }}" class="nav-link {{ request()->routeIs('transacoes.levantamento') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Levantamento</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('transacoes.transferencia') }}" class="nav-link {{ request()->routeIs('transacoes.transferencia') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Transferência</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('transacoes.pagamento') }}" class="nav-link {{ request()->routeIs('transacoes.pagamento') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Pagamento</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('transacoes.cambio') }}" class="nav-link {{ request()->routeIs('transacoes.cambio') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Câmbio</p>
                                        </a>
                                    </li>
                        </ul>
                    </li>

                    <!-- Modal trigger: add a hidden trigger for opening full-account modal from sidebar via JS -->
                    <!-- The actual 'Por Conta' page will also open the modal; we add a global modal here so it's available in all pages -->

                    <!-- Seguros -->
                    <li class="nav-item {{ request()->routeIs('seguros.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('seguros.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>
                                Seguros
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('seguros.apolices.index') }}" class="nav-link {{ request()->routeIs('seguros.apolices.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Apólices</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('seguros.sinistros.index') }}" class="nav-link {{ request()->routeIs('seguros.sinistros.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sinistros</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Relatórios -->
                    <li class="nav-item {{ request()->routeIs('relatorios.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('relatorios.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>
                                Relatórios
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('relatorios.clientes') }}" class="nav-link {{ request()->routeIs('relatorios.clientes') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Relatório de Clientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('relatorios.transacoes') }}" class="nav-link {{ request()->routeIs('relatorios.transacoes') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Relatório de Transações</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Configurações -->
                    <li class="nav-item {{ request()->routeIs('admin.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Administração
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Usuários</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.agencias.index') }}" class="nav-link {{ request()->routeIs('admin.agencias.index') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Agências</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Toast container top-right -->
                <div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 1080; display:flex; flex-direction:column; gap: .5rem;"></div>

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="#">Sistema Bancário</a>.</strong>
        Todos os direitos reservados.
        <div class="float-right d-none d-sm-inline-block">
            <b>Versão</b> 1.0.0
        </div>
    </footer>
</div>

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('js/adminlte.js') }}"></script>

<script>
$(function() {
    // Initialize DataTables
    $('.data-table').DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        }
    });
});
</script>
<script>
// Minimal toast helper across the app
window.showToast = function(message, type='success'){
    try{
        var container = document.getElementById('toast-container');
        if(!container){ return alert(message); }
        var div = document.createElement('div');
        div.className = 'alert alert-' + (type || 'success') + ' shadow';
        div.setAttribute('role','alert');
        div.style.minWidth = '260px';
        div.innerHTML = '<div class="d-flex align-items-center"><div class="flex-fill">'+ message +'</div><button type="button" class="close ml-2" aria-label="Close">&times;</button></div>';
        container.appendChild(div);
        var closer = div.querySelector('.close'); if(closer){ closer.addEventListener('click', function(){ div.remove(); }); }
        setTimeout(function(){ if(div && div.parentNode){ div.remove(); } }, 5000);
    }catch(e){ console.warn('toast error', e); }
}
</script>
<!-- Lightweight fallback bundle for transacoes helpers (no npm required) -->
<script src="{{ asset('js/transacoes.bundle.js') }}"></script>

@stack('scripts')

<!-- Account operations modal (global) -->
<div class="modal fade" id="contaOperationsModal" tabindex="-1" role="dialog" aria-labelledby="contaOperationsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contaOperationsModalLabel">Operações por Conta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-row mb-3">
            <div class="col">
                <input type="text" id="modal_numero_conta" class="form-control" placeholder="Número da conta">
            </div>
            <div class="col-auto">
                <button id="modal_load_conta" class="btn btn-primary">Carregar</button>
            </div>
        </div>

        <div id="modal_conta_info" style="display:none;">
            <div class="mb-2"><strong>Conta:</strong> <span id="mi_numero"></span> — <span id="mi_cliente"></span></div>
            <div class="mb-2"><strong>Agência:</strong> <span id="mi_agencia"></span> — <strong>Saldo:</strong> <span id="mi_saldo"></span> <small id="mi_moeda"></small></div>

            <ul class="nav nav-tabs" id="modalTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="tab-deposito" data-toggle="tab" href="#pane-deposito">Depósito</a></li>
                <li class="nav-item"><a class="nav-link" id="tab-levantamento" data-toggle="tab" href="#pane-levantamento">Levantamento</a></li>
                <li class="nav-item"><a class="nav-link" id="tab-transferencia" data-toggle="tab" href="#pane-transferencia">Transferência</a></li>
                <li class="nav-item"><a class="nav-link" id="tab-pagamento" data-toggle="tab" href="#pane-pagamento">Pagamento</a></li>
            </ul>

            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="pane-deposito">
                    <form id="form_deposito">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Valor</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="valor">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Moeda</label>
                                <select class="form-control" name="moeda_id" id="deposito_moeda"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <input class="form-control" name="descricao">
                        </div>
                        <div class="form-group">
                            <label>Referência externa</label>
                            <input class="form-control" name="referencia_externa">
                        </div>
                        <div class="text-right">
                            <button class="btn btn-success" id="btn_depositar">Depositar</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="pane-levantamento">
                    <form id="form_levantamento">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Valor</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="valor">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Moeda</label>
                                <select class="form-control" name="moeda_id" id="levantamento_moeda"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <input class="form-control" name="descricao">
                        </div>
                        <div class="form-group">
                            <label>Referência externa</label>
                            <input class="form-control" name="referencia_externa">
                        </div>
                        <div class="text-right">
                            <button class="btn btn-warning" id="btn_levantar">Levantar</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="pane-transferencia">
                    <form id="form_transferencia">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Conta destino (número)</label>
                                <input class="form-control" name="conta_destino_numero">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Valor</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="valor">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Moeda</label>
                                <select class="form-control" name="moeda_id" id="transfer_moeda"></select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Referência externa</label>
                                <input class="form-control" name="referencia_externa">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <input class="form-control" name="descricao">
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" id="btn_transferir">Transferir</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="pane-pagamento">
                    <form id="form_pagamento">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Parceiro</label>
                                <input class="form-control" name="parceiro">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Referência</label>
                                <input class="form-control" name="referencia">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Valor</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="valor">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Moeda</label>
                                <select class="form-control" name="moeda_id" id="pagamento_moeda"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <input class="form-control" name="descricao">
                        </div>
                        <div class="text-right">
                            <button class="btn btn-danger" id="btn_pagar">Pagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="modal_conta_notfound" style="display:none;" class="text-danger">Conta não encontrada.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    async function loadMoedas(){
        try{
            const r = await fetch('/api/moedas', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            if(!r.ok) return;
            const json = await r.json();
            const lists = ['deposito_moeda','levantamento_moeda','transfer_moeda','pagamento_moeda'];
            lists.forEach(id => {
                const sel = document.getElementById(id);
                if(!sel) return;
                sel.innerHTML = '';
                (json.data || []).forEach(m => {
                    const opt = document.createElement('option'); opt.value = m.id; opt.textContent = (m.codigo?m.codigo+' - ':'') + (m.nome||''); sel.appendChild(opt);
                });
            });
        }catch(e){ console.warn('Não foi possível carregar moedas', e); }
    }
    loadMoedas();

    const modal = $('#contaOperationsModal');
    const mi = {
        numero: document.getElementById('mi_numero'),
        cliente: document.getElementById('mi_cliente'),
        agencia: document.getElementById('mi_agencia'),
        saldo: document.getElementById('mi_saldo'),
        moeda: document.getElementById('mi_moeda')
    };

    // UI helpers
    function showLoading(el){ if(el) el.style.opacity = '0.6'; }
    function hideLoading(el){ if(el) el.style.opacity = ''; }

    function setInlineError(msg){
        let div = document.getElementById('modal_inline_error');
        if(!div){ div = document.createElement('div'); div.id = 'modal_inline_error'; div.className = 'alert alert-danger mt-2'; document.querySelector('#contaOperationsModal .modal-body').prepend(div); }
        div.textContent = msg; div.style.display = msg ? 'block' : 'none';
    }

    function renderLastTransactions(list){
        let container = document.getElementById('modal_last_tx');
        if(!container){ container = document.createElement('div'); container.id = 'modal_last_tx'; container.className = 'mt-3'; document.getElementById('modal_conta_info').appendChild(container); }
        if(!list || !list.length){ container.innerHTML = '<small class="text-muted">Sem movimentos recentes.</small>'; return; }
        let html = '<h6>Últimos movimentos</h6><ul class="list-group">';
        list.forEach(t => { html += '<li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>#' + t.id + '</strong> ' + t.data + '<br/><small>' + (t.tipo||'') + ' — ' + (t.descricao||'') + '</small></div><span>' + Number(t.valor).toFixed(2) + ' ' + (t.moeda||'') + '</span></li>'; });
        html += '</ul>';
        container.innerHTML = html;
    }

    document.getElementById('modal_load_conta').addEventListener('click', async function(e){
        e.preventDefault();
        setInlineError('');
        const numero = document.getElementById('modal_numero_conta').value.trim();
        if(!numero){ setInlineError('Informe o número da conta'); return; }
        const btn = this; btn.disabled = true; showLoading(btn);
        try{
            const r = await fetch('{{ route('transacoes.findConta') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ numero_conta: numero })
            });
            if(r.status === 403){ setInlineError('Não autorizado'); document.getElementById('modal_conta_info').style.display='none'; document.getElementById('modal_conta_notfound').style.display='none'; return; }
            if(r.status === 404){ document.getElementById('modal_conta_info').style.display='none'; document.getElementById('modal_conta_notfound').style.display='block'; return; }
            const json = await r.json();
            const conta = json.conta;
            document.getElementById('modal_conta_notfound').style.display='none';
            document.getElementById('modal_conta_info').style.display='block';
            mi.numero.textContent = conta.numero_conta;
            mi.cliente.textContent = (conta.cliente && conta.cliente.nome) ? conta.cliente.nome : '—';
            mi.agencia.textContent = (conta.agencia && conta.agencia.nome) ? conta.agencia.nome : '—';
            mi.saldo.textContent = (conta.saldo !== undefined) ? Number(conta.saldo).toFixed(2) : conta.saldo;
            mi.moeda.textContent = conta.moeda ? (conta.moeda.codigo || '') : '';
            modal.data('conta-id', conta.id);
            renderLastTransactions(json.lastTransactions || []);
        }catch(err){ console.error(err); setInlineError('Erro carregando conta: ' + (err.message||'')); }
        finally{ btn.disabled = false; hideLoading(btn); }
    });

    async function submitForm(url, form){
        setInlineError('');
        const btn = form.querySelector('button[type="submit"], button[id]') || null;
        if(btn) btn.disabled = true; if(btn) showLoading(btn);
        const data = {};
        new FormData(form).forEach((v,k) => data[k] = v);
        try{
            const r = await fetch(url, {
                method: 'POST', headers: { 'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify(data)
            });
            const json = await r.json();
            if(!r.ok){ throw new Error(json.error || 'Erro'); }
            // update conta saldo if present
            if(json.conta){
                mi.saldo.textContent = (json.conta.saldo !== undefined) ? Number(json.conta.saldo).toFixed(2) : json.conta.saldo;
            }
            renderLastTransactions(json.lastTransactions || []);
            // visual success
            const successMsg = json.message || 'Operação concluída';
            const sdiv = document.createElement('div'); sdiv.className = 'alert alert-success mt-2'; sdiv.textContent = successMsg;
            form.parentElement.prepend(sdiv);
            setTimeout(()=>{ if(sdiv) sdiv.remove(); }, 4000);
        }catch(e){ setInlineError('Erro: ' + (e.message||'')); }
        finally{ if(btn) btn.disabled = false; if(btn) hideLoading(btn); }
    }

    document.getElementById('btn_depositar').addEventListener('click', function(e){ e.preventDefault(); const contaId = modal.data('conta-id'); if(!contaId) return alert('Carregue a conta primeiro'); submitForm('/transacoes/conta/' + contaId + '/depositar', document.getElementById('form_deposito')); });
    document.getElementById('btn_levantar').addEventListener('click', function(e){ e.preventDefault(); const contaId = modal.data('conta-id'); if(!contaId) return alert('Carregue a conta primeiro'); submitForm('/transacoes/conta/' + contaId + '/levantar', document.getElementById('form_levantamento')); });
    document.getElementById('btn_transferir').addEventListener('click', function(e){ e.preventDefault(); const contaId = modal.data('conta-id'); if(!contaId) return alert('Carregue a conta primeiro'); submitForm('/transacoes/conta/' + contaId + '/transferir', document.getElementById('form_transferencia')); });
    document.getElementById('btn_pagar').addEventListener('click', function(e){ e.preventDefault(); const contaId = modal.data('conta-id'); if(!contaId) return alert('Carregue a conta primeiro'); submitForm('/transacoes/conta/' + contaId + '/pagar', document.getElementById('form_pagamento')); });

    document.querySelectorAll('a[href="{{ route('transacoes.byConta') }}"]').forEach(a => { a.addEventListener('click', function(e){ e.preventDefault(); $('#contaOperationsModal').modal('show'); }); });
});
</script>
@endpush

@if(session('success'))
<script>
    $(function(){
        // Simple modal using Bootstrap's modal if available
        var msg = {!! json_encode(session('success')) !!};
        var modalHtml = '<div class="modal fade" id="successModal" tabindex="-1" role="dialog">\n' +
            '<div class="modal-dialog" role="document">\n' +
            '<div class="modal-content">\n' +
            '<div class="modal-header"><h5 class="modal-title">Sucesso</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>\n' +
            '<div class="modal-body">' + msg + '</div>\n' +
            '<div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button></div>\n' +
            '</div></div></div>';
        $('body').append(modalHtml);
        $('#successModal').modal('show');
    });
</script>
@endif

</body>
</html>