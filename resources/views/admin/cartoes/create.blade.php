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
                                <option value="{{ $t->id }}" data-default-validade-years="{{ $t->validade_anos ?? 3 }}">{{ $t->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número do Cartão</label>
                        <div class="input-group">
                            <input type="text" name="numero_cartao" id="numero_cartao" class="form-control" readonly value="{{ old('numero_cartao') }}">
                            <div class="input-group-append">
                                <button type="button" id="btn_generate" class="btn btn-primary">Gerar</button>
                            </div>
                        </div>
                        <small id="mask_preview" class="form-text text-muted">Preview: ---- ---- ---- ----</small>
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
                        <button class="btn btn-success" type="submit">Salvar</button>
                        <a href="{{ route('cartoes.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn_generate');
    const numeroInput = document.getElementById('numero_cartao');
    const maskPreview = document.getElementById('mask_preview');
    const tipoSelect = document.getElementById('tipo_cartao_id');
    const validadeInput = document.getElementById('validade');
    const limiteGroup = document.getElementById('limite_group');

    function formatMask(num) {
        if (!num) return '---- ---- ---- ----';
        return num.replace(/(\d{4})(?=\d)/g, '$1 ');
    }

    btn.addEventListener('click', function () {
        // generate 16-digit number: BIN 4000 + 12 random digits
        btn.disabled = true;
        btn.textContent = 'Gerando...';
        setTimeout(() => {
            const random = Math.floor(Math.random() * 999999999999).toString().padStart(12, '0');
            const numero = '4000' + random;
            numeroInput.value = numero;
            maskPreview.textContent = 'Preview: ' + formatMask(numero);
            btn.disabled = false;
            btn.textContent = 'Gerar';
        }, 500); // small delay for UX
    });

    tipoSelect && tipoSelect.addEventListener('change', function () {
        const opt = tipoSelect.options[tipoSelect.selectedIndex];
        const years = parseInt(opt.getAttribute('data-default-validade-years') || '3', 10);
        if (!isNaN(years)) {
            const d = new Date();
            d.setFullYear(d.getFullYear() + years);
            // set to last day of that month
            const yyyy = d.getFullYear();
            const mm = (d.getMonth() + 1).toString().padStart(2, '0');
            const dd = (d.getDate()).toString().padStart(2, '0');
            validadeInput.value = `${yyyy}-${mm}-${dd}`;
        }
        // Show limite input for credit cards - convention: option text contains 'crédito' or 'credit'
        const label = opt.text.toLowerCase();
        if (label.includes('crédito') || label.includes('credit')) {
            limiteGroup.style.display = 'block';
        } else {
            limiteGroup.style.display = 'none';
            document.getElementById('limite').value = '';
        }
    });
});
</script>
@endsection
@extends('layouts.app')

@section('title', 'Criar Cartão')
@section('page-title', 'Criar Cartão')

@section('content')
<div class="card">
    <div class="card-body">
        <p>Página de criação de cartão (placeholder).</p>
    </div>
</div>
@endsection
