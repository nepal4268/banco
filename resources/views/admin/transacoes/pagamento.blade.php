@extends('layouts.app')

@section('title', 'Pagamento')
@section('page-title', 'Pagamento')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Pagamento</h5>
        <p class="text-muted">Formulário para efetuar pagamentos.</p>

        <form id="op_pay" class="op_form" style="">
            <input type="hidden" name="conta_id" id="pay_conta_id" />
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Conta (número)</label>
                    <div class="input-group">
                        <input name="numero_conta" class="form-control conta-input" data-role="pay" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="pay">🔎</button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="pay_account_info"></small>
                </div>
                <div class="form-group col-md-4">
                    <label>Parceiro</label>
                    <input name="parceiro" class="form-control" />
                </div>
                <div class="form-group col-md-4">
                    <label>Referência</label>
                    <input name="referencia" class="form-control" />
                </div>
            </div>
            <div class="op_body" style="display:none; width:100%;">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Valor</label>
                        <input type="number" step="0.01" min="0.01" name="valor" class="form-control" />
                    </div>
                    <div class="form-group col-md-3">
                        <label>Moeda</label>
                        <select name="moeda_id" id="pay_moeda" class="form-control"></select>
                        <div class="invalid-feedback" data-field="moeda_id"></div>
                    </div>
                    <div class="form-group col-md-3 text-right align-self-end">
                        <button class="btn btn-danger">Executar Pagamento</button>
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
        const r = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}, credentials:'same-origin', body: JSON.stringify(data)});
        const txt = await r.text(); try{ const j = txt?JSON.parse(txt):null; if(!r.ok) throw new Error(j?.message || txt || 'Erro'); return j; }catch(e){ if(!r.ok) throw e; return null; }
    }

    let allMoedas = [];
    async function loadMoedasInto(ids){
        try{ const r = await fetch('/api/moedas', { headers:{'Accept':'application/json'}, credentials:'same-origin'}); if(!r.ok) return; const json = await r.json(); const data = json.data || []; allMoedas = data; ids.forEach(id => { const sel = document.getElementById(id); if(!sel) return; sel.innerHTML = ''; data.forEach(m => { const opt = document.createElement('option'); opt.value = m.id; opt.textContent = (m.codigo?m.codigo+' - ':'') + (m.nome||''); sel.appendChild(opt); }); }); }catch(e){ console.warn('Erro carregando moedas', e); }
    }
    loadMoedasInto(['pay_moeda']);

    async function verifyAccount(numero, role){ if(!numero) return { error: 'Informe número' }; try{ const json = await postJson('{{ route('transacoes.findConta') }}', { numero_conta: numero }); const conta = json.conta; const infoEl = document.getElementById('pay_account_info'); if(infoEl){ infoEl.textContent = (conta.cliente ? (conta.cliente.nome || '') : '') + ' — ' + (conta.agencia ? (conta.agencia.nome||conta.agencia.id) : ''); }
            const input = document.querySelector('.conta-input[data-role="pay"]'); if(input){ input.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) input.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) input.dataset.moedaCodigo = conta.moeda.codigo; }
            try{ document.getElementById('pay_conta_id').value = conta.id; }catch(e){}
            const opBody = document.querySelector('#op_pay .op_body'); if(opBody){ opBody.style.display = (input && input.dataset.contaId) ? 'block' : 'none'; }
            return { conta };
        }catch(e){ const infoEl = document.getElementById('pay_account_info'); if(infoEl) infoEl.textContent = 'Conta não encontrada'; const input = document.querySelector('.conta-input[data-role="pay"]'); if(input) delete input.dataset.contaId; const opBody = document.querySelector('#op_pay .op_body'); if(opBody) opBody.style.display='none'; return { error: e.message || 'Conta não encontrada' }; } }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; verifyAccount(input.value.trim(), role); }));
    document.querySelectorAll('.conta-input').forEach(inp => { inp.addEventListener('blur', function(e){ const role = this.dataset.role; verifyAccount(this.value.trim(), role); }); });

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d){ const wrapper = document.querySelector('.card-body'); if(wrapper){ const div = document.createElement('div'); div.id='ops_alert'; wrapper.prepend(div); } } const dd = document.getElementById('ops_alert'); dd.innerHTML = '<div class="alert alert-'+type+'">'+msg+'</div>'; setTimeout(()=>{ dd.innerHTML=''; }, 4000); }

    async function renderTransacaoDetails(t){ if(!t) return; const container = document.getElementById('last_operation_details'); const card = document.getElementById('last_operation_card'); if(!container || !card) return; const dt = t.created_at || t.createdAt || null; let html = '<table class="table table-sm">'; html += '<tr><th>ID</th><td>'+ (t.id||'—') +'</td></tr>'; html += '<tr><th>Data / Hora</th><td>'+ (dt ? dt.replace('T',' ').replace('Z','') : '—') +'</td></tr>'; html += '<tr><th>Tipo</th><td>'+ ((t.tipoTransacao && t.tipoTransacao.nome) || (t.tipo_transacao && t.tipo_transacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Valor</th><td>'+ (t.valor !== undefined ? Number(t.valor).toFixed(2) : '—') +'</td></tr>'; html += '<tr><th>Moeda</th><td>'+ ((t.moeda && (t.moeda.codigo || t.moeda.nome)) || '—') +'</td></tr>'; html += '<tr><th>Status</th><td>'+ ((t.statusTransacao && t.statusTransacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Descrição</th><td>'+ (t.descricao || '—') +'</td></tr>'; html += '</table>'; container.innerHTML = html; card.style.display = 'block'; card.scrollIntoView({ behavior: 'smooth', block: 'center' }); }

    document.getElementById('op_pay').addEventListener('submit', async function(e){ e.preventDefault(); try{ const formData = new FormData(this); const data = Object.fromEntries(formData.entries()); const contaId = formData.get('conta_id') || document.querySelector('.conta-input[data-role="pay"]')?.dataset.contaId; if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger'); const payload = { valor: data.valor, moeda_id: data.moeda_id, parceiro: data.parceiro, referencia: data.referencia };
        const resp = await postJson('/api/contas/' + contaId + '/pagar', payload);
        setOpsAlert(resp.message || 'Pagamento efetuado', 'success'); if(resp.transacao && resp.transacao.id){ try{ const r2 = await fetch('/api/transacoes/' + resp.transacao.id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } }catch(e){}
    }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

});

    // Prefill from query params
    try{ const params = new URLSearchParams(window.location.search); const numero = params.get('numero_conta') || ''; if(numero){ const input = document.querySelector('.conta-input[data-role="pay"]'); if(input){ input.value = numero; verifyAccount(numero, 'pay'); } } }catch(e){}
</script>
@endpush
