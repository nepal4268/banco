@extends('layouts.app')

@section('title', 'Câmbio')
@section('page-title', 'Câmbio')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Câmbio</h5>
        <p class="text-muted">Operações de câmbio (cotação, compra/venda de moeda).</p>

        <form id="op_cambio">
            <input type="hidden" id="cambio_conta_origem_id" name="conta_origem_id" />
            <input type="hidden" id="cambio_conta_destino_id" name="conta_destino_id" />
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Conta Origem (número)</label>
                    <div class="input-group">
                        <input name="numero_origem" class="form-control conta-input" data-role="cambio-origem" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="cambio-origem">🔎</button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="cambio_origem_info"></small>
                </div>
                <div class="form-group col-md-4">
                    <label>Conta Destino (número)</label>
                    <div class="input-group">
                        <input name="numero_destino" class="form-control conta-input" data-role="cambio-destino" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="cambio-destino">🔎</button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="cambio_destino_info"></small>
                </div>
                <div class="form-group col-md-4">
                    <label>Valor Origem</label>
                    <input type="number" step="0.01" min="0.01" name="valor_origem" class="form-control" />
                </div>
            </div>

            <div class="op_body" style="display:none;">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Moeda Origem</label>
                        <select id="cambio_moeda_origem" name="moeda_origem_id" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Moeda Destino</label>
                        <select id="cambio_moeda_destino" name="moeda_destino_id" class="form-control"></select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Cotação</label>
                        <input type="number" step="0.0001" name="cotacao" id="cambio_cotacao" class="form-control" />
                        <small class="form-text text-muted"><a href="#" id="btn_buscar_cotacao">Buscar cotação</a></small>
                    </div>
                    <div class="form-group col-md-3 text-right align-self-end">
                        <button class="btn btn-primary">Executar Câmbio</button>
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
    loadMoedasInto(['cambio_moeda_origem','cambio_moeda_destino']);

    async function verifyAccount(numero, role){ if(!numero) return { error: 'Informe número' }; try{ const json = await postJson('{{ route('transacoes.findConta') }}', { numero_conta: numero }); const conta = json.conta; const infoEl = document.getElementById('cambio_' + (role.includes('origem')? 'origem_info':'destino_info'));
            if(infoEl){ infoEl.textContent = (conta.cliente ? (conta.cliente.nome || '') : '') + ' — ' + (conta.agencia ? (conta.agencia.nome||conta.agencia.id) : ''); }
            const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input){ input.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) input.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) input.dataset.moedaCodigo = conta.moeda.codigo; }
            try{ if(role === 'cambio-origem') document.getElementById('cambio_conta_origem_id').value = conta.id; if(role === 'cambio-destino') document.getElementById('cambio_conta_destino_id').value = conta.id; }catch(e){}
            const opBody = document.querySelector('#op_cambio .op_body'); const ori = document.querySelector('.conta-input[data-role="cambio-origem"]'); const dst = document.querySelector('.conta-input[data-role="cambio-destino"]'); if(opBody){ if(ori && ori.dataset.contaId && dst && dst.dataset.contaId){ const oriMoeda = ori.dataset.moedaId; const dstMoeda = dst.dataset.moedaId; if(oriMoeda && dstMoeda && oriMoeda === dstMoeda){ const msg = 'Moedas iguais: câmbio exige moedas diferentes.'; if(infoEl) infoEl.textContent = msg; opBody.style.display = 'none'; } else { opBody.style.display = 'block'; if(oriMoeda){ const s = document.getElementById('cambio_moeda_origem'); if(s){ s.value = oriMoeda; } } if(dstMoeda){ const s2 = document.getElementById('cambio_moeda_destino'); if(s2){ s2.value = dstMoeda; } } } } else { opBody.style.display = 'none'; } }
            return { conta };
        }catch(e){ const infoEl = document.getElementById('cambio_' + (role.includes('origem')? 'origem_info':'destino_info')); if(infoEl) infoEl.textContent = 'Conta não encontrada'; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input) delete input.dataset.contaId; const opBody = document.querySelector('#op_cambio .op_body'); if(opBody) opBody.style.display='none'; return { error: e.message || 'Conta não encontrada' }; } }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; verifyAccount(input.value.trim(), role); }));
    document.querySelectorAll('.conta-input').forEach(inp => { inp.addEventListener('blur', function(e){ const role = this.dataset.role; verifyAccount(this.value.trim(), role); }); });

    document.getElementById('btn_buscar_cotacao').addEventListener('click', async function(e){ e.preventDefault(); try{ const moOrig = document.getElementById('cambio_moeda_origem').value; const moDst = document.getElementById('cambio_moeda_destino').value; if(!moOrig || !moDst) return alert('Selecione ambas as moedas'); const r = await fetch('/api/taxas-cambio/cotacao?moeda_origem='+moOrig+'&moeda_destino='+moDst, { headers:{'Accept':'application/json'}, credentials:'same-origin'}); if(!r.ok) return alert('Erro ao buscar cotação'); const j = await r.json(); const cot = j.cotacao || j.data?.cotacao || j.data?.valor || null; if(cot) document.getElementById('cambio_cotacao').value = cot; else alert('Cotação não disponível'); }catch(e){ console.warn(e); alert('Erro ao buscar cotação'); } });

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d){ const wrapper = document.querySelector('.card-body'); if(wrapper){ const div = document.createElement('div'); div.id='ops_alert'; wrapper.prepend(div); } } const dd = document.getElementById('ops_alert'); dd.innerHTML = '<div class="alert alert-'+type+'">'+msg+'</div>'; setTimeout(()=>{ dd.innerHTML=''; }, 4000); }

    async function renderTransacaoDetails(t){ if(!t) return; const container = document.getElementById('last_operation_details'); const card = document.getElementById('last_operation_card'); if(!container || !card) return; const dt = t.created_at || t.createdAt || null; let html = '<table class="table table-sm">'; html += '<tr><th>ID</th><td>'+ (t.id||'—') +'</td></tr>'; html += '<tr><th>Data / Hora</th><td>'+ (dt ? dt.replace('T',' ').replace('Z','') : '—') +'</td></tr>'; html += '<tr><th>Tipo</th><td>'+ ((t.tipoTransacao && t.tipoTransacao.nome) || (t.tipo_transacao && t.tipo_transacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Valor</th><td>'+ (t.valor !== undefined ? Number(t.valor).toFixed(2) : '—') +'</td></tr>'; html += '<tr><th>Moeda</th><td>'+ ((t.moeda && (t.moeda.codigo || t.moeda.nome)) || '—') +'</td></tr>'; html += '<tr><th>Status</th><td>'+ ((t.statusTransacao && t.statusTransacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Descrição</th><td>'+ (t.descricao || '—') +'</td></tr>'; html += '</table>'; container.innerHTML = html; card.style.display = 'block'; card.scrollIntoView({ behavior: 'smooth', block: 'center' }); }

    document.getElementById('op_cambio').addEventListener('submit', async function(e){ e.preventDefault(); try{ const fd = new FormData(this); const data = Object.fromEntries(fd.entries()); const contaOrig = fd.get('conta_origem_id') || document.querySelector('.conta-input[data-role="cambio-origem"]')?.dataset.contaId; const contaDst = fd.get('conta_destino_id') || document.querySelector('.conta-input[data-role="cambio-destino"]')?.dataset.contaId; if(!contaOrig || !contaDst) return setOpsAlert('Verifique contas antes de submeter','danger'); const moOrig = data.moeda_origem_id; const moDst = data.moeda_destino_id; if(!moOrig || !moDst || moOrig === moDst) return setOpsAlert('Selecione moedas diferentes','danger'); const payload = { conta_origem_id: contaOrig, conta_destino_id: contaDst, valor_origem: data.valor_origem, moeda_origem_id: moOrig, moeda_destino_id: moDst, cotacao: data.cotacao };
        const resp = await postJson('/api/transacoes/cambio', payload);
        setOpsAlert(resp.message || 'Câmbio efetuado', 'success'); if(resp.operacao_cambio && resp.operacao_cambio.id){ try{ const r2 = await fetch('/api/operacoes-cambio/' + resp.operacao_cambio.id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } }catch(e){} }
    }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

});

    // Prefill from query params
    try{
        const params = new URLSearchParams(window.location.search);
        const numOrig = params.get('numero_origem') || '';
        const numDst = params.get('numero_destino') || '';
        if(numOrig){ const i = document.querySelector('.conta-input[data-role="cambio-origem"]'); if(i){ i.value = numOrig; verifyAccount(numOrig, 'cambio-origem'); } }
        if(numDst){ const i2 = document.querySelector('.conta-input[data-role="cambio-destino"]'); if(i2){ i2.value = numDst; verifyAccount(numDst, 'cambio-destino'); } }
    }catch(e){}
</script>
@endpush
