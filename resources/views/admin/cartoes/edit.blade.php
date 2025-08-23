@extends('layouts.app')

@section('title', 'Gerir Cartão')
@section('page-title', 'Gerir Cartão')

@section('content')
<div class="card">
    <div class="card-body">
        @if(isset($cartao))
            <div class="mb-3">
                <strong>Status:</strong>
                <span id="cartao-status">{{ optional($cartao->statusCartao)->nome ?? '—' }}</span>
            </div>

            <div class="mb-3">
                <strong>Conta:</strong>
                @if($cartao->conta)
                    <a href="{{ route('contas.show', ['conta' => $cartao->conta->id]) }}">{{ $cartao->conta->numero_conta ?? $cartao->conta->id }}</a>
                @else
                    <span class="text-danger">Conta associada não encontrada</span>
                @endif
            </div>

            <form method="POST" action="{{ route('cartoes.update', ['carto' => $cartao->id]) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="numero_cartao">Número (mostrar apenas os últimos 4)</label>
                    <input type="text" name="numero_cartao" id="numero_cartao" class="form-control" value="{{ old('numero_cartao', $cartao->numero_cartao) }}" readonly>
                </div>

                <div class="form-group">
                    <label>Validade</label>
                    <input type="date" name="validade" class="form-control" value="{{ old('validade', $cartao->validade ? $cartao->validade->toDateString() : '') }}">
                </div>

                <div class="form-group">
                    <label>Limite</label>
                    <input type="number" step="0.01" name="limite" class="form-control" value="{{ old('limite', $cartao->limite) }}">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status_cartao_id" class="form-control">
                        @foreach($statusCartao as $s)
                            <option value="{{ $s->id }}" {{ $s->id == old('status_cartao_id', $cartao->status_cartao_id) ? 'selected' : '' }}>{{ $s->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary">Salvar</button>
                    <a href="{{ route('cartoes.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>

            <hr>

            {{-- Substituir action: only enabled for certain statuses or when conta missing --}}
            <form id="substituir-form" method="POST" action="{{ route('cartoes.web.substituir', ['carto' => $cartao->id]) }}">
                @csrf
                <button id="btn-substituir" class="btn btn-secondary" type="submit" disabled>Substituir</button>
            </form>

            <script>
                (function(){
                    // Allowed statuses for substitution
                    const allowed = ['Bloqueado','Expirado','Cancelado'];
                    const statusText = document.getElementById('cartao-status')?.innerText || '';
                    const hasConta = {{ $cartao->conta ? 'true' : 'false' }};
                    const btn = document.getElementById('btn-substituir');

                    // Enable if status in allowed OR conta missing
                    if(!hasConta || allowed.includes(statusText)){
                        btn.removeAttribute('disabled');
                    }

                    // Prevent accidental submits unless enabled server-side will also verify
                    document.getElementById('substituir-form').addEventListener('submit', function(e){
                        if(btn.hasAttribute('disabled')){
                            e.preventDefault();
                            alert('Substituição não permitida para este cartão.');
                        }
                    });
                })();
            </script>

        @else
            <p class="text-danger">Cartão inválido ou não encontrado.</p>
            <a href="{{ route('cartoes.index') }}" class="btn btn-secondary">Voltar</a>
        @endif
    </div>
</div>

@endsection
