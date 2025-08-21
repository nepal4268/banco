@extends('layouts.app')

@section('title', 'Transações')
@section('page-title', 'Transações')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Transações</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Transações</h3>
            </div>

            <!-- Filtros -->
            <div class="card-body">
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
                                <th>Conta</th>
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
                                <td>{{ $transacao->conta->cliente->nome ?? 'N/A' }}</td>
                                <td>{{ $transacao->conta->numero_conta ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $transacao->tipoTransacao->nome ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $transacao->valor > 0 ? 'success' : 'danger' }}">
                                        {{ $transacao->valor > 0 ? '+' : '' }}{{ number_format($transacao->valor, 2, ',', '.') }} AOA
                                    </span>
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
                                <td colspan="9" class="text-center">Nenhuma transação encontrada</td>
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