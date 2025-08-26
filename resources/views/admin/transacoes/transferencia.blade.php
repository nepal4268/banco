@extends('layouts.app')

@section('title', 'Transferência')
@section('page-title', 'Transferência')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Transferência</h5>
        <p class="text-muted">Formulário para efetuar transferências entre contas.</p>

        <div id="ops_alert"></div>

        <form id="op_transfer" class="op_form" style="">
            <input type="hidden" name="conta_origem_id" id="transfer_conta_origem_id" />
            <input type="hidden" name="conta_destino_id" id="transfer_conta_destino_id" />
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Conta Origem (número)</label>
                    <div class="input-group">
                        <input name="numero_origem" class="form-control conta-input" data-role="transfer-origem" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="transfer-origem">🔎</button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="transfer_origem_info"></small>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" value="1" id="transfer_origem_externa">
                        <label class="form-check-label small" for="transfer_origem_externa">Origem externa</label>
                    </div>
                    <input type="text" id="transfer_origem_externa_num" class="form-control form-control-sm mt-2" placeholder="Conta externa (IBAN/RC)" style="display:none;" />
                </div>
                <div class="form-group col-md-4">
                    <label>Conta Destino (número)</label>
                    <div class="input-group">
                        <input name="numero_destino" class="form-control conta-input" data-role="transfer-destino" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-verify" type="button" data-role="transfer-destino">🔎</button>
                        </div>
                    </div>
                    <small class="form-text text-muted" id="transfer_destino_info"></small>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" value="1" id="transfer_destino_externa">
                        <label class="form-check-label small" for="transfer_destino_externa">Destino externo</label>
                    </div>
                    <input type="text" id="transfer_destino_externa_num" class="form-control form-control-sm mt-2" placeholder="Conta externa (IBAN/RC)" style="display:none;" />
                </div>
                <div class="form-group col-md-4">
                    <label>Valor</label>
                    <input type="number" step="0.01" min="0.01" name="valor" class="form-control" />
                </div>
            </div>
            <div class="op_body" style="display:none; width:100%;">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Moeda</label>
                        <select name="moeda_id" id="transfer_moeda" class="form-control"></select>
                        <div class="invalid-feedback" data-field="moeda_id"></div>
                    </div>
                    <div class="form-group col-md-6 text-right align-self-end">
                        <button class="btn btn-primary">Executar Transferência</button>
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
        try{ json = text ? JSON.parse(text) : null; }catch(e){ if(!r.ok){ if(r.status===401||r.status===403) throw new Error('Não autorizado'); throw new Error(text||'Erro no servidor'); } return { data: text }; }
        if(!r.ok) throw new Error(json.error || json.message || 'Erro'); return json;
    }

    let allMoedas = [];
    async function loadMoedasInto(ids){
        try{
            const r = await fetch('/api/moedas', { headers:{'Accept':'application/json'}, credentials:'same-origin' });
            if(!r.ok) return; const json = await r.json(); const data = json.data || []; allMoedas = data;
            ids.forEach(id => { const sel = document.getElementById(id); if(!sel) return; sel.innerHTML = ''; data.forEach(m => { const opt = document.createElement('option'); opt.value = m.id; opt.textContent = (m.codigo?m.codigo+' - ':'') + (m.nome||''); sel.appendChild(opt); }); });
        }catch(e){ console.warn('Erro carregando moedas', e); }
    }
    loadMoedasInto(['transfer_moeda']);

    function debounce(fn, wait){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); }; }

    async function verifyAccount(numero, role){ if(!numero) return { error: 'Informe número' }; try{ const json = await postJson('{{ route('transacoes.findConta') }}', { numero_conta: numero }); const conta = json.conta; const infoEl = document.getElementById(role + '_info'); if(infoEl){ infoEl.textContent = (conta.cliente ? (conta.cliente.nome || '') : '') + ' — ' + (conta.agencia ? (conta.agencia.nome||conta.agencia.id) : ''); }

            const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input){ input.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) input.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) input.dataset.moedaCodigo = conta.moeda.codigo; }
            try{ if(role === 'transfer-origem') document.getElementById('transfer_conta_origem_id').value = conta.id; if(role === 'transfer-destino') document.getElementById('transfer_conta_destino_id').value = conta.id; }catch(e){}

            const ori = document.querySelector('.conta-input[data-role="transfer-origem"]'); const dst = document.querySelector('.conta-input[data-role="transfer-destino"]'); const opBody = document.querySelector('#op_transfer .op_body'); if(opBody){ if(ori && ori.dataset.contaId && dst && dst.dataset.contaId){ const oriMoeda = ori.dataset.moedaId; const dstMoeda = dst.dataset.moedaId; const oriInfo = document.getElementById('transfer_origem_info'); const dstInfo = document.getElementById('transfer_destino_info'); if(oriMoeda && dstMoeda && oriMoeda !== dstMoeda){ const msg = 'Moedas diferentes: origem (' + (oriMoeda) + ') ≠ destino (' + (dstMoeda) + '). Transferência não permitida.'; if(oriInfo){ oriInfo.textContent = msg; oriInfo.classList.add('text-danger'); } if(dstInfo){ dstInfo.textContent = msg; dstInfo.classList.add('text-danger'); } opBody.style.display = 'none'; } else { if(oriInfo){ oriInfo.classList.remove('text-danger'); } if(dstInfo){ dstInfo.classList.remove('text-danger'); } const moedaId = oriMoeda || dstMoeda; if(moedaId){ const moedaObj = allMoedas.find(x => x.id == moedaId || x.id == String(moedaId)); if(moedaObj){ const s = document.getElementById('transfer_moeda'); if(s){ s.innerHTML = ''; const opt = document.createElement('option'); opt.value = moedaObj.id; opt.textContent = (moedaObj.codigo?moedaObj.codigo+' - ':'') + (moedaObj.nome||''); s.appendChild(opt); s.value = moedaObj.id; } } } opBody.style.display = 'block'; } } else { opBody.style.display = 'none'; } }

            return { conta };
        }catch(e){ const infoEl = document.getElementById(role + '_info'); if(infoEl) infoEl.textContent = 'Conta não encontrada'; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input) delete input.dataset.contaId; try{ if(role === 'transfer-origem') document.getElementById('transfer_conta_origem_id').value=''; if(role === 'transfer-destino') document.getElementById('transfer_conta_destino_id').value=''; }catch(e){} const opBody = document.querySelector('#op_transfer .op_body'); if(opBody) opBody.style.display='none'; return { error: e.message || 'Conta não encontrada' }; } }

    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; verifyAccount(input.value.trim(), role); }));
    document.querySelectorAll('.conta-input').forEach(inp => { inp.addEventListener('blur', debounce(function(e){ const role = this.dataset.role; verifyAccount(this.value.trim(), role); }, 600)); });

    const oriExternaCb = document.getElementById('transfer_origem_externa'); const dstExternaCb = document.getElementById('transfer_destino_externa'); const oriExternaNum = document.getElementById('transfer_origem_externa_num'); const dstExternaNum = document.getElementById('transfer_destino_externa_num'); if(oriExternaCb){ oriExternaCb.addEventListener('change', function(){ if(this.checked){ oriExternaNum.style.display='block'; } else { oriExternaNum.style.display='none'; } }); } if(dstExternaCb){ dstExternaCb.addEventListener('change', function(){ if(this.checked){ dstExternaNum.style.display='block'; } else { dstExternaNum.style.display='none'; } }); }

    function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d) return; d.innerHTML = '<div class="alert alert-'+type+'">'+msg+'</div>'; setTimeout(()=>{ d.innerHTML=''; }, 4000); }

    async function renderTransacaoDetails(t){ if(!t) return; const container = document.getElementById('last_operation_details'); const card = document.getElementById('last_operation_card'); if(!container || !card) return; const dt = t.created_at || t.createdAt || null; let html = '<table class="table table-sm">'; html += '<tr><th>ID</th><td>'+ (t.id||'—') +'</td></tr>'; html += '<tr><th>Data / Hora</th><td>'+ (dt ? dt.replace('T',' ').replace('Z','') : '—') +'</td></tr>'; html += '<tr><th>Tipo</th><td>'+ ((t.tipoTransacao && t.tipoTransacao.nome) || (t.tipo_transacao && t.tipo_transacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Valor</th><td>'+ (t.valor !== undefined ? Number(t.valor).toFixed(2) : '—') +'</td></tr>'; html += '<tr><th>Moeda</th><td>'+ ((t.moeda && (t.moeda.codigo || t.moeda.nome)) || '—') +'</td></tr>'; html += '<tr><th>Status</th><td>'+ ((t.statusTransacao && t.statusTransacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Descrição</th><td>'+ (t.descricao || '—') +'</td></tr>'; html += '<tr><th>Conta Origem</th><td>' + ((t.contaOrigem && t.contaOrigem.numero_conta) || (t.conta_externa_origem) || '—') + '</td></tr>'; html += '<tr><th>Conta Destino</th><td>' + ((t.contaDestino && t.contaDestino.numero_conta) || (t.conta_externa_destino) || '—') + '</td></tr>'; html += '<tr><th>Referência</th><td>' + (t.referencia_externa || '—') + '</td></tr>'; html += '</table>'; container.innerHTML = html; card.style.display = 'block'; card.scrollIntoView({ behavior: 'smooth', block: 'center' }); }

    document.getElementById('op_transfer').addEventListener('submit', async function(e){ e.preventDefault(); try{ const formData = new FormData(this); const data = Object.fromEntries(formData.entries()); const origemInput = this.querySelector('.conta-input[data-role="transfer-origem"]'); const destinoInput = this.querySelector('.conta-input[data-role="transfer-destino"]'); const contaOrigemId = formData.get('conta_origem_id') || (origemInput?.dataset.contaId); const contaDestinoId = formData.get('conta_destino_id') || (destinoInput?.dataset.contaId); if(!contaOrigemId || !contaDestinoId) return setOpsAlert('Verifique origem e destino antes de submeter', 'danger'); const origemExterna = document.getElementById('transfer_origem_externa')?.checked; const destinoExterna = document.getElementById('transfer_destino_externa')?.checked; const origemMoeda = origemInput?.dataset.moedaId; const destinoMoeda = destinoInput?.dataset.moedaId; if(!origemExterna && !destinoExterna && origemMoeda && destinoMoeda && origemMoeda !== destinoMoeda){ return setOpsAlert('Não é possível transferir entre contas com moedas diferentes.', 'danger'); } const btn = this.querySelector('button[type="submit"], button.btn-primary'); if(btn){ btn.disabled = true; var old = btn.innerHTML; btn.innerHTML='Processando...'; } let endpoint = '/api/transacoes/transferir'; const payload = { conta_origem_id: contaOrigemId, conta_destino_id: contaDestinoId, valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao }; if(origemExterna || destinoExterna){ endpoint = '/api/transacoes/transferir-externo'; payload.origem_externa = origemExterna ? true : false; payload.destino_externa = destinoExterna ? true : false; if(oriExternaNum && oriExternaNum.value) payload.conta_externa_origem = oriExternaNum.value; if(dstExternaNum && dstExternaNum.value) payload.conta_externa_destino = dstExternaNum.value; }

        const resp = await postJson(endpoint, payload);
        setOpsAlert(resp.message || 'Transferência efetuada','success');
        if(resp.transacao && resp.transacao.id){ const r2 = await fetch('/api/transacoes/' + resp.transacao.id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } }
        if(btn){ btn.disabled = false; btn.innerHTML = old; }
    }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); } });

});

    // Prefill from query params
    try{
        const params = new URLSearchParams(window.location.search);
        const numOrig = params.get('numero_origem') || '';
        const numDst = params.get('numero_destino') || '';
        if(numOrig){ const i = document.querySelector('.conta-input[data-role="transfer-origem"]'); if(i){ i.value = numOrig; verifyAccount(numOrig, 'transfer-origem'); } }
        if(numDst){ const i2 = document.querySelector('.conta-input[data-role="transfer-destino"]'); if(i2){ i2.value = numDst; verifyAccount(numDst, 'transfer-destino'); } }
    }catch(e){}
</script>
@endpush
