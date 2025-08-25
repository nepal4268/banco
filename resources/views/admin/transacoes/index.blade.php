@extends('layouts.app')

@section('title', 'Transações')
@section('page-title', 'Transações')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Transações</li>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('btn_quick_search');
    const input = document.getElementById('quick_numero_conta');
    const results = document.getElementById('quick_search_results');
    const error = document.getElementById('quick_search_error');

    function doSearch(url = null){
        const numero = input.value.trim();
        if(!numero){ error.style.display='block'; error.textContent='Informe o número da conta.'; results.style.display='none'; return; }
        error.style.display='none';

        fetch(url || '{{ route('transacoes.searchByConta') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ numero_conta: numero })
        }).then(r => {
            if(r.status === 404) return r.json().then(j => { throw new Error(j.error || 'Conta não encontrada'); });
            return r.json();
        }).then(data => {
            results.innerHTML = data.html || '<p>Sem resultados.</p>';
            results.style.display = 'block';

            // intercept pagination links inside results to use AJAX
            results.querySelectorAll('a').forEach(a => {
                const href = a.getAttribute('href') || '';
                if(href.includes('?page=')){
                    a.addEventListener('click', function(e){
                        e.preventDefault();
                        const u = href;
                        // attach page to our endpoint
                        doSearch('{{ route('transacoes.searchByConta') }}?'+u.split('?')[1]);
                    });
                }
            });
        }).catch(err => {
            error.style.display='block';
            error.textContent = err.message || 'Erro ao buscar conta.';
            results.style.display='none';
        });
    }

    btn && btn.addEventListener('click', function(e){ e.preventDefault(); doSearch(); });
    input && input.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); doSearch(); } });
});
</script>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Transações</h3>
            </div>

            <!-- Filtros -->
            <div class="card-body">
                <!-- Quick lookup by account number -->
                <div class="mb-3">
                    <label for="quick_numero_conta"><strong>Pesquisar por Número da Conta</strong></label>
                    <div class="input-group">
                        <input type="text" id="quick_numero_conta" class="form-control" placeholder="Digite o número da conta">
                        <div class="input-group-append">
                            <button id="btn_quick_search" class="btn btn-primary">Pesquisar</button>
                        </div>
                    </div>
                    <div id="quick_search_error" class="text-danger mt-2" style="display:none;"></div>
                </div>

                <div id="quick_search_results" style="display:none;"></div>

                <form method="GET" action="{{ route('transacoes.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" name="cliente_nome" class="form-control" placeholder="Nome do Cliente" value="{{ request('cliente_nome') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="numero_conta" class="form-control" placeholder="Número da Conta" value="{{ request('numero_conta') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="tipo_transacao_id" class="form-control">
                                <option value="">Todos os tipos</option>
                                @foreach($tiposTransacao as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('tipo_transacao_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('transacoes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data/Hora</th>
                                <th>Cliente</th>
                                <th>Conta Origem</th>
                                <th>Conta Destino</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Descrição</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transacoes as $transacao)
                            <tr>
                                <td>{{ $transacao->id }}</td>
                                <td>{{ $transacao->created_at->format('d/m/Y H:i:s') }}</td>
                                @php
                                    $isInternalTransfer = ($transacao->conta_origem_id && $transacao->conta_destino_id);
                                    // prefer origem client name if available, otherwise destino
                                    $clientName = 'N/A';
                                    if($transacao->contaOrigem && $transacao->contaOrigem->cliente) $clientName = $transacao->contaOrigem->cliente->nome;
                                    elseif($transacao->contaDestino && $transacao->contaDestino->cliente) $clientName = $transacao->contaDestino->cliente->nome;
                                @endphp
                                <td>{{ $clientName }}</td>
                                <td>
                                    @if($transacao->contaOrigem)
                                        {{ $transacao->contaOrigem->numero_conta }}
                                        <button class="btn btn-sm btn-link open-conta-modal" data-conta="{{ $transacao->contaOrigem->numero_conta }}">Abrir</button>
                                    @else
                                        {{ $transacao->conta_externa_origem ?? 'Externa' }}
                                    @endif
                                </td>
                                <td>
                                    @if($transacao->contaDestino)
                                        {{ $transacao->contaDestino->numero_conta }}
                                        <button class="btn btn-sm btn-link open-conta-modal" data-conta="{{ $transacao->contaDestino->numero_conta }}">Abrir</button>
                                    @else
                                        {{ $transacao->conta_externa_destino ?? 'Externa' }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $transacao->tipoTransacao->nome ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($isInternalTransfer)
                                        <span class="badge badge-danger">-{{ number_format(abs($transacao->valor), 2, ',', '.') }} AOA</span>
                                        <span class="badge badge-success ml-2">+{{ number_format($transacao->valor, 2, ',', '.') }} AOA</span>
                                    @else
                                        @php $isDebit = (bool) $transacao->conta_origem_id; $badge = $isDebit ? 'danger' : 'success'; $sign = $isDebit ? '-' : ($transacao->valor > 0 ? '+' : ''); @endphp
                                        <span class="badge badge-{{ $badge }}">{{ $sign }}{{ number_format($transacao->valor, 2, ',', '.') }} AOA</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $transacao->statusTransacao->nome == 'concluida' ? 'success' : 'warning' }}">
                                        {{ $transacao->statusTransacao->nome ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($transacao->descricao ?? 'N/A', 50) }}</td>
                                <td>
                                    <a href="{{ route('transacoes.show', $transacao) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">Nenhuma transação encontrada</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center">
                    {{ $transacoes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection