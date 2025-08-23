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
            @if($cartao->conta)
                <a href="{{ route('admin.contas.show', $cartao->conta->id) }}" class="btn btn-secondary btn-sm">Voltar para Conta</a>
            @else
                <a href="{{ route('cartoes.index') }}" class="btn btn-secondary btn-sm">Voltar</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <p><strong>Conta:</strong> {{ $cartao->conta->numero_conta ?? 'N/A' }}</p>
        <p><strong>Cliente:</strong> {{ $cartao->conta->cliente->nome ?? 'N/A' }}</p>
        <p><strong>Tipo:</strong> {{ $cartao->tipoCartao->nome ?? 'N/A' }}</p>
        <p><strong>Número (últimos 4):</strong> **** **** **** {{ $cartao->numero_cartao ? substr($cartao->numero_cartao, -4) : '----' }}</p>
        <p><strong>Validade:</strong> {{ $cartao->validade ? \Carbon\Carbon::parse($cartao->validade)->format('d/m/Y') : '-' }}</p>
        <p><strong>Limite:</strong> {{ $cartao->limite ? number_format($cartao->limite,2,',','.') : '-' }}</p>
        <p><strong>Status:</strong> {{ $cartao->statusCartao->nome ?? '-' }}</p>

        <hr>

        {{-- Substituir cartão: somente quando status != Ativo --}}
        @if(isset($cartao) && $cartao->id)
            @php
                $statusId = optional($cartao)->status_cartao_id;
                $ativoId = \App\Models\StatusCartao::where('nome', 'Ativo')->value('id');
                $canSubstitute = $statusId && $statusId != $ativoId;
            @endphp

            <form method="POST" action="{{ route('cartoes.web.substituir', ['carto' => $cartao->id]) }}">
                @csrf
                <div class="form-group">
                    <label for="novo_numero">Novo número do cartão</label>
                    <input type="text" name="novo_numero" id="novo_numero" class="form-control" {{ $canSubstitute ? '' : 'disabled' }} placeholder="Digite o novo número do cartão">
                    <small class="form-text text-muted">Apenas preencha se desejar um número manual; será usado se informado. Substituição só permitida quando o cartão não estiver Ativo.</small>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-danger" {{ $canSubstitute ? '' : 'disabled' }}>Substituir Cartão</button>
                </div>
            </form>
        @endif
    </div>
</div>

@endsection
