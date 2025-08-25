@extends('layouts.app')

@section('title', 'Transações por Conta')
@section('page-title', 'Transações por Conta')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Transações por Conta</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <label for="numero_conta_input"><strong>Informe o número da conta</strong></label>
            <div class="input-group">
                <input type="text" id="numero_conta_input" class="form-control" placeholder="Número da conta">
                <div class="input-group-append">
                    <button id="btn_buscar_conta" class="btn btn-primary">Pesquisar</button>
                </div>
            </div>
            <div id="por_conta_error" class="text-danger mt-2" style="display:none;"></div>
            <div id="por_conta_loading" class="mt-2" style="display:none;"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...</div>
        </div>

        <div id="por_conta_results" style="display:none;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('btn_buscar_conta');
    const input = document.getElementById('numero_conta_input');
    const results = document.getElementById('por_conta_results');
    const error = document.getElementById('por_conta_error');

    function search(pageQuery = ''){
        const numero = input.value.trim();
        if(!numero){ error.style.display='block'; error.textContent='Informe o número da conta.'; results.style.display='none'; return; }
    error.style.display='none';
    const loading = document.getElementById('por_conta_loading');
    if(loading) loading.style.display = 'inline-block';
    results.style.display = 'none';

    fetch('{{ route('transacoes.searchByConta') }}' + (pageQuery ? ('?' + pageQuery) : ''), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ numero_conta: numero })
        }).then(r => {
            if(r.status === 404) return r.json().then(j => { throw new Error(j.error || 'Conta não encontrada'); });
            return r.json();
        }).then(data => {
            if(loading) loading.style.display = 'none';
            results.innerHTML = data.html || '<p>Sem resultados.</p>';
            results.style.display = 'block';
        }).catch(err => {
            if(loading) loading.style.display = 'none';
            error.style.display='block';
            error.textContent = err.message || 'Erro ao buscar conta.';
            results.style.display='none';
        });
    }

    btn && btn.addEventListener('click', function(e){ e.preventDefault(); search(); });
    input && input.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); search(); } });
});
</script>
@endpush
