@extends('layouts.app')

@section('title', 'Transferência')
@section('page-title', 'Transferência')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4">Transferência</h1>
        <p class="text-muted">Formulário para efetuar transferências entre contas.</p>

        <div id="ops_alert" aria-live="polite" class="mb-2"></div>

        <!-- Origin lookup moved to top to reveal the rest after verification -->
        <div class="form-row mb-3">
            <div class="form-group col-md-4">
                <label for="transfer_numero_origem">Conta Origem (número)</label>
                <div class="input-group">
                    <input id="transfer_numero_origem" name="numero_origem" class="form-control conta-input" data-role="transfer-origem" aria-describedby="transfer_origem_info" autocomplete="off" />
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-verify" type="button" data-role="transfer-origem" aria-label="Verificar conta origem">🔎</button>
                    </div>
                </div>
                <small id="transfer_origem_info" class="form-text text-muted">&nbsp;</small>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" value="1" id="transfer_origem_externa">
                    <label class="form-check-label small" for="transfer_origem_externa">Origem externa</label>
                </div>
                <input type="text" id="transfer_origem_externa_num" class="form-control form-control-sm mt-2" placeholder="Conta externa (IBAN/RC)" style="display:none;" aria-hidden="true" />
            </div>
            <div class="form-group col-md-4">
                <label for="transfer_numero_destino">Conta Destino (número)</label>
                <div class="input-group">
                    <input id="transfer_numero_destino" name="numero_destino" class="form-control conta-input" data-role="transfer-destino" aria-describedby="transfer_destino_info" autocomplete="off" />
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-verify" type="button" data-role="transfer-destino" aria-label="Verificar conta destino">🔎</button>
                    </div>
                </div>
                <small id="transfer_destino_info" class="form-text text-muted">&nbsp;</small>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" value="1" id="transfer_destino_externa">
                    <label class="form-check-label small" for="transfer_destino_externa">Destino externo</label>
                </div>
                <input type="text" id="transfer_destino_externa_num" class="form-control form-control-sm mt-2" placeholder="Conta externa (IBAN/RC)" style="display:none;" aria-hidden="true" />
            </div>
            <div class="form-group col-md-4">
                <label for="transfer_valor">Valor</label>
                <input id="transfer_valor" type="number" step="0.01" min="0.01" name="valor" class="form-control" required />
            </div>
        </div>

        <div id="transfer_account_summary" class="card mb-3" style="display:none;" aria-hidden="true">
            <div class="card-body" id="transfer_account_summary_body"></div>
        </div>

        <form id="op_transfer" novalidate>
            <input type="hidden" name="conta_origem_id" id="transfer_conta_origem_id" />
            <input type="hidden" name="conta_destino_id" id="transfer_conta_destino_id" />
            <fieldset>
                <legend class="sr-only">Dados da transferência</legend>
                <div class="op_body" style="display:none; width:100%;">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="transfer_moeda">Moeda</label>
                        <select name="moeda_id" id="transfer_moeda" class="form-control" required aria-required="true"></select>
                        <div class="invalid-feedback" data-field="moeda_id">Selecione a moeda.</div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="transfer_bi_origem">BI do titular (origem) <span class="text-danger">*</span></label>
                        <input id="transfer_bi_origem" name="bi_origem" class="form-control" placeholder="BI do titular da conta de origem" required />
                        <div class="invalid-feedback" data-field="bi_origem">Informe o BI do titular da conta de origem.</div>
                    </div>
                    <div class="form-group col-md-3 text-right align-self-end">
                        <button class="btn btn-primary" type="submit">Executar Transferência</button>
                    </div>
                </div>
                </div>
            </fieldset>
        </form>

        <div class="card mt-3" id="last_operation_card" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalhes da última Operação</h5>
                <button id="transfer_export_btn" class="btn btn-sm btn-outline-secondary" type="button" aria-disabled="true">Exportar CSV</button>
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
    if(window.Transacoes && window.Transacoes.loadMoedasInto) window.Transacoes.loadMoedasInto(['transfer_moeda']);

    async function fetchAndRenderAccount(numero, role){
        if(!numero) return;
        try{
            const json = await window.Transacoes.postJson(findRoute, { numero_conta: numero });
            const conta = json.conta;
            renderAccountInfo(conta, role);
            // set dataset on the input element so we know contaId/moeda without hidden fields
            try{ const inputEl = document.querySelector('.conta-input[data-role="'+role+'"]'); if(inputEl){ inputEl.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) inputEl.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) inputEl.dataset.moedaCodigo = conta.moeda.codigo; } }catch(e){}
            return conta;
        }catch(e){
            document.getElementById(role + '_info')?.textContent = 'Conta não encontrada';
            const body = document.querySelector('#op_transfer .op_body'); if(body) body.style.display='none';
            document.getElementById('last_operation_card').style.display = 'none';
            return null;
        }
    }

    function renderAccountInfo(conta, role){
        const sum = document.getElementById('transfer_account_summary'); const sumBody = document.getElementById('transfer_account_summary_body');
        if(sum && sumBody){
            sumBody.innerHTML = `<dl class="row mb-0"><dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${conta.numero_conta||'—'}</dd><dt class="col-sm-3">Saldo disponível</dt><dd class="col-sm-9">${(typeof conta.saldo !== 'undefined' ? Number(conta.saldo).toFixed(2) : '—')}</dd><dt class="col-sm-3">Titular</dt><dd class="col-sm-9">${conta.cliente?.nome||'—'}</dd><dt class="col-sm-3">Agência</dt><dd class="col-sm-9">${conta.agencia?.nome||conta.agencia?.id||'—'}</dd><dt class="col-sm-3">Moeda</dt><dd class="col-sm-9">${conta.moeda?.codigo||conta.moeda?.nome||'—'}</dd></dl>`;
            sum.style.display='block'; sum.setAttribute('aria-hidden','false');
        }
        const body = document.querySelector('#op_transfer .op_body'); if(body) body.style.display='block';
    }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; fetchAndRenderAccount(input.value.trim(), role); }));
    // add debounced fetch on typing and set dataset.contaId in verify
    document.querySelectorAll('.conta-input').forEach(function(inp){ let t; inp.addEventListener('blur', function(){ const role = this.dataset.role; setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 250); }); inp.addEventListener('input', function(){ const role = this.dataset.role; clearTimeout(t); t = setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 600); }); });

    const oriExternaCb = document.getElementById('transfer_origem_externa'); const dstExternaCb = document.getElementById('transfer_destino_externa'); const oriExternaNum = document.getElementById('transfer_origem_externa_num'); const dstExternaNum = document.getElementById('transfer_destino_externa_num'); if(oriExternaCb){ oriExternaCb.addEventListener('change', function(){ oriExternaNum.style.display = this.checked ? 'block' : 'none'; oriExternaNum.setAttribute('aria-hidden', (!this.checked).toString()); }); } if(dstExternaCb){ dstExternaCb.addEventListener('change', function(){ dstExternaNum.style.display = this.checked ? 'block' : 'none'; dstExternaNum.setAttribute('aria-hidden', (!this.checked).toString()); }); }

    document.getElementById('op_transfer').addEventListener('submit', async function(e){ e.preventDefault(); try{ const formData = new FormData(this); const data = Object.fromEntries(formData.entries()); const origemInput = this.querySelector('.conta-input[data-role="transfer-origem"]'); const destinoInput = this.querySelector('.conta-input[data-role="transfer-destino"]'); const contaOrigemId = formData.get('conta_origem_id') || (origemInput?.dataset.contaId); const contaDestinoId = formData.get('conta_destino_id') || (destinoInput?.dataset.contaId); if(!contaOrigemId || !contaDestinoId) return setOpsAlert('Verifique origem e destino antes de submeter', 'danger'); const origemExterna = document.getElementById('transfer_origem_externa')?.checked; const destinoExterna = document.getElementById('transfer_destino_externa')?.checked; const origemMoeda = origemInput?.dataset.moedaId; const destinoMoeda = destinoInput?.dataset.moedaId; if(!origemExterna && !destinoExterna && origemMoeda && destinoMoeda && origemMoeda !== destinoMoeda){ return setOpsAlert('Não é possível transferir entre contas com moedas diferentes.', 'danger'); }
        // client-side check: ensure valor positive and <= saldo origem
        const valorNum = Number(data.valor || 0);
        if(valorNum <= 0) return setOpsAlert('Valor deve ser maior que zero','danger');
        const saldoOrig = Number(document.querySelector('#transfer_account_summary_body dd:nth-of-type(2)')?.textContent || 0);
        if(!isNaN(saldoOrig) && valorNum > saldoOrig) return setOpsAlert('Valor não pode ser maior que o saldo disponível na conta de origem','danger');
        const btn = this.querySelector('button[type="submit"]'); if(btn){ btn.disabled = true; var old = btn.innerHTML; btn.innerHTML='Processando...'; } let endpoint = '/api/transacoes/transferir'; const payload = { conta_origem_id: contaOrigemId, conta_destino_id: contaDestinoId, valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao };
        // include BI of origin
        if(document.getElementById('transfer_bi_origem')){ const biOri = document.getElementById('transfer_bi_origem').value || ''; if(biOri) payload.bi_origem = biOri; }
        if(origemExterna || destinoExterna){ endpoint = '/api/transacoes/transferir-externo'; payload.origem_externa = !!origemExterna; payload.destino_externa = !!destinoExterna; if(oriExternaNum && oriExternaNum.value) payload.conta_externa_origem = oriExternaNum.value; if(dstExternaNum && dstExternaNum.value) payload.conta_externa_destino = dstExternaNum.value; }

    const resp = await window.Transacoes.postJson(endpoint, payload);
    setOpsAlert(resp.message || 'Transferência efetuada','success'); if(resp.transacao && resp.transacao.id && window.Transacoes && window.Transacoes.renderTransacaoDetailsTo){ window.Transacoes.renderTransacaoDetailsTo('last_operation_details', resp.transacao); }
    // clear form and hide UI
    this.reset();
    const bodyEl = document.querySelector('#op_transfer .op_body'); if(bodyEl) bodyEl.style.display = 'none';
    document.getElementById('transfer_account_summary').style.display = 'none';
    if(btn){ btn.disabled = false; btn.innerHTML = old; }
    }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

    if(window.Transacoes && window.Transacoes.prefillFromQuery){ window.Transacoes.prefillFromQuery({ numero_origem: { selector: '.conta-input[data-role="transfer-origem"]', role: 'transfer-origem', options: { findContaRoute: findRoute, infoIdPrefix: 'transfer_origem_info' } }, numero_destino: { selector: '.conta-input[data-role="transfer-destino"]', role: 'transfer-destino', options: { findContaRoute: findRoute, infoIdPrefix: 'transfer_destino_info' } } }); }

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'" role="alert">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 5000); }
});
</script>
@endpush


