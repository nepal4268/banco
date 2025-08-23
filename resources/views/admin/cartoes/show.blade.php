@extends('layouts.app')

@section('title', 'Detalhes do Cartão')
@section('page-title', 'Detalhes do Cartão')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cartão</h3>
        <div>
            @if(isset($cartao) && $cartao->id)
                <a href="{{ route('cartoes.edit', ['carto' => $cartao->id]) }}" class="btn btn-warning btn-sm">Editar</a>
            @endif
            <a href="{{ route('cartoes.index') }}" class="btn btn-secondary btn-sm">Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <p><strong>Conta:</strong> {{ $cartao->conta->numero_conta ?? 'N/A' }}</p>
        <p><strong>Cliente:</strong> {{ $cartao->conta->cliente->nome ?? 'N/A' }}</p>
        <p><strong>Tipo:</strong> {{ $cartao->tipoCartao->nome ?? 'N/A' }}</p>
        <p><strong>Número (últimos 4):</strong> **** **** **** {{ $cartao->numero_cartao ? substr($cartao->numero_cartao, -4) : '----' }}</p>
        <p><strong>Validade:</strong> {{ $cartao->validade ? $cartao->validade->format('m/Y') : '-' }}</p>
        <p><strong>Limite:</strong> {{ $cartao->limite ? number_format($cartao->limite,2,',','.') : '-' }}</p>
        <p><strong>Status:</strong> {{ $cartao->statusCartao->nome ?? '-' }}</p>
    </div>
</div>

@endsection
