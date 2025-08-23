@extends('layouts.app')

@section('title', 'Cartões')
@section('page-title', 'Cartões')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cartões</h3>
        <!-- Novo Cartão removido: criação somente via detalhes da conta -->
    </div>
    <div class="card-body">
        @if($cartoes->count() > 0)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Conta</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Número (últimos 4)</th>
                        <th>Validade</th>
                        <th>Limite</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartoes as $c)
                        <tr>
                            <td>{{ $c->conta->numero_conta ?? 'N/A' }}</td>
                            <td>{{ $c->conta->cliente->nome ?? 'N/A' }}</td>
                            <td>{{ $c->tipoCartao->nome ?? 'N/A' }}</td>
                            <td>**** **** **** {{ $c->numero_cartao ? substr($c->numero_cartao, -4) : '----' }}</td>
                            <td>{{ $c->validade ? $c->validade->format('m/Y') : '' }}</td>
                            <td>{{ $c->limite ? number_format($c->limite,2,',','.') : '-' }}</td>
                            <td>{{ $c->statusCartao->nome ?? '' }}</td>
                            <td>
                                <a href="{{ route('cartoes.show', ['carto' => $c->id]) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('cartoes.edit', ['carto' => $c->id]) }}" class="btn btn-sm btn-primary">Gerir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $cartoes->withQueryString()->links() }}
        @else
            <p class="text-muted">Nenhum cartão encontrado.</p>
        @endif
    </div>
</div>

@endsection
