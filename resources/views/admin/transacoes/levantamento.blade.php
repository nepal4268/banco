@extends('layouts.app')

@section('title', 'Levantamento')
@section('page-title', 'Levantamento')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Levantamento</h5>
        <p class="text-muted">Formulário para efetuar levantamentos em contas.</p>

        <div id="ops_alert"></div>

        <form id="op_withdraw" class="op_form" style="">
            <input type="hidden" name="conta_id" id="withdraw_conta_id" />
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Número da conta</label>
                    <div class="input-group">
                        <input name="numero_conta" class="form-control conta-input" data-role="withdraw" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="withdraw">🔎</button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="withdraw_account_info"></small>
                </div>
            </div>
            <div class="op_body" style="display:none; width:100%;">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Valor</label>
                        <input type="number" step="0.01" min="0.01" name="valor" class="form-control" />
                    </div>
                    <div class="form-group col-md-3">
                        <label>Moeda</label>
                        <select name="moeda_id" id="withdraw_moeda" class="form-control"></select>
                        <div class="invalid-feedback" data-field="moeda_id"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-9">
                        <label>Descrição</label>
                        <input name="descricao" class="form-control" />
                    </div>
                    <div class="form-group col-md-3 text-right align-self-end">
                        <button class="btn btn-warning">Executar Levantamento</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card mt-3" id="last_operation_card" style="display:none;">
            <div class="card-header"><h5 class="card-title">Detalhes da Última Operação</h5></div>
            <div class="card-body" id="last_operation_details"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    async function postJson(url, data){
        const r = await fetch(url, { 
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            credentials: 'same-origin',
            body: JSON.stringify(data)
        });
        const text = await r.text(); let json = null;
        try{ json = text ? JSON.parse(text) : null; }catch(e){
            if(!r.ok){ if(r.status===401||r.status===403) throw new Error('Não autorizado'); throw new Error(text||'Erro no servidor'); }
            return { data: text };
        }
        if(!r.ok) throw new Error(json.error || json.message || 'Erro');
        return json;
    }

    let allMoedas = [];
    async function loadMoedasInto(ids){
        try{
            const r = await fetch('/api/moedas', { headers:{'Accept':'application/json'}, credentials:'same-origin' });
            if(!r.ok) return;
            const json = await r.json(); const data = json.data || [];
            allMoedas = data;
            ids.forEach(id => {
                const sel = document.getElementById(id); if(!sel) return; sel.innerHTML = '';
                data.forEach(m => { const opt = document.createElement('option'); opt.value = m.id; opt.textContent = (m.codigo?m.codigo+' - ':'') + (m.nome||''); sel.appendChild(opt); });
            });
        }catch(e){ console.warn('Erro carregando moedas', e); }
    }
    loadMoedasInto(['withdraw_moeda']);

    function debounce(fn, wait){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); }; }

    async function verifyAccount(numero, role){
        if(!numero) return { error: 'Informe número' };
        try{
            const json = await postJson('{{ route('transacoes.findConta') }}', { numero_conta: numero });
            const conta = json.conta;
            const infoEl = document.getElementById(role + '_account_info');
            if(infoEl){ infoEl.textContent = (conta.cliente ? (conta.cliente.nome || '') : '') + ' — ' + (conta.agencia ? (conta.agencia.nome||conta.agencia.id) : ''); }

            if(conta && conta.moeda){ const s = document.getElementById('withdraw_moeda'); if(s){ s.innerHTML = ''; const opt = document.createElement('option'); opt.value = conta.moeda.id; opt.textContent = (conta.moeda.codigo?conta.moeda.codigo+' - ':'') + (conta.moeda.nome||''); s.appendChild(opt); s.value = conta.moeda.id; } }

            const input = document.querySelector('.conta-input[data-role="withdraw"]'); if(input) input.dataset.contaId = conta.id;
            try{ document.getElementById('withdraw_conta_id').value = conta.id; }catch(e){}
            const body = document.querySelector('#op_withdraw .op_body'); if(body) body.style.display = 'block';
            return { conta };
        }catch(e){
            const infoEl = document.getElementById(role + '_account_info'); if(infoEl) infoEl.textContent = 'Conta não encontrada';
            const input = document.querySelector('.conta-input[data-role="withdraw"]'); if(input) delete input.dataset.contaId;
            try{ document.getElementById('withdraw_conta_id').value = ''; }catch(e){}
            const body = document.querySelector('#op_withdraw .op_body'); if(body) body.style.display = 'none';
            return { error: e.message || 'Conta não encontrada' };
        }
    }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; verifyAccount(input.value.trim(), role); }));
    document.querySelectorAll('.conta-input').forEach(inp => { inp.addEventListener('blur', debounce(function(e){ const role = this.dataset.role; verifyAccount(this.value.trim(), role); }, 600)); });

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 4000); }

    // Prefill from query string
    try{ const params = new URLSearchParams(window.location.search); const numero = params.get('numero_conta') || params.get('numero') || ''; if(numero){ const input = document.querySelector('.conta-input[data-role="withdraw"]'); if(input){ input.value = numero; verifyAccount(numero, 'withdraw'); } } }catch(e){}

    async function renderTransacaoDetails(t){ if(!t) return; const container = document.getElementById('last_operation_details'); const card = document.getElementById('last_operation_card'); if(!container || !card) return; const dt = t.created_at || t.createdAt || null; let html = '<table class="table table-sm">'; html += '<tr><th>ID</th><td>'+ (t.id||'—') +'</td></tr>'; html += '<tr><th>Data / Hora</th><td>'+ (dt ? dt.replace('T',' ').replace('Z','') : '—') +'</td></tr>'; html += '<tr><th>Tipo</th><td>'+ ((t.tipoTransacao && t.tipoTransacao.nome) || (t.tipo_transacao && t.tipo_transacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Valor</th><td>'+ (t.valor !== undefined ? Number(t.valor).toFixed(2) : '—') +'</td></tr>'; html += '<tr><th>Moeda</th><td>'+ ((t.moeda && (t.moeda.codigo || t.moeda.nome)) || '—') +'</td></tr>'; html += '<tr><th>Status</th><td>'+ ((t.statusTransacao && t.statusTransacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Descrição</th><td>'+ (t.descricao || '—') +'</td></tr>'; html += '<tr><th>Conta Origem</th><td>' + ((t.contaOrigem && t.contaOrigem.numero_conta) || (t.conta_externa_origem) || '—') + '</td></tr>'; html += '<tr><th>Conta Destino</th><td>' + ((t.contaDestino && t.contaDestino.numero_conta) || (t.conta_externa_destino) || '—') + '</td></tr>'; html += '<tr><th>Referência</th><td>' + (t.referencia_externa || '—') + '</td></tr>'; html += '</table>'; container.innerHTML = html; card.style.display = 'block'; card.scrollIntoView({ behavior: 'smooth', block: 'center' }); }

    document.getElementById('op_withdraw').addEventListener('submit', async function(e){ e.preventDefault(); try{ const formData = new FormData(this); const data = Object.fromEntries(formData.entries()); const contaId = formData.get('conta_id') || (this.querySelector('.conta-input[data-role="withdraw"]')?.dataset.contaId); if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger'); const btn = this.querySelector('button[type="submit"], button.btn-warning'); if(btn){ btn.disabled = true; var old = btn.innerHTML; btn.innerHTML='Processando...'; } const resp = await postJson('/api/contas/' + contaId + '/levantar', { valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao, referencia_externa: data.referencia_externa }); setOpsAlert(resp.message || 'Levantamento efetuado','success'); if(resp.transacao && resp.transacao.id){ const r2 = await fetch('/api/transacoes/' + resp.transacao.id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } } if(btn){ btn.disabled = false; btn.innerHTML = old; } }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

});
</script>
@endpush
