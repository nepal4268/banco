@extends('layouts.app')

@section('title', 'Detalhes da Conta')
@section('page-title', 'Detalhes da Conta')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.contas.index') }}">Contas</a></li>
<li class="breadcrumb-item active">{{ $conta->numero_conta }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
                <div class="card-header">
                <h3 class="card-title">Informações da Conta</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.contas.edit', $conta) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Número:</strong> {{ $conta->numero_conta }}</p>
                <p><strong>Cliente:</strong> {{ $conta->cliente->nome ?? 'N/A' }}</p>
                <p><strong>Agência:</strong> {{ $conta->agencia->nome ?? 'N/A' }}</p>
                <p><strong>Tipo:</strong> {{ $conta->tipoConta->nome ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ $conta->statusConta->nome ?? 'N/A' }}</p>
                <p><strong>Saldo:</strong> {{ number_format($conta->saldo, 2, ',', '.') }} AOA</p>
                <p><strong>Moeda:</strong> {{ $conta->moeda->nome ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Transações</h3></div>
            <div class="card-body">
                @if(!empty($transacoes) && count($transacoes) > 0)
                    <ul class="list-group">
                        @foreach($transacoes as $t)
                            <li class="list-group-item">
                                {{ $t->created_at->format('d/m/Y H:i') }} - {{ $t->descricao ?? $t->tipoTransacao->nome ?? 'Transação' }} - {{ number_format($t->valor,2,',','.') }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Nenhuma transação encontrada.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Resumo</h3></div>
            <div class="card-body">
                <p><strong>Agência:</strong> {{ $conta->agencia->nome ?? 'N/A' }}</p>
                <p><strong>IBAN:</strong> {{ $conta->iban ?? 'N/A' }}</p>
                <p><strong>Criada em:</strong> {{ $conta->created_at->format('d/m/Y H:i') }}</p>
                <div class="d-flex flex-column">
                    <a href="{{ route('cartoes.index') }}?conta_id={{ $conta->id }}" class="btn btn-info btn-block mb-2">Ver Cartões</a>
                    <a href="{{ route('cartoes.create', ['conta_id' => $conta->id]) }}" class="btn btn-primary btn-block">Adicionar Cartão</a>
                    <a href="{{ route('admin.contas.index') }}" class="btn btn-secondary btn-block mt-2">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
