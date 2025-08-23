@extends('layouts.admin')

@section('title', 'Dashboard da Agência')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Dashboard - {{ $agencia->nome }}</h4>
                    <a href="{{ route('admin.agencias.index') }}" class="btn btn-secondary">Voltar</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">De</label>
                            <input type="date" id="filter_start" class="form-control" value="{{ $start->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Até</label>
                            <input type="date" id="filter_end" class="form-control" value="{{ $end->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="applyFilters" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Contas</h5>
                                    <p class="display-6">{{ number_format($totalContas) }}</p>
                                    <p class="text-muted">Número de contas vinculadas à agência</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Saldo total</h5>
                                    <p class="display-6">{{ number_format($saldoTotal, 2, ',', '.') }}</p>
                                    <p class="text-muted">Soma dos saldos das contas desta agência</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">Transações</h5>
                                    <p class="display-6" id="summary_total_transacoes">{{ $totalTransacoes }}</p>
                                    <p class="text-muted">Total de transações envolvendo esta agência</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Entradas (total)</h6>
                                    <p class="h4" id="summary_entradas">{{ number_format($totalEntradas, 2, ',', '.') }}</p>
                                    <p class="text-muted">Soma de valores recebidos (conta destino nesta agência)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Saídas (total)</h6>
                                    <p class="h4" id="summary_saidas">{{ number_format($totalSaidas, 2, ',', '.') }}</p>
                                    <p class="text-muted">Soma de valores enviados (conta origem nesta agência)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h6>Transações por mês</h6>
                    <canvas id="chartTotal" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6>Entradas x Saídas (por mês)</h6>
                    <canvas id="chartEntradasSaidas" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function renderChartsFromData(data) {
    const months = data.months || [];
    const seriesTotal = data.seriesTotal || [];
    const seriesEntradas = data.seriesEntradas || [];
    const seriesSaidas = data.seriesSaidas || [];

    const ctx1 = document.getElementById('chartTotal').getContext('2d');
    const ctx2 = document.getElementById('chartEntradasSaidas').getContext('2d');

    if (window._chart1) window._chart1.destroy();
    if (window._chart2) window._chart2.destroy();

    window._chart1 = new Chart(ctx1, {
        type: 'bar',
        data: { labels: months, datasets: [{ label: 'Transações', data: seriesTotal, backgroundColor: 'rgba(54,162,235,0.6)' }] },
        options: { responsive: true }
    });

    window._chart2 = new Chart(ctx2, {
        type: 'line',
        data: { labels: months, datasets: [ { label: 'Entradas', data: seriesEntradas, borderColor: 'rgba(40,167,69,1)', tension: 0.2 }, { label: 'Saídas', data: seriesSaidas, borderColor: 'rgba(220,53,69,1)', tension: 0.2 } ] },
        options: { responsive: true }
    });

    // update summary values if present
    if (data.total !== undefined) document.getElementById('summary_total_transacoes').textContent = data.total;
    if (data.entradas !== undefined) document.getElementById('summary_entradas').textContent = Number(data.entradas).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
    if (data.saidas !== undefined) document.getElementById('summary_saidas').textContent = Number(data.saidas).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2});
}

async function fetchDashboardData() {
    const start = document.getElementById('filter_start').value;
    const end = document.getElementById('filter_end').value;
    const url = new URL("{{ route('admin.agencias.dashboard.data', $agencia->id) }}", window.location.origin);
    if (start) url.searchParams.set('start', start);
    if (end) url.searchParams.set('end', end);

    const resp = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!resp.ok) {
        console.error('Erro ao carregar dados do dashboard');
        return;
    }
    const data = await resp.json();
    renderChartsFromData(data);
}

document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardData();
    document.getElementById('applyFilters').addEventListener('click', function() { fetchDashboardData(); });
});
</script>
@endpush
