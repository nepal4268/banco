@extends('layouts.app')

@section('title', 'Contas')
@section('page-title', 'Contas')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Contas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Contas</h3>
                    <div class="card-tools">
                    <a href="{{ route('admin.contas.findByBi.form') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nova Conta
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card-body">
                <form method="GET" action="{{ route('admin.contas.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="numero_conta" class="form-control" placeholder="Número da Conta" value="{{ request('numero_conta') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="cliente_nome" class="form-control" placeholder="Nome do Cliente" value="{{ request('cliente_nome') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="tipo_conta_id" class="form-control">
                                <option value="">Todos os tipos</option>
                                @foreach($tiposConta as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('tipo_conta_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status_conta_id" class="form-control">
                                <option value="">Todos os status</option>
                                @foreach($statusConta as $status)
                                    <option value="{{ $status->id }}" {{ request('status_conta_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.contas.index') }}" class="btn btn-secondary">
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
                                <th>Número da Conta</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Saldo</th>
                                <th>Moeda</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contas as $conta)
                            <tr>
                                <td>{{ $conta->id }}</td>
                                <td>{{ $conta->numero_conta }}</td>
                                <td>{{ $conta->cliente->nome ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $conta->tipoConta->nome ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $conta->statusConta->nome == 'ativa' ? 'success' : 'danger' }}">
                                        {{ $conta->statusConta->nome ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ number_format($conta->saldo, 2, ',', '.') }}</td>
                                <td>{{ $conta->moeda->codigo ?? 'AOA' }}</td>
                                <td>{{ $conta->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.contas.show', $conta) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.contas.edit', $conta) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.contas.destroy', $conta) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta conta?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Nenhuma conta encontrada</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center">
                    {{ $contas->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection