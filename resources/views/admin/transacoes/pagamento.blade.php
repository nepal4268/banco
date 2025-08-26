@extends('layouts.app')

@section('title', 'Pagamento')
@section('page-title', 'Pagamento')

@section('content')
<div class="card">
    <div class="card-body">
        <h1 class="h4">Pagamento</h1>
        <p class="text-muted">Formulário para efetuar pagamentos.</p>

        <div id="ops_alert" aria-live="polite" class="mb-2"></div>

        <div id="pag_account_summary" class="card mb-3" style="display:none;" aria-hidden="true">
            <div class="card-body" id="pag_account_summary_body"></div>
        </div>

        <form id="op_pay" novalidate>
            <input type="hidden" name="conta_id" id="pay_conta_id" />
            <fieldset>
                <legend class="sr-only">Dados do pagamento</legend>
                <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="pay_numero_conta">Conta (número)</label>
                    <div class="input-group">
                        <input id="pay_numero_conta" name="numero_conta" class="form-control conta-input" data-role="pay" aria-describedby="pay_account_info" autocomplete="off" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="pay" aria-label="Verificar conta">🔎</button>
                        </div>
                    </div>
                    <small id="pay_account_info" class="form-text text-muted">&nbsp;</small>
                </div>
                <div class="form-group col-md-4">
                    <label for="pay_parceiro">Parceiro</label>
                    <input id="pay_parceiro" name="parceiro" class="form-control" />
                </div>
                <div class="form-group col-md-4">
                    <label for="pay_referencia">Referência</label>
                    <input id="pay_referencia" name="referencia" class="form-control" />
                </div>
            </div>
                <div class="op_body" style="display:none; width:100%;">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="pay_valor">Valor</label>
                        <input id="pay_valor" type="number" step="0.01" min="0.01" name="valor" class="form-control" required />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="pay_bi">BI do titular <span class="text-danger">*</span></label>
                        <input id="pay_bi" name="bi" class="form-control" placeholder="BI do titular" required />
                        <div class="invalid-feedback" data-field="bi">Informe o BI do titular.</div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="pay_moeda">Moeda</label>
                        <select name="moeda_id" id="pay_moeda" class="form-control" required aria-required="true"></select>
                        <div class="invalid-feedback" data-field="moeda_id">Selecione a moeda.</div>
                    </div>
                    <div class="form-group col-md-3 text-right align-self-end">
                        <button class="btn btn-danger" type="submit">Executar Pagamento</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card mt-3" id="last_operation_card" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detalhes da última Operação</h5>
                <button id="pag_export_btn" class="btn btn-sm btn-outline-secondary" type="button" aria-disabled="true">Exportar CSV</button>
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
    if(window.Transacoes && window.Transacoes.loadMoedasInto) window.Transacoes.loadMoedasInto(['pay_moeda']);

    async function fetchAndRenderAccount(numero, role){
        if(!numero) return;
        try{
            const json = await window.Transacoes.postJson(findRoute, { numero_conta: numero });
            const conta = json.conta;
            renderAccountInfo(conta, role);
            try{ const inputEl = document.querySelector('.conta-input[data-role="'+role+'"]'); if(inputEl){ inputEl.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) inputEl.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) inputEl.dataset.moedaCodigo = conta.moeda.codigo; } }catch(e){}
            return conta;
        }catch(e){
            document.getElementById(role + '_info')?.textContent = 'Conta não encontrada';
            const body = document.querySelector('#op_pay .op_body'); if(body) body.style.display='none';
            document.getElementById('last_operation_card').style.display = 'none';
            return null;
        }
    }

    function renderAccountInfo(conta, role){
        const sum = document.getElementById('pag_account_summary'); const sumBody = document.getElementById('pag_account_summary_body'); if(sum && sumBody){ sumBody.innerHTML = `<dl class="row mb-0"><dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${conta.numero_conta||'—'}</dd><dt class="col-sm-3">Saldo disponível</dt><dd class="col-sm-9">${(typeof conta.saldo !== 'undefined' ? Number(conta.saldo).toFixed(2) : '—')}</dd><dt class="col-sm-3">Titular</dt><dd class="col-sm-9">${conta.cliente?.nome||'—'}</dd><dt class="col-sm-3">Agência</dt><dd class="col-sm-9">${conta.agencia?.nome||conta.agencia?.id||'—'}</dd><dt class="col-sm-3">Moeda</dt><dd class="col-sm-9">${conta.moeda?.codigo||conta.moeda?.nome||'—'}</dd></dl>`; sum.style.display='block'; sum.setAttribute('aria-hidden','false'); }
        const body = document.querySelector('#op_pay .op_body'); if(body) body.style.display='block';
    }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; fetchAndRenderAccount(input.value.trim(), role); }));
    document.querySelectorAll('.conta-input').forEach(function(inp){ let t; inp.addEventListener('blur', function(){ const role = this.dataset.role; setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 250); }); inp.addEventListener('input', function(){ const role = this.dataset.role; clearTimeout(t); t = setTimeout(()=> fetchAndRenderAccount(this.value.trim(), role), 600); }); });

    document.getElementById('op_pay').addEventListener('submit', async function(e){ e.preventDefault(); try{ const formData = new FormData(this); const data = Object.fromEntries(formData.entries()); const contaId = formData.get('conta_id') || document.querySelector('.conta-input[data-role="pay"]')?.dataset.contaId; if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger'); // require BI client-side
        const bi = document.getElementById('pay_bi')?.value || '';
        if(!bi) return setOpsAlert('Informe o BI do titular antes de submeter', 'danger');
        // client-side saldo check
        const saldo = Number(document.querySelector('#pag_account_summary_body dd:nth-of-type(2)')?.textContent || 0);
        const valor = Number(data.valor || 0);
        if(valor <= 0) return setOpsAlert('Valor deve ser maior que zero', 'danger');
        if(!isNaN(saldo) && valor > saldo) return setOpsAlert('Valor não pode ser maior que o saldo disponível', 'danger');
        const btn = this.querySelector('button[type="submit"]'); if(btn){ btn.disabled = true; var old = btn.innerHTML; btn.innerHTML='Processando...'; }
        const resp = await window.Transacoes.postJson('/api/contas/' + contaId + '/pagar', { valor: data.valor, moeda_id: data.moeda_id, parceiro: data.parceiro, referencia: data.referencia, bi: bi });
        setOpsAlert(resp.message || 'Pagamento efetuado', 'success');
        if(resp.transacao && resp.transacao.id && window.Transacoes && window.Transacoes.renderTransacaoDetailsTo){
            window.Transacoes.renderTransacaoDetailsTo('last_operation_details', resp.transacao);
        }
        // clear form and hide UI sections
        this.reset();
        const body = document.querySelector('#op_pay .op_body'); if(body) body.style.display = 'none';
        const sum = document.getElementById('pag_account_summary'); if(sum) sum.style.display = 'none';
        if(btn){ btn.disabled = false; btn.innerHTML = old; }
    }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

    if(window.Transacoes && window.Transacoes.prefillFromQuery){ window.Transacoes.prefillFromQuery({ numero_conta: { selector: '.conta-input[data-role="pay"]', role: 'pay', options: { findContaRoute: findRoute, infoIdPrefix: 'pay_account_info' } } }); }

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'" role="alert">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 5000); }
});
</script>
@endpush
 