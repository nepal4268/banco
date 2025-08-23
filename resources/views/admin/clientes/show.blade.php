@extends('layouts.app')

@section('title', 'Detalhes do Cliente')
@section('page-title', 'Detalhes do Cliente')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.clientes.index') }}">Clientes</a></li>
<li class="breadcrumb-item active">{{ $cliente->nome }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informações do Cliente</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nome:</strong> {{ $cliente->nome }}<br>
                        <strong>Email:</strong> {{ $cliente->email }}<br>
                        <strong>BI:</strong> {{ $cliente->bi }}<br>
                        <strong>Telefone:</strong> {{ is_array($cliente->telefone) ? implode(', ', $cliente->telefone) : $cliente->telefone }}<br>
                        <strong>Data de Nascimento:</strong> {{ $cliente->data_nascimento->format('d/m/Y') }}<br>
                        <strong>Sexo:</strong> {{ $cliente->sexo == 'M' ? 'Masculino' : 'Feminino' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong> 
                        <span class="badge badge-info">{{ $cliente->tipoCliente->nome ?? 'N/A' }}</span><br>
                        <strong>Status:</strong> 
                        <span class="badge badge-{{ $cliente->statusCliente->nome == 'ativo' ? 'success' : 'danger' }}">
                            {{ $cliente->statusCliente->nome ?? 'N/A' }}
                        </span><br>
                        <strong>Endereço:</strong> {{ $cliente->endereco ?? 'Não informado' }}<br>
                        <strong>Cidade:</strong> {{ $cliente->cidade ?? 'Não informado' }}<br>
                        <strong>Província:</strong> {{ $cliente->provincia ?? 'Não informado' }}<br>
                        <strong>Cadastrado em:</strong> {{ $cliente->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Contas do Cliente -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Contas do Cliente</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.contas.createForClient', $cliente->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nova Conta
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($cliente->contas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Número da Conta</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Saldo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->contas as $conta)
                                <tr>
                                    <td>{{ $conta->numero_conta }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $conta->tipoConta->nome ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $conta->statusConta->nome == 'ativa' ? 'success' : 'danger' }}">
                                            {{ $conta->statusConta->nome ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($conta->saldo, 2, ',', '.') }} AOA</td>
                                    <td>
                                        <a href="{{ route('admin.contas.show', $conta) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Este cliente ainda não possui contas.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Resumo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Resumo</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-university"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Contas</span>
                        <span class="info-box-number">{{ $cliente->contas->count() }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Saldo Total</span>
                        <span class="info-box-number">{{ number_format($cliente->contas->sum('saldo'), 2, ',', '.') }} AOA</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-credit-card"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Cartões</span>
                        <span class="info-box-number">{{ $cliente->contas->sum(function($conta) { return $conta->cartoes->count(); }) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ações Rápidas</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.contas.createForClient', $cliente->id) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-university"></i> Nova Conta
                </a>
                <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-warning btn-block">
                    <i class="fas fa-edit"></i> Editar Cliente
                </a>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-list"></i> Voltar à Lista
                </a>
            </div>
        </div>
    </div>
</div>
@endsection