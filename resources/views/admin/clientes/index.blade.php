@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Clientes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Clientes</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Novo Cliente
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card-body">
                <form method="GET" action="{{ route('admin.clientes.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="nome" class="form-control" placeholder="Nome" value="{{ request('nome') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="bi" class="form-control" placeholder="BI" value="{{ request('bi') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="tipo_cliente_id" class="form-control">
                                <option value="">Todos os tipos</option>
                                @foreach($tiposCliente as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('tipo_cliente_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status_cliente_id" class="form-control">
                                <option value="">Todos os status</option>
                                @foreach($statusCliente as $status)
                                    <option value="{{ $status->id }}" {{ request('status_cliente_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>BI</th>
                                <th>Telefone</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->id }}</td>
                                <td>{{ $cliente->nome }}</td>
                                <td>{{ $cliente->email }}</td>
                                <td>{{ $cliente->bi }}</td>
                                <td>{{ is_array($cliente->telefone) ? implode(', ', $cliente->telefone) : $cliente->telefone }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $cliente->tipoCliente->nome ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $cliente->statusCliente->nome == 'ativo' ? 'success' : 'danger' }}">
                                        {{ $cliente->statusCliente->nome ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.clientes.show', $cliente) }}" class="btn btn-info btn-sm" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.contas.createForClient', $cliente->id) }}" class="btn btn-success btn-sm" title="Abrir Conta">
                                            <i class="fas fa-university"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.clientes.destroy', $cliente) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Nenhum cliente encontrado</td>
                            </tr>
                            @endforelse
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