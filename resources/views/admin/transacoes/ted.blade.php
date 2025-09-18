@extends('layouts.app')

@section('title', 'TED')
@section('page-title', 'TED')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>TED</h5>
        <p class="text-muted">Formulário para efetuar TEDs (Transferência Eletrônica Disponível).</p>

        <div id="ops_alert" aria-live="polite" class="mb-2"></div>

        @include('admin.transacoes.partials.op_transfer')
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const findRoute = '{{ route('transacoes.findConta') }}';
    if(window.Transacoes && window.Transacoes.loadMoedasInto) window.Transacoes.loadMoedasInto(['transfer_moeda']);

    async function fetchAndRenderAccount(numero, role){ if(!numero) return; try{ const json = await window.Transacoes.postJson(findRoute, { numero_conta: numero }); const conta = json.conta; renderAccountInfo(conta); try{ const inputEl = document.querySelector('.conta-input[data-role="'+role+'"]'); if(inputEl){ inputEl.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) inputEl.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) inputEl.dataset.moedaCodigo = conta.moeda.codigo; } }catch(e){} return conta; }catch(e){ document.getElementById(role + '_info')?.textContent = 'Conta não encontrada'; const body = document.querySelector('#op_transfer .op_body'); if(body) body.style.display='none'; document.getElementById('last_operation_card')?.style.display = 'none'; return null; } }

    function renderAccountInfo(conta){ const sum = document.getElementById('transfer_account_summary'); const sumBody = document.getElementById('transfer_account_summary_body'); if(sum && sumBody){ sumBody.innerHTML = `<dl class="row mb-0"><dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${conta.numero_conta||'—'}</dd><dt class="col-sm-3">Saldo disponível</dt><dd class="col-sm-9">${(typeof conta.saldo !== 'undefined' ? Number(conta.saldo).toFixed(2) : '—')}</dd><dt class="col-sm-3">Titular</dt><dd class="col-sm-9">${conta.cliente?.nome||'—'}</dd><dt class="col-sm-3">Agência</dt><dd class="col-sm-9">${conta.agencia?.nome||conta.agencia?.id||'—'}</dd><dt class="col-sm-3">Moeda</dt><dd class="col-sm-9">${conta.moeda?.codigo||conta.moeda?.nome||'—'}</dd></dl>`; sum.style.display='block'; sum.setAttribute('aria-hidden','false'); }
        const body = document.querySelector('#op_transfer .op_body'); if(body) body.style.display='block'; }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; fetchAndRenderAccount(input.value.trim(), role); }));
    document.querySelectorAll('.conta-input').forEach(function(inp){ let t; inp.addEventListener('blur', function(){ const role = this.dataset.role; setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 250); }); inp.addEventListener('input', function(){ const role = this.dataset.role; clearTimeout(t); t = setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 600); }); });

    document.getElementById('op_transfer').addEventListener('submit', async function(e){ e.preventDefault(); try{ const formData = new FormData(this); const data = Object.fromEntries(formData.entries()); const origemInput = this.querySelector('.conta-input[data-role="transfer-origem"]'); const destinoInput = this.querySelector('.conta-input[data-role="transfer-destino"]'); const contaOrigemId = formData.get('conta_origem_id') || (origemInput?.dataset.contaId); const contaDestinoId = formData.get('conta_destino_id') || (destinoInput?.dataset.contaId); if(!contaOrigemId || !contaDestinoId) return setOpsAlert('Verifique origem e destino antes de submeter', 'danger'); const valorNum = Number(data.valor || 0); if(valorNum <= 0) return setOpsAlert('Valor deve ser maior que zero','danger'); const saldoOrig = Number(document.querySelector('#transfer_account_summary_body dd:nth-of-type(2)')?.textContent || 0); if(!isNaN(saldoOrig) && valorNum > saldoOrig) return setOpsAlert('Valor não pode ser maior que o saldo disponível na conta de origem','danger'); if(!confirm('Confirmar envio da TED?')) return; const btn = this.querySelector('button[type="submit"]'); if(btn){ btn.disabled = true; var old = btn.innerHTML; btn.innerHTML='Processando...'; } await new Promise(r=> setTimeout(r, 2000)); const endpoint = '/api/transacoes/transferir'; const payload = { conta_origem_id: contaOrigemId, conta_destino_id: contaDestinoId, valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao, tipo: 'TED' }; const resp = await window.Transacoes.postJson(endpoint, payload); setOpsAlert(resp.message || 'TED efetuada','success'); if(resp.transacao && resp.transacao.id && window.Transacoes && window.Transacoes.renderTransacaoDetailsTo){ window.Transacoes.renderTransacaoDetailsTo('last_operation_details', resp.transacao); } this.reset(); const bodyEl = document.querySelector('#op_transfer .op_body'); if(bodyEl) bodyEl.style.display = 'none'; document.getElementById('transfer_account_summary').style.display = 'none'; document.getElementById('last_operation_card').style.display = 'block'; if(btn){ btn.disabled = false; btn.innerHTML = old; } }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

    if(window.Transacoes && window.Transacoes.prefillFromQuery){ window.Transacoes.prefillFromQuery({ numero_origem: { selector: '.conta-input[data-role="transfer-origem"]', role: 'transfer-origem', options: { findContaRoute: findRoute, infoIdPrefix: 'transfer_origem_info' } }, numero_destino: { selector: '.conta-input[data-role="transfer-destino"]', role: 'transfer-destino', options: { findContaRoute: findRoute, infoIdPrefix: 'transfer_destino_info' } } }); }

    function setOpsAlert(msg, type='success'){ if(window.showToast){ window.showToast(msg, type); } const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'" role="alert">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 5000); }
});
</script>
@endpush
