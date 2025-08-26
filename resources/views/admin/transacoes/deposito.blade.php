@extends('layouts.app')

@section('title', 'Depósito')
@section('page-title', 'Depósito')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4">Depósito</h1>
        <p class="text-muted">Execute um depósito em uma conta. Verifique a conta antes de submeter.</p>

        <div id="ops_alert" aria-live="polite" class="mb-2"></div>

        <form id="op_deposit" novalidate>
            <input type="hidden" name="conta_id" id="deposit_conta_id" />
            <fieldset>
                <legend class="sr-only">Dados do depósito</legend>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="deposit_numero_conta">Número da conta</label>
                        <div class="input-group">
                            <input id="deposit_numero_conta" name="numero_conta" class="form-control conta-input" data-role="deposit" aria-describedby="deposit_account_info" autocomplete="off" />
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-verify" type="button" data-role="deposit" aria-label="Verificar conta">🔎</button>
                            </div>
                        </div>
                        <small id="deposit_account_info" class="form-text text-muted">&nbsp;</small>
                    </div>
                </div>

                <div id="deposit_account_summary" class="card mb-3" style="display:none;" aria-hidden="true">
                    <div class="card-body" id="deposit_account_summary_body">
                        <!-- populated dynamically -->
                    </div>
                </div>

                <div class="op_body" style="display:none; width:100%;">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="deposit_valor">Valor</label>
                            <input id="deposit_valor" type="number" step="0.01" min="0.01" name="valor" class="form-control" required />
                        </div>
                        <div class="form-group col-md-3">
                            <label for="deposit_moeda">Moeda</label>
                            <select name="moeda_id" id="deposit_moeda" class="form-control" required aria-required="true"></select>
                            <div class="invalid-feedback" data-field="moeda_id">Selecione a moeda.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                                <label for="deposit_descricao">Descrição</label>
                                <input id="deposit_descricao" name="descricao" class="form-control" />
                            </div>
                            <div class="form-group col-md-3">
                                <label for="deposit_depositante">Nome do depositante</label>
                                <input id="deposit_depositante" name="depositante" class="form-control" placeholder="Nome da pessoa que fez o depósito" />
                            </div>
                            <div class="form-group col-md-3 text-right align-self-end">
                                <button class="btn btn-success" type="submit">Executar Depósito</button>
                            </div>
                    </div>
                </div>
            </fieldset>
        </form>

        <div class="card mt-3" id="last_operation_card" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalhes da última Operação</h5>
                <div>
                    <button id="deposit_export_btn" class="btn btn-sm btn-outline-secondary" type="button" aria-disabled="true">Exportar CSV</button>
                </div>
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
    if(window.Transacoes && window.Transacoes.loadMoedasInto) window.Transacoes.loadMoedasInto(['deposit_moeda']);

    async function fetchAndRenderAccount(numero, role){
        if(!numero) return;
        try{
            const json = await window.Transacoes.postJson(findRoute, { numero_conta: numero });
            const conta = json.conta;
            renderAccountInfo(conta, role);
            return conta;
        }catch(e){
            document.getElementById(role + '_account_info').textContent = 'Conta não encontrada';
            document.getElementById(role + '_conta_id')?.remove();
            const body = document.querySelector('#op_' + role + ' .op_body'); if(body) body.style.display = 'none';
            // ensure last operation area hidden until an operation is performed
            document.getElementById('last_operation_card').style.display = 'none';
            return null;
        }
    }

    function renderAccountInfo(conta, role){
        const infoEl = document.getElementById(role + '_account_info');
        if(infoEl){
            infoEl.textContent = (conta.cliente?.nome || '—') + ' — ' + (conta.agencia?.nome || conta.agencia?.id || '—');
        }
        // fill hidden id
        try{ document.getElementById(role + '_conta_id').value = conta.id; }catch(e){}

        // populate account summary card
        const sum = document.getElementById(role + '_account_summary');
        const sumBody = document.getElementById(role + '_account_summary_body');
        if(sum && sumBody){
            const html = `
                <dl class="row mb-0">
                    <dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${conta.numero_conta || '—'}</dd>
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
            const sel = document.getElementById('deposit_moeda');
            if(sel){
                const moeda = conta.moeda;
                if(moeda){
                    // prefer to set existing option, otherwise insert one
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

        // show form body
        const body = document.querySelector('#op_' + role + ' .op_body'); if(body) body.style.display = 'block';

        // hide last operation area until user performs an operation
        document.getElementById('last_operation_card').style.display = 'none';
    }

    // wire verify buttons to our fetch function
    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; fetchAndRenderAccount(input.value.trim(), role); }));

    document.querySelectorAll('.conta-input').forEach(inp => inp.addEventListener('blur', function(){ const role = this.dataset.role; setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 250); }));

    // Submit
    document.getElementById('op_deposit').addEventListener('submit', async function(e){
        e.preventDefault();
        try{
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            const contaId = formData.get('conta_id') || (this.querySelector('.conta-input[data-role="deposit"]')?.dataset.contaId);
            if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger');
            const btn = this.querySelector('button[type="submit"]'); if(btn){ btn.disabled = true; var old = btn.innerHTML; btn.innerHTML='Processando...'; }
            const resp = await window.Transacoes.postJson('/api/contas/' + contaId + '/depositar', { valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao, depositante: data.depositante });
            setOpsAlert(resp.message || 'Depósito efetuado','success');
            if(resp.transacao){
                // render an invoice-like receipt using transaction details
                const t = resp.transacao || {};
                const details = document.getElementById('last_operation_details');
                // date/time: prefer server timestamps, fallback to now
                const rawDate = t.created_at || t.data || t.data_operacao || t.data_hora || t.timestamp || new Date().toISOString();
                let dateStr = rawDate; try{ const dt = new Date(rawDate); if(!isNaN(dt)) dateStr = dt.toLocaleString(); }catch(e){}
                // moeda
                const moeda = (t.moeda && (t.moeda.codigo || t.moeda.nome)) || (t.moeda_codigo) || (document.getElementById('deposit_moeda')?.selectedOptions[0]?.text) || '';
                // account number: try transaction.conta.numero_conta, transaction.numero_conta, fallback to known contaId or first dd in summary
                const accountNumber = (t.conta && (t.conta.numero_conta || t.conta.numero || t.conta.numeroConta)) || t.numero_conta || t.conta_numero || contaId || document.querySelector('#deposit_account_summary_body dd')?.textContent || (t.conta_id || '—');
                // titular name: prefer conta.cliente.nome, then cliente.nome, then t.titular
                const titular = (t.conta && t.conta.cliente && (t.conta.cliente.nome || t.conta.cliente.nome_completo)) || (t.cliente && (t.cliente.nome || t.cliente.nome_completo)) || t.titular || document.querySelector('#deposit_account_summary_body dd:nth-of-type(2)')?.textContent || '—';
                const depositante = t.depositante || data.depositante || document.getElementById('deposit_depositante')?.value || '—';
                const valorStr = (t.valor!==undefined ? Number(t.valor).toFixed(2) : (data.valor ? Number(data.valor).toFixed(2) : '—'));
                const invoiceHtml = `
                    <div class="invoice p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Recibo de Depósito</h4>
                            <small>#${t.id || '—'} — ${dateStr}</small>
                        </div>
                        <dl class="row">
                            <dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${accountNumber}</dd>
                            <dt class="col-sm-3">Titular</dt><dd class="col-sm-9">${titular}</dd>
                            <dt class="col-sm-3">Depositante</dt><dd class="col-sm-9">${depositante}</dd>
                            <dt class="col-sm-3">Valor</dt><dd class="col-sm-9">${valorStr} ${moeda}</dd>
                            <dt class="col-sm-3">Moeda</dt><dd class="col-sm-9">${moeda}</dd>
                            <dt class="col-sm-3">Descrição</dt><dd class="col-sm-9">${t.descricao || data.descricao || '—'}</dd>
                        </dl>
                        <div class="mt-3"><small class="text-muted">Este documento comprova que o depósito foi registrado no sistema.</small></div>
                    </div>
                `;
                if(details) details.innerHTML = invoiceHtml;
                document.getElementById('last_operation_card').style.display = 'block';
            }
            if(btn){ btn.disabled = false; btn.innerHTML = old; }
        }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); }
    });

    if(window.Transacoes && window.Transacoes.prefillFromQuery){
        window.Transacoes.prefillFromQuery({ numero_conta: { selector: '.conta-input[data-role="deposit"]', role: 'deposit', options: { findContaRoute: findRoute, infoIdPrefix: 'deposit_account_info' } } });
    }

    function setOpsAlert(msg, type='success'){
        const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'" role="alert">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 5000);
    }
});
</script>
@endpush
