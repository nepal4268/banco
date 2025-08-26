@extends('layouts.app')

@section('title', 'Levantamento')
@section('page-title', 'Levantamento')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4">Levantamento</h1>
        <p class="text-muted">Formulário para efetuar levantamentos em contas.</p>

        <div id="ops_alert" aria-live="polite" class="mb-2"></div>

        <!-- Account lookup moved to top -->
        <div class="form-row mb-3">
            <div class="form-group col-md-6">
                <label for="withdraw_numero_conta">Número da conta</label>
                <div class="input-group">
                    <input id="withdraw_numero_conta" name="numero_conta" class="form-control conta-input" data-role="withdraw" aria-describedby="withdraw_account_info" autocomplete="off" />
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-verify" type="button" data-role="withdraw" aria-label="Verificar conta">🔎</button>
                    </div>
                </div>
                <small id="withdraw_account_info" class="form-text text-muted">&nbsp;</small>
            </div>
        </div>

        <div id="withdraw_account_summary" class="card mb-3" style="display:none;" aria-hidden="true">
            <div class="card-body" id="withdraw_account_summary_body"></div>
        </div>

        <form id="op_withdraw" novalidate>
            <input type="hidden" name="conta_id" id="withdraw_conta_id" />
            <fieldset>
                <legend class="sr-only">Dados do levantamento</legend>
                <div class="op_body" style="display:none; width:100%;">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="withdraw_valor">Valor</label>
                        <input id="withdraw_valor" type="number" step="0.01" min="0.01" name="valor" class="form-control" required />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="withdraw_moeda">Moeda</label>
                        <select name="moeda_id" id="withdraw_moeda" class="form-control" required aria-required="true"></select>
                        <div class="invalid-feedback" data-field="moeda_id">Selecione a moeda.</div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="withdraw_bi">BI do titular <span class="text-danger">*</span></label>
                        <input id="withdraw_bi" name="bi" class="form-control" placeholder="Número do BI do titular" required />
                        <div class="invalid-feedback" data-field="bi">Informe o BI do titular.</div>
                        <small class="form-text text-muted">Informe o BI do titular para confirmar identidade.</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-9">
                        <label for="withdraw_descricao">Descrição</label>
                        <input id="withdraw_descricao" name="descricao" class="form-control" />
                    </div>
                    <div class="form-group col-md-3 text-right align-self-end">
                        <button class="btn btn-warning" type="submit">Executar Levantamento</button>
                    </div>
                </div>
                </div>
            </fieldset>
        </form>

        <div class="card mt-3" id="last_operation_card" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalhes da última Operação</h5>
                <button id="withdraw_export_btn" class="btn btn-sm btn-outline-secondary" type="button" aria-disabled="true">Exportar CSV</button>
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
    if(window.Transacoes && window.Transacoes.loadMoedasInto) window.Transacoes.loadMoedasInto(['withdraw_moeda']);

    async function fetchAndRenderAccount(numero, role){
        if(!numero) return;
        try{
            const json = await window.Transacoes.postJson(findRoute, { numero_conta: numero });
            const conta = json.conta;
            renderAccountInfo(conta, role);
            return conta;
        }catch(e){
            document.getElementById(role + '_account_info').textContent = 'Conta não encontrada';
            const body = document.querySelector('#op_' + role + ' .op_body'); if(body) body.style.display = 'none';
            document.getElementById('last_operation_card').style.display = 'none';
            return null;
        }
    }

    function renderAccountInfo(conta, role){
        const infoEl = document.getElementById(role + '_account_info');
        if(infoEl){
            infoEl.textContent = (conta.cliente?.nome || '—') + ' — ' + (conta.agencia?.nome || conta.agencia?.id || '—');
        }
        try{ document.getElementById(role + '_conta_id').value = conta.id; }catch(e){}

        const sum = document.getElementById(role + '_account_summary');
        const sumBody = document.getElementById(role + '_account_summary_body');
        if(sum && sumBody){
            const html = `
                <dl class="row mb-0">
                    <dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${conta.numero_conta || '—'}</dd>
                    <dt class="col-sm-3">Saldo disponível</dt><dd class="col-sm-9">${(typeof conta.saldo !== 'undefined' ? Number(conta.saldo).toFixed(2) : '—')}</dd>
                    <dt class="col-sm-3">Titular</dt><dd class="col-sm-9">${conta.cliente?.nome || '—'}</dd>
                    <dt class="col-sm-3">Agência</dt><dd class="col-sm-9">${conta.agencia?.nome || conta.agencia?.id || '—'}</dd>
                    <dt class="col-sm-3">Moeda</dt><dd class="col-sm-9">${conta.moeda?.codigo || conta.moeda?.nome || '—'}</dd>
                </dl>
            `;
            sumBody.innerHTML = html;
            sum.style.display = 'block'; sum.setAttribute('aria-hidden', 'false');
        }

        // ensure moeda select contains the account's moeda and select it
        try{
            const sel = document.getElementById('withdraw_moeda');
            if(sel){
                const moeda = conta.moeda;
                if(moeda){
                    let opt = sel.querySelector(`option[value="${moeda.id}"]`);
                    if(!opt){
                        opt = document.createElement('option');
                        opt.value = moeda.id;
                        opt.text = moeda.codigo || moeda.nome || ('Moeda ' + moeda.id);
                        sel.insertBefore(opt, sel.firstChild);
                    }
                    sel.value = moeda.id;
                }
            }
        }catch(e){ console.warn('Erro ao setar moeda:', e); }

        const body = document.querySelector('#op_' + role + ' .op_body'); if(body) body.style.display = 'block';
        document.getElementById('last_operation_card').style.display = 'none';
    }

    // wire verify buttons
    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; fetchAndRenderAccount(input.value.trim(), role); }));

    document.querySelectorAll('.conta-input').forEach(inp => inp.addEventListener('blur', function(){ const role = this.dataset.role; setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 250); }));

    // Submit
    document.getElementById('op_withdraw').addEventListener('submit', async function(e){
        e.preventDefault();
        try{
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            const contaId = formData.get('conta_id') || (this.querySelector('.conta-input[data-role="withdraw"]')?.dataset.contaId);
            if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger');
            const bi = formData.get('bi') || document.getElementById('withdraw_bi')?.value || '';
            // client-side saldo validation
            const saldoAvailable = Number(document.querySelector('#withdraw_account_summary_body dd:nth-of-type(1)')?.textContent || document.querySelector('#withdraw_account_summary_body dd:nth-of-type(2)')?.textContent || 0);
            const valorInput = Number(data.valor || 0);
            if(valorInput <= 0) return setOpsAlert('Valor deve ser maior que zero', 'danger');
            if(!isNaN(saldoAvailable) && valorInput > saldoAvailable) return setOpsAlert('Valor não pode ser maior que o saldo disponível', 'danger');
            const btn = this.querySelector('button[type="submit"]'); if(btn){ btn.disabled = true; var old = btn.innerHTML; btn.innerHTML='Processando...'; }
            const resp = await window.Transacoes.postJson('/api/contas/' + contaId + '/levantar', { valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao, bi: bi });
            setOpsAlert(resp.message || 'Levantamento efetuado','success');
            if(resp.transacao && resp.transacao.id && window.Transacoes && window.Transacoes.renderTransacaoDetailsTo) window.Transacoes.renderTransacaoDetailsTo('last_operation_details', resp.transacao);
            // clear form and hide UI
            this.reset();
            const body = document.querySelector('#op_withdraw .op_body'); if(body) body.style.display = 'none';
            document.getElementById('withdraw_account_summary').style.display = 'none';
            document.getElementById('last_operation_card').style.display = 'block';
            if(btn){ btn.disabled = false; btn.innerHTML = old; }
        }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); }
    });

    if(window.Transacoes && window.Transacoes.prefillFromQuery){ window.Transacoes.prefillFromQuery({ numero_conta: { selector: '.conta-input[data-role="withdraw"]', role: 'withdraw', options: { findContaRoute: findRoute, infoIdPrefix: 'withdraw_account_info' } } }); }

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'" role="alert">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 5000); }
});
</script>
@endpush
