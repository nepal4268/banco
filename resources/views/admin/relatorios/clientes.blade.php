@extends('layouts.app')

@section('title', 'Relatório de Clientes')
@section('page-title', 'Relatório de Clientes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Relatório de Clientes</li>
@endsection

@section('content')
<!-- Filtros -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros do Relatório</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('relatorios.clientes') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Data Início:</label>
                            <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                        </div>
                        <div class="col-md-4">
                            <label>Data Fim:</label>
                            <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('relatorios.clientes') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalClientes) }}</h3>
                <p>Total de Clientes</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="row">
            @foreach($clientesPorTipo as $tipo)
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $tipo->nome }}</span>
                        <span class="info-box-number">{{ $tipo->total }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Clientes por Tipo</h3>
            </div>
            <div class="card-body">
                <canvas id="clientesTipoChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Clientes por Status</h3>
            </div>
            <div class="card-body">
                <canvas id="clientesStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Clientes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Clientes</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportarExcel()">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="clientesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>BI</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Cadastrado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->id }}</td>
                                <td>{{ $cliente->nome }}</td>
                                <td>{{ $cliente->email }}</td>
                                <td>{{ $cliente->bi }}</td>
                                <td>{{ $cliente->tipoCliente->nome ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $cliente->statusCliente->nome == 'ativo' ? 'success' : 'danger' }}">
                                        {{ $cliente->statusCliente->nome ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="d-flex justify-content-center">
                    {{ $clientes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // Gráfico de Clientes por Tipo
    var ctx = document.getElementById('clientesTipoChart').getContext('2d');
    var tipoData = @json($clientesPorTipo);
    
    var labels = [];
    var data = [];
    var colors = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
    
    tipoData.forEach(function(item, index) {
        labels.push(item.nome);
        data.push(item.total);
    });
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Clientes por Status
    var ctx2 = document.getElementById('clientesStatusChart').getContext('2d');
    var statusData = @json($clientesPorStatus);
    
    var labels2 = [];
    var data2 = [];
    
    statusData.forEach(function(item, index) {
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

function exportarPDF() {
    // Implementar exportação para PDF
    alert('Funcionalidade de exportação PDF será implementada');
}

function exportarExcel() {
    // Implementar exportação para Excel
    alert('Funcionalidade de exportação Excel será implementada');
}
</script>
@endpush