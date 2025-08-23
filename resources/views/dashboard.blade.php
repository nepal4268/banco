@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalClientes) }}</h3>
                <p>Clientes</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ route('admin.clientes.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($totalContas) }}</h3>
                <p>Contas</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('admin.contas.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($totalTransacoes) }}</h3>
                <p>Transações</p>
            </div>
            <div class="icon">
                <i class="ion ion-card"></i>
            </div>
            <a href="{{ route('transacoes.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($totalCartoes) }}</h3>
                <p>Cartões</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ route('cartoes.index') }}" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Custom tabs (Charts with tabs)-->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Transações por Mês
                </h3>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content p-0">
                    <!-- Morris chart - Sales -->
                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                        <canvas id="transacoesChart" height="300" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- Clientes por Tipo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Clientes por Tipo
                </h3>
            </div>
            <div class="card-body">
                <canvas id="clientesTipoChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.Left col -->

    <!-- right col (We are only adding the ID to make the widgets sortable)-->
    <section class="col-lg-5 connectedSortable">
        <!-- Info Box -->
        <div class="info-box mb-3 bg-info">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Saldo Total</span>
                <span class="info-box-number">{{ number_format($saldoTotal, 2, ',', '.') }} AOA</span>
            </div>
        </div>
        <!-- /.info-box -->

        <!-- Info Box -->
        <div class="info-box mb-3 bg-success">
            <span class="info-box-icon"><i class="far fa-heart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Novos Clientes (Este Mês)</span>
                <span class="info-box-number">{{ $novosClientesMes }}</span>
            </div>
        </div>
        <!-- /.info-box -->

        <!-- Info Box -->
        <div class="info-box mb-3 bg-warning">
            <span class="info-box-icon"><i class="fas fa-shield-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Apólices Ativas</span>
                <span class="info-box-number">{{ $apolicesAtivas }}</span>
            </div>
        </div>
        <!-- /.info-box -->

        <!-- Contas por Status -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Contas por Status</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @foreach($contasPorStatus as $status)
                    <li class="item">
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">{{ $status->nome }}</a>
                            <span class="product-description">
                                {{ $status->total }} contas
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- /.card -->

        <!-- Últimas Transações -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Últimas Transações</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @foreach($ultimasTransacoes as $transacao)
                    <li class="item">
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">
                                {{ $transacao->conta->cliente->nome ?? 'Cliente não encontrado' }}
                                <span class="badge badge-{{ $transacao->valor > 0 ? 'success' : 'danger' }} float-right">
                                    {{ number_format($transacao->valor, 2, ',', '.') }} AOA
                                </span>
                            </a>
                            <span class="product-description">
                                {{ $transacao->tipoTransacao->nome ?? 'Tipo não definido' }} - 
                                {{ $transacao->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('transacoes.index') }}" class="uppercase">Ver Todas as Transações</a>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- right col -->
</div>
<!-- /.row (main row) -->
@endsection

@push('scripts')
<script>
$(function () {
    // Gráfico de Transações por Mês
    var ctx = document.getElementById('transacoesChart').getContext('2d');
    var transacoesData = @json($transacoesPorMes);
    
    var labels = [];
    var data = [];
    var valores = [];
    
    transacoesData.forEach(function(item) {
        var meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        labels.push(meses[item.mes - 1] + '/' + item.ano);
        data.push(item.total);
        valores.push(item.valor_total);
    });
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.reverse(),
            datasets: [{
                label: 'Número de Transações',
                data: data.reverse(),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Clientes por Tipo
    var ctx2 = document.getElementById('clientesTipoChart').getContext('2d');
    var clientesData = @json($clientesPorTipo);
    
    var labels2 = [];
    var data2 = [];
    var colors = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
    
    clientesData.forEach(function(item, index) {
        labels2.push(item.nome);
        data2.push(item.total);
    });
    
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: labels2,
            datasets: [{
                data: data2,
                backgroundColor: colors.slice(0, data2.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endpush