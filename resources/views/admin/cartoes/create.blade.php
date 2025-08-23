@extends('layouts.app')

@section('title', 'Novo Cartão')
@section('page-title', 'Adicionar Cartão')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('cartoes.index') }}">Cartões</a></li>
<li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Criar Cartão</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('cartoes.store') }}">
                    @csrf

                    {{-- If coming from a conta, we expect conta_id in query string or passed by controller --}}
                    <input type="hidden" name="conta_id" id="conta_id" value="{{ old('conta_id', request('conta_id') ?? ($conta->id ?? '')) }}">

                    <div class="form-group">
                        <label for="conta_display">Conta associada</label>
                        <input type="text" id="conta_display" class="form-control" readonly
                               value="{{ isset($conta) ? $conta->numero_conta . ' - ' . ($conta->cliente->nome ?? '') : (old('conta_display') ?? '') }}">
                        <small class="form-text text-muted">Se não estiver preenchido, selecione uma conta na lista.</small>
                    </div>

                    <div class="form-group">
                        <label for="tipo_cartao_id">Tipo de Cartão</label>
                        <select name="tipo_cartao_id" id="tipo_cartao_id" class="form-control">
                            <option value="">-- selecione --</option>
                            @foreach($tiposCartao as $t)
                                @php $disabled = in_array($t->id, $tiposAssociados ?? []) ? 'disabled' : ''; @endphp
                                <option value="{{ $t->id }}" data-default-validade-years="{{ $t->validade_anos ?? 3 }}" {{ $disabled }}>{{ $t->nome }}@if($disabled) (já associado)@endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número do Cartão</label>
                        <div class="input-group">
                            <input type="text" name="numero_cartao" id="numero_cartao" class="form-control" value="{{ old('numero_cartao') }}">
                            <input type="hidden" name="numero_cartao_clean" id="numero_cartao_clean" value="">
                        </div>
                        <small class="form-text text-muted">Preencha o número do cartão manualmente.</small>
                        @error('numero_cartao')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="validade">Validade</label>
                        <input type="date" name="validade" id="validade" class="form-control" value="{{ old('validade') }}">
                        @error('validade')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" id="limite_group" style="display:none;">
                        <label for="limite">Limite (apenas para crédito)</label>
                        <input type="number" step="0.01" name="limite" id="limite" class="form-control" value="{{ old('limite') }}">
                    </div>

                    <div class="form-group">
                        <label for="status_cartao_id">Status</label>
                        <select name="status_cartao_id" id="status_cartao_id" class="form-control">
                            @foreach($statusCartao as $s)
                                <option value="{{ $s->id }}">{{ $s->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <button id="btn_save" class="btn btn-success" type="submit">Salvar</button>
                        @if(isset($conta) && isset($conta->id))
                            <a href="{{ route('admin.contas.show', $conta->id) }}" class="btn btn-secondary">Cancelar</a>
                        @else
                            <a href="{{ route('cartoes.index') }}" class="btn btn-secondary">Cancelar</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoSelect = document.getElementById('tipo_cartao_id');
    const validadeInput = document.getElementById('validade');
    const limiteGroup = document.getElementById('limite_group');
    const btnSave = document.getElementById('btn_save');
    const numeroInput = document.getElementById('numero_cartao');

    function showLimiteIfCredit(opt) {
        const label = (opt && opt.text) ? opt.text.toLowerCase() : '';
        if (label.includes('crédito') || label.includes('credit')) {
            limiteGroup.style.display = 'block';
        } else {
            limiteGroup.style.display = 'none';
            const lim = document.getElementById('limite'); if (lim) lim.value = '';
        }
    }

    tipoSelect && tipoSelect.addEventListener('change', function () {
        const opt = tipoSelect.options[tipoSelect.selectedIndex];
        const years = parseInt(opt.getAttribute('data-default-validade-years') || '3', 10);
        if (!isNaN(years)) {
            const d = new Date();
            d.setFullYear(d.getFullYear() + years);
            const yyyy = d.getFullYear();
            const mm = (d.getMonth() + 1).toString().padStart(2, '0');
            const dd = (d.getDate()).toString().padStart(2, '0');
            validadeInput.value = `${yyyy}-${mm}-${dd}`;
        }
        showLimiteIfCredit(opt);
    });

    // Masking for card number: format as 4 groups while typing
    function formatCardNumber(v){
        const digits = v.replace(/\D/g, '').slice(0,16);
        return digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
    }

    numeroInput && numeroInput.addEventListener('input', function(e){
        const formatted = formatCardNumber(this.value);
        this.value = formatted;
        const clean = (this.value || '').replace(/\D/g, '');
        const hidden = document.getElementById('numero_cartao_clean');
        if(hidden) hidden.value = clean;
    });

    // Populate hidden before submit in case user didn't type after paste
    const form = document.querySelector('form');
    if(form){
        form.addEventListener('submit', function(){
            const hidden = document.getElementById('numero_cartao_clean');
            const visible = document.getElementById('numero_cartao');
            if(hidden && visible){
                hidden.value = (visible.value || '').replace(/\D/g, '');
            }
        });
    }

    // Ensure Save remains enabled for create (server will validate stricter)
    if(btnSave) btnSave.disabled = false;
});
</script>
@endpush

</div>
@endsection
