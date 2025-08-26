export async function postJson(url, data){
    const r = await fetch(url, { method: 'POST', headers:{ 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content') }, credentials:'same-origin', body: JSON.stringify(data) });
    const txt = await r.text(); try{ const j = txt?JSON.parse(txt):null; if(!r.ok) throw new Error(j?.message || txt || 'Erro'); return j; }catch(e){ if(!r.ok) throw e; return null; }
}

export async function loadMoedas(){
    try{ const r = await fetch('/api/moedas', { headers:{'Accept':'application/json'}, credentials:'same-origin'}); if(!r.ok) return []; const j = await r.json(); return j.data || []; }catch(e){ console.warn('Erro carregando moedas', e); return []; }
}

export function debounce(fn, wait=600){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); }; }

function setOpsAlert(msg, type='success'){ const d = document.getElementById('ops_alert'); if(!d){ const wrapper = document.querySelector('.card-body'); if(wrapper){ const div = document.createElement('div'); div.id='ops_alert'; wrapper.prepend(div); } } const dd = document.getElementById('ops_alert'); if(dd) dd.innerHTML = '<div class="alert alert-'+type+'">'+msg+'</div>'; setTimeout(()=>{ if(dd) dd.innerHTML=''; }, 4000); }

export async function renderTransacaoDetailsTo(containerId, t){
    if(!t) return;
    const container = document.getElementById(containerId);
    const card = document.getElementById('last_operation_card');
    if(!container || !card) return;

    const dtRaw = t.created_at || t.createdAt || t.data || null;
    let dt = dtRaw || new Date().toISOString();
    try{ const tmp = new Date(dtRaw); if(!isNaN(tmp)) dt = tmp.toLocaleString(); }catch(e){}

    // Resolve common fields with fallbacks
    const tipoNome = (t.tipoTransacao && t.tipoTransacao.nome) || (t.tipo_transacao && t.tipo_transacao.nome) || (t.tipo) || 'Operação';
    const title = 'Recibo de ' + tipoNome;
    const id = t.id || '—';
    const descricao = t.descricao || t.observacao || '—';
    // Show absolute value (no leading sign) and format
    const valor = (t.valor !== undefined ? Math.abs(Number(t.valor)).toFixed(2) : '—');
    // Resolve moeda: prefer relation, then provided code, then try to read from visible account summary
    let moeda = (t.moeda && (t.moeda.codigo || t.moeda.nome)) || t.moeda_codigo || null;

    // account / titular heuristics
    const acctFromTrans = (t.conta && (t.conta.numero_conta || t.conta.numero)) || t.numero_conta || t.conta_numero || null;
    const acctFromDest = (t.contaDestino && (t.contaDestino.numero_conta || t.contaDestino.numero)) || null;
    // Try to extract account number and titular from a visible account summary card on the page using dt label match
    function findVisibleSummary(){
        const candidates = Array.from(document.querySelectorAll('[id$="_account_summary_body"]'));
        for(const el of candidates){
            // consider visible
            if(el.offsetParent === null) continue;
            return el;
        }
        return null;
    }

    const summaryEl = findVisibleSummary();
    let acctFromSumm = null, titularFromSumm = null, moedaFromSumm = null;
    if(summaryEl){
        const dts = summaryEl.querySelectorAll('dt');
        for(const dtEl of dts){
            const key = (dtEl.textContent || '').trim().toLowerCase();
            const dd = dtEl.nextElementSibling;
            if(!dd) continue;
            if(key.startsWith('conta')) acctFromSumm = dd.textContent.trim();
            if(key.startsWith('titular')) titularFromSumm = dd.textContent.trim();
            if(key.startsWith('moeda')) moedaFromSumm = dd.textContent.trim();
        }
    }

    const accountNumber = (acctFromTrans || acctFromDest || acctFromSumm || (t.conta_id || t.conta_origem_id || t.conta_destino_id) || '—')?.toString().trim();
    const titularFromTrans = (t.conta && t.conta.cliente && (t.conta.cliente.nome || t.conta.cliente.nome_completo)) || (t.cliente && (t.cliente.nome || t.cliente.nome_completo)) || null;
    const titular = (titularFromTrans || titularFromSumm || '—')?.toString().trim();

    // actor: depositante / parceiro / origem external label
    // If moeda not resolved yet, try from summary
    if(!moeda && moedaFromSumm) moeda = moedaFromSumm;
    if(!moeda) moeda = '—';

    let html = `
        <div class="invoice p-3">
            <div class="mb-3"><h4>${title}</h4></div>
            <dl class="row">
                <dt class="col-sm-3">ID</dt><dd class="col-sm-9">${id}</dd>
                <dt class="col-sm-3">Data / Hora</dt><dd class="col-sm-9">${dt}</dd>
                <dt class="col-sm-3">Conta</dt><dd class="col-sm-9">${accountNumber}</dd>
                <dt class="col-sm-3">Titular</dt><dd class="col-sm-9">${titular}</dd>
                <dt class="col-sm-3">Valor</dt><dd class="col-sm-9">${valor}</dd>
                <dt class="col-sm-3">Moeda</dt><dd class="col-sm-9">${moeda}</dd>
                ${ (t.conta && (typeof t.conta.saldo !== 'undefined')) ? (`<dt class="col-sm-3">Saldo disponível</dt><dd class="col-sm-9">${Number(t.conta.saldo).toFixed(2)}</dd>`) : '' }
                <dt class="col-sm-3">Descrição</dt><dd class="col-sm-9">${descricao}</dd>
            </dl>
            <div class="mt-3"><small class="text-muted">Este documento comprova que a operação foi registrada no sistema.</small></div>
        </div>
    `;

    container.innerHTML = html;
    card.style.display = 'block';
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

export function initContaLookupBehavior(){
    return {
        attachVerifyButtons(){
            document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){ const role = this.dataset.role; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(!input) return; verifyAccount(input.value.trim(), role); }));
        }
    }
}

// Utility: expose a small verifyAccount used by views (they can call verifyAccountGlobal)
export async function verifyAccountGlobal(numero, role, options={}){
    if(!numero) return { error: 'Informe número' };
    try{
        const json = await postJson(options.findContaRoute || '/transacoes/find-conta', { numero_conta: numero });
        const conta = json.conta;
        const infoEl = document.getElementById((options.infoIdPrefix||role + '_info'));
        if(infoEl){ infoEl.textContent = (conta.cliente ? (conta.cliente.nome || '') : '') + ' — ' + (conta.agencia ? (conta.agencia.nome||conta.agencia.id) : ''); }
        const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input){ input.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) input.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) input.dataset.moedaCodigo = conta.moeda.codigo; }
        return { conta };
    }catch(e){ const infoEl = document.getElementById((options.infoIdPrefix||role + '_info')); if(infoEl) infoEl.textContent = 'Conta não encontrada'; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input) delete input.dataset.contaId; return { error: e.message || 'Conta não encontrada' }; }
}

export function prefillFromQuery(mapping){ // mapping: { paramName: { selector, role, verifierRole } }
    try{
        const params = new URLSearchParams(window.location.search);
        Object.keys(mapping).forEach(k => {
            const val = params.get(k); if(!val) return; const cfg = mapping[k]; const el = document.querySelector(cfg.selector); if(!el) return; el.value = val; if(cfg.role) verifyAccountGlobal(val, cfg.role, cfg.options||{});
        });
    }catch(e){}
}

// Expose to global for inline invocation from blade views
window.Transacoes = {
    postJson,
    loadMoedas,
    debounce,
    verifyAccountGlobal,
    renderTransacaoDetailsTo,
    prefillFromQuery
};
