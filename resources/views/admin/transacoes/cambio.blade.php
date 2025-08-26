@extends('layouts.app')

@section('title', 'Câmbio')
@section('page-title', 'Câmbio')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4">Câmbio</h1>
        <p class="text-muted">Operações de câmbio (cotação, compra/venda de moeda).</p>

        <div id="ops_alert" aria-live="polite" class="mb-2"></div>

        <div id="cambio_account_summary" class="card mb-3" style="display:none;" aria-hidden="true">
            <div class="card-body" id="cambio_account_summary_body"></div>
        </div>

        <form id="op_cambio" novalidate>
            <input type="hidden" id="cambio_conta_origem_id" name="conta_origem_id" />
            <input type="hidden" id="cambio_conta_destino_id" name="conta_destino_id" />
            <fieldset>
                <legend class="sr-only">Dados da operação de câmbio</legend>
                <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="cambio_numero_origem">Conta Origem (número)</label>
                    <div class="input-group">
                        <input id="cambio_numero_origem" name="numero_origem" class="form-control conta-input" data-role="cambio-origem" aria-describedby="cambio_origem_info" autocomplete="off" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="cambio-origem" aria-label="Verificar conta origem">🔎</button>
                        </div>
                    </div>
                    <small id="cambio_origem_info" class="form-text text-muted">&nbsp;</small>
                </div>
                <div class="form-group col-md-4">
                    <label for="cambio_numero_destino">Conta Destino (número)</label>
                    <div class="input-group">
                        <input id="cambio_numero_destino" name="numero_destino" class="form-control conta-input" data-role="cambio-destino" aria-describedby="cambio_destino_info" autocomplete="off" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="cambio-destino" aria-label="Verificar conta destino">🔎</button>
                        </div>
                    </div>
                    <small id="cambio_destino_info" class="form-text text-muted">&nbsp;</small>
                </div>
                <div class="form-group col-md-4">
                    <label for="cambio_valor_origem">Valor Origem</label>
                    <input id="cambio_valor_origem" type="number" step="0.01" min="0.01" name="valor_origem" class="form-control" required />
                </div>
            </div>

            <div class="op_body" style="display:none;">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="cambio_moeda_origem">Moeda Origem</label>
                        <select id="cambio_moeda_origem" name="moeda_origem_id" class="form-control" required aria-required="true"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="cambio_moeda_destino">Moeda Destino</label>
                        <select id="cambio_moeda_destino" name="moeda_destino_id" class="form-control" required aria-required="true"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="cambio_cotacao">Cotação</label>
                        <input id="cambio_cotacao" type="number" step="0.0001" name="cotacao" class="form-control" />
                        <small class="form-text text-muted"><a href="#" id="btn_buscar_cotacao">Buscar cotação</a></small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="cambio_bi_origem">BI do titular (origem)</label>
                        <input id="cambio_bi_origem" name="bi_origem" class="form-control" placeholder="BI do titular da conta origem" />
                    </div>
                    <div class="form-group col-md-3 text-right align-self-end">
                        <button class="btn btn-primary" type="submit">Executar Câmbio</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card mt-3" id="last_operation_card" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalhes da última Operação</h5>
                <button id="cambio_export_btn" class="btn btn-sm btn-outline-secondary" type="button" aria-disabled="true">Exportar CSV</button>
            </div>
            <div class="card-body" id="last_operation_details"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const findRoute = '{{ route('transacoes.findConta') }}';
    if(window.Transacoes && window.Transacoes.loadMoedasInto) window.Transacoes.loadMoedasInto(['cambio_moeda_origem','cambio_moeda_destino']);

    async function fetchAndRenderAccount(numero, role){
        if(!numero) return;
        try{
            const json = await window.Transacoes.postJson(findRoute, { numero_conta: numero });
            renderAccountInfo(json.conta, role, json.lastTransactions || []);
            // set dataset on the input so other scripts can read contaId/moeda
            try{
                const inputEl = document.querySelector('.conta-input[data-role="'+role+'"]');
                if(inputEl){ inputEl.dataset.contaId = json.conta.id; if(json.conta.moeda && json.conta.moeda.id) inputEl.dataset.moedaId = json.conta.moeda.id; if(json.conta.moeda && json.conta.moeda.codigo) inputEl.dataset.moedaCodigo = json.conta.moeda.codigo; }
            }catch(e){}
            return json.conta;
        }catch(e){
            document.getElementById(role + '_info')?.textContent = 'Conta não encontrada';
            const body = document.querySelector('#op_cambio .op_body'); if(body) body.style.display='none';
            return null;
        }
    }

    function renderAccountInfo(conta, role, lastTransactions){ const sum = document.getElementById('cambio_account_summary'); const sumBody = document.getElementById('cambio_account_summary_body'); if(sum && sumBody){ sumBody.innerHTML = `<dl class="row mb-0"><dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${conta.numero_conta||'—'}</dd><dt class="col-sm-3">Saldo disponível</dt><dd class="col-sm-9">${(typeof conta.saldo !== 'undefined' ? Number(conta.saldo).toFixed(2) : '—')}</dd><dt class="col-sm-3">Titular</dt><dd class="col-sm-9">${conta.cliente?.nome||'—'}</dd><dt class="col-sm-3">Agência</dt><dd class="col-sm-9">${conta.agencia?.nome||conta.agencia?.id||'—'}</dd><dt class="col-sm-3">Moeda</dt><dd class="col-sm-9">${conta.moeda?.codigo||conta.moeda?.nome||'—'}</dd></dl>`; sum.style.display='block'; sum.setAttribute('aria-hidden','false'); }
        const body = document.querySelector('#op_cambio .op_body'); if(body) body.style.display='block'; const details = document.getElementById('last_operation_details'); if(details && lastTransactions && lastTransactions.length){ let thtml = '<table class="table table-sm"><thead><tr><th>Data</th><th>Tipo</th><th>Valor</th><th>Moeda</th></tr></thead><tbody>'; lastTransactions.slice(0,5).forEach(t=>{ thtml += `<tr><td>${t.data||t.created_at||'—'}</td><td>${t.tipo||'—'}</td><td>${(t.valor!==undefined?Number(t.valor).toFixed(2):'—')}</td><td>${t.moeda||'—'}</td></tr>`; }); thtml += '</tbody></table>'; details.innerHTML = thtml; document.getElementById('last_operation_card').style.display='block'; }
    }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; fetchAndRenderAccount(input.value.trim(), role); }));
    // debounced fetch on typing
    document.querySelectorAll('.conta-input').forEach(function(inp){ let t; inp.addEventListener('blur', function(){ const role = this.dataset.role; setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 250); }); inp.addEventListener('input', function(){ const role = this.dataset.role; clearTimeout(t); t = setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 600); }); });

    document.getElementById('btn_buscar_cotacao').addEventListener('click', async function(e){ e.preventDefault(); try{ const moOrig = document.getElementById('cambio_moeda_origem').value; const moDst = document.getElementById('cambio_moeda_destino').value; if(!moOrig || !moDst) return alert('Selecione ambas as moedas'); const r = await fetch('/api/taxas-cambio/cotacao?moeda_origem='+moOrig+'&moeda_destino='+moDst, { headers:{'Accept':'application/json'}, credentials:'same-origin'}); if(!r.ok) return alert('Erro ao buscar cotação'); const j = await r.json(); const cot = j.cotacao || j.data?.cotacao || j.data?.valor || null; if(cot) document.getElementById('cambio_cotacao').value = cot; else alert('Cotação não disponível'); }catch(e){ console.warn(e); alert('Erro ao buscar cotação'); } });

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'" role="alert">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 5000); }

    document.getElementById('op_cambio').addEventListener('submit', async function(e){ e.preventDefault(); try{ const fd = new FormData(this); const data = Object.fromEntries(fd.entries()); const contaOrig = fd.get('conta_origem_id') || document.querySelector('.conta-input[data-role="cambio-origem"]')?.dataset.contaId; const contaDst = fd.get('conta_destino_id') || document.querySelector('.conta-input[data-role="cambio-destino"]')?.dataset.contaId; if(!contaOrig || !contaDst) return setOpsAlert('Verifique contas antes de submeter','danger'); const moOrig = data.moeda_origem_id; const moDst = data.moeda_destino_id; if(!moOrig || !moDst || moOrig === moDst) return setOpsAlert('Selecione moedas diferentes','danger');
        // require BI and ensure <= saldo origem
        const bi = document.getElementById('cambio_bi_origem')?.value || '';
        if(!bi) return setOpsAlert('Informe o BI do titular da conta de origem','danger');
        const valorOrig = Number(data.valor_origem || 0);
        if(valorOrig <= 0) return setOpsAlert('Valor deve ser maior que zero','danger');
        const saldoOrig = Number(document.querySelector('#cambio_account_summary_body dd:nth-of-type(2)')?.textContent || 0);
        if(!isNaN(saldoOrig) && valorOrig > saldoOrig) return setOpsAlert('Valor não pode ser maior que o saldo disponível na conta de origem','danger');
        const payload = { conta_origem_id: contaOrig, conta_destino_id: contaDst, valor_origem: data.valor_origem, moeda_origem_id: moOrig, moeda_destino_id: moDst, cotacao: data.cotacao, bi_origem: bi };
    const resp = await window.Transacoes.postJson('/api/transacoes/cambio', payload);
    setOpsAlert(resp.message || 'Câmbio efetuado', 'success');
    if(resp.operacao_cambio && resp.operacao_cambio.id && window.Transacoes && window.Transacoes.renderTransacaoDetailsTo){ window.Transacoes.renderTransacaoDetailsTo('last_operation_details', resp.operacao_cambio); }
    // clear form and hide UI
    this.reset(); const body = document.querySelector('#op_cambio .op_body'); if(body) body.style.display = 'none'; const sum = document.getElementById('cambio_account_summary'); if(sum) sum.style.display = 'none';
    }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

    if(window.Transacoes && window.Transacoes.prefillFromQuery){ try{ const params = new URLSearchParams(window.location.search); const numOrig = params.get('numero_origem') || ''; const numDst = params.get('numero_destino') || ''; if(numOrig){ const i = document.querySelector('.conta-input[data-role="cambio-origem"]'); if(i){ i.value = numOrig; fetchAndRenderAccount(numOrig, 'cambio-origem'); } } if(numDst){ const i2 = document.querySelector('.conta-input[data-role="cambio-destino"]'); if(i2){ i2.value = numDst; fetchAndRenderAccount(numDst, 'cambio-destino'); } } }catch(e){} }
});
</script>
@endpush
 