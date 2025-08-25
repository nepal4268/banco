@extends('layouts.app')

@section('title', 'Gerir Cartão')
@section('page-title', 'Gerir Cartão')

@section('content')
<div class="card">
    <div class="card-body">
        @if(isset($cartao))
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php $updateAction = (isset($cartao) && isset($cartao->id) && $cartao->id) ? route('cartoes.update', ['carto' => $cartao->id]) : null; @endphp
            <div class="mb-3">
                <strong>Conta:</strong>
                @if($cartao->conta)
                    <a href="{{ route('admin.contas.show', $cartao->conta->id) }}">{{ $cartao->conta->numero_conta ?? $cartao->conta->id }}</a>
                @else
                    <span class="text-danger">Conta associada não encontrada</span>
                @endif
                <br>
                <strong>Cliente:</strong> {{ optional($cartao->conta->cliente)->nome ?? 'N/A' }}
                <br>
                <strong>Número do Cartão:</strong>
                <div class="mt-1" id="card_number_display">{{ $cartao->numero_cartao ?? 'N/A' }}</div>
                <br>
                <label>Validade: <span id="label_validade">{{ $cartao->validade ? \Carbon\Carbon::parse($cartao->validade)->format('d/m/Y') : '-' }}</span></label>
                <br>
                <label>Status atual: <span id="label_status">{{ $cartao->statusCartao->nome ?? '-' }}</span></label>
            </div>

            @php
                // IDs permitidos para substituição: Bloqueado, Expirado, Cancelado
                $allowedStatusIds = \App\Models\StatusCartao::whereIn('nome', ['Bloqueado','Expirado','Cancelado'])->pluck('id')->toArray();
            @endphp
            <form id="cartaoForm" method="POST" action="{{ $updateAction ?? '' }}" data-allowed-status-ids="{{ implode(',', $allowedStatusIds) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Status</label>
                    <select name="status_cartao_id" id="status_cartao_id" class="form-control">
                        @foreach($statusCartao as $s)
                            <option value="{{ $s->id }}" {{ $s->id == old('status_cartao_id', $cartao->status_cartao_id) ? 'selected' : '' }}>{{ $s->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-3 d-flex gap-2">
                    <button id="btn_save_status" class="btn btn-primary" type="submit" {{ $updateAction ? '' : 'disabled' }}>Salvar</button>
                    @if($cartao->conta)
                        <a href="{{ route('admin.contas.show', $cartao->conta->id) }}" class="btn btn-secondary">Cancelar</a>
                    @else
                        <a href="{{ route('cartoes.index') }}" class="btn btn-secondary">Cancelar</a>
                    @endif
                    <button id="btn_toggle_sub" type="button" class="btn btn-outline-danger ml-auto" disabled>Substituir número</button>
                </div>
                
                {{-- Hidden novo_numero field: shown when user opts to substitute --}}
                <div id="substitute_block" style="display:none;">
                    <hr>
                    <div class="form-group">
                        <label for="novo_numero">Novo número do cartão</label>
                        <input type="text" name="novo_numero" id="novo_numero" class="form-control" placeholder="Digite o novo número do cartão" disabled>
                        <small class="form-text text-muted">Digite 16 dígitos se quiser informar manualmente. Quando ativado, o status será alterado para não-Ativo automaticamente.</small>
                    </div>
                </div>

            </form>

            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function(){
                const btnSave = document.getElementById('btn_save_status');
                const btnToggle = document.getElementById('btn_toggle_sub');
                const subBlock = document.getElementById('substitute_block');
                const novoInput = document.getElementById('novo_numero');
                const selStatus = document.querySelector('select[name="status_cartao_id"]');
                const cardNumberDisplay = document.getElementById('card_number_display');
                const allowedStatusIds = (document.getElementById('cartaoForm').dataset.allowedStatusIds || '').split(',').map(Number);
                let substituteMode = false;

                // format card number display in groups of 4
                function formatCardNumberDisplay(v){
                    if(!v) return 'N/A';
                    const digits = String(v).replace(/\D/g, '').slice(0,16);
                    return digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
                }
                if(cardNumberDisplay) cardNumberDisplay.textContent = formatCardNumberDisplay(cardNumberDisplay.textContent);

                function isAllowedForSubstitute(statusId){
                    return allowedStatusIds.includes(Number(statusId));
                }

                btnToggle && btnToggle.addEventListener('click', function(){
                    substituteMode = !substituteMode;
                    if(substituteMode){
                        subBlock.style.display = 'block';
                        novoInput.disabled = false;
                        // ao ativar substituição, se status não for permitido, muda para o primeiro permitido
                        const options = Array.from(selStatus.options);
                        const prefer = options.find(o => allowedStatusIds.includes(Number(o.value)));
                        if(prefer) selStatus.value = prefer.value;
                        btnToggle.classList.remove('btn-outline-danger');
                        btnToggle.classList.add('btn-danger');
                        btnToggle.textContent = 'Cancelar substituição';
                    } else {
                        subBlock.style.display = 'none';
                        novoInput.disabled = true;
                        novoInput.value = '';
                        btnToggle.classList.remove('btn-danger');
                        btnToggle.classList.add('btn-outline-danger');
                        btnToggle.textContent = 'Substituir número';
                    }
                });
                selStatus && selStatus.addEventListener('change', function(){
                    const opt = selStatus.options[selStatus.selectedIndex];
                    const labelStatus = document.getElementById('label_status');
                    if(labelStatus) labelStatus.textContent = opt ? opt.text : '-';

                    // habilita botão substituir apenas se status for permitido
                    const allowed = isAllowedForSubstitute(opt ? opt.value : null);
                    if(btnToggle) btnToggle.disabled = !allowed;
                });

                // estado inicial: habilita Salvar, define substituição conforme status atual
                if(btnSave) btnSave.disabled = false;
                if(selStatus){
                    const curOpt = selStatus.options[selStatus.selectedIndex];
                    const allowed = isAllowedForSubstitute(curOpt ? curOpt.value : null);
                    if(btnToggle) btnToggle.disabled = !allowed;
                }
            });
            </script>
            @endpush

        @else
            <p class="text-danger">Cartão inválido ou não encontrado.</p>
            <a href="{{ route('cartoes.index') }}" class="btn btn-secondary">Voltar</a>
        @endif
    </div>
</div>

@endsection
