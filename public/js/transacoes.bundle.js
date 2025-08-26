;(function(window){
    'use strict';

    async function postJson(url, data){
        const r = await fetch(url, { method: 'POST', headers:{ 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, credentials:'same-origin', body: JSON.stringify(data) });
        const txt = await r.text(); try{ const j = txt?JSON.parse(txt):null; if(!r.ok) throw new Error(j?.message || txt || 'Erro'); return j; }catch(e){ if(!r.ok) throw e; return null; }
    }

    async function loadMoedas(){
        try{ const r = await fetch('/api/moedas', { headers:{'Accept':'application/json'}, credentials:'same-origin'}); if(!r.ok) return []; const j = await r.json(); return j.data || []; }catch(e){ console.warn('Erro carregando moedas', e); return []; }
    }

    function debounce(fn, wait){ wait = wait || 600; let t; return function(){ const ctx = this; const args = arguments; clearTimeout(t); t = setTimeout(function(){ fn.apply(ctx, args); }, wait); }; }

    function setOpsAlert(msg, type){ if(type===undefined) type='success'; const d = document.getElementById('ops_alert'); if(!d){ const wrapper = document.querySelector('.card-body'); if(wrapper){ const div = document.createElement('div'); div.id='ops_alert'; wrapper.prepend(div); } } const dd = document.getElementById('ops_alert'); if(dd) dd.innerHTML = '<div class="alert alert-'+type+'">'+msg+'</div>'; setTimeout(()=>{ if(dd) dd.innerHTML=''; }, 4000); }

    async function renderTransacaoDetailsTo(containerId, t){ if(!t) return; const container = document.getElementById(containerId); const card = document.getElementById('last_operation_card'); if(!container || !card) return; const dt = t.created_at || t.createdAt || null; let html = '<table class="table table-sm">'; html += '<tr><th>ID</th><td>'+ (t.id||'—') +'</td></tr>'; html += '<tr><th>Data / Hora</th><td>'+ (dt ? dt.replace('T',' ').replace('Z','') : '—') +'</td></tr>'; html += '<tr><th>Tipo</th><td>'+ ((t.tipoTransacao && t.tipoTransacao.nome) || (t.tipo_transacao && t.tipo_transacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Valor</th><td>'+ (t.valor !== undefined ? Number(t.valor).toFixed(2) : '—') +'</td></tr>'; html += '<tr><th>Moeda</th><td>'+ ((t.moeda && (t.moeda.codigo || t.moeda.nome)) || '—') +'</td></tr>'; html += '<tr><th>Status</th><td>'+ ((t.statusTransacao && t.statusTransacao.nome) || '—') +'</td></tr>'; html += '<tr><th>Descrição</th><td>'+ (t.descricao || '—') +'</td></tr>'; html += '<tr><th>Conta Origem</th><td>' + ((t.contaOrigem && t.contaOrigem.numero_conta) || (t.conta_externa_origem) || '—') + '</td></tr>'; html += '<tr><th>Conta Destino</th><td>' + ((t.contaDestino && t.contaDestino.numero_conta) || (t.conta_externa_destino) || '—') + '</td></tr>'; html += '<tr><th>Referência</th><td>' + (t.referencia_externa || '—') + '</td></tr>'; html += '</table>'; container.innerHTML = html; card.style.display = 'block'; try{ card.scrollIntoView({ behavior: 'smooth', block: 'center' }); }catch(e){}
    }

    async function verifyAccountGlobal(numero, role, options){
        if(!numero) return { error: 'Informe número' };
        try{
            const json = await postJson(options?.findContaRoute || '/transacoes/find-conta', { numero_conta: numero });
            const conta = json.conta;
            const infoEl = document.getElementById((options?.infoIdPrefix|| role + '_info'));
            if(infoEl){ infoEl.textContent = (conta.cliente ? (conta.cliente.nome || '') : '') + ' — ' + (conta.agencia ? (conta.agencia.nome||conta.agencia.id) : ''); }
            const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input){ input.dataset.contaId = conta.id; if(conta.moeda && conta.moeda.id) input.dataset.moedaId = conta.moeda.id; if(conta.moeda && conta.moeda.codigo) input.dataset.moedaCodigo = conta.moeda.codigo; }
            return { conta };
        }catch(e){ const infoEl = document.getElementById((options?.infoIdPrefix|| role + '_info')); if(infoEl) infoEl.textContent = 'Conta não encontrada'; const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input) delete input.dataset.contaId; return { error: e.message || 'Conta não encontrada' }; }
    }

    function prefillFromQuery(mapping){ try{ const params = new URLSearchParams(window.location.search); Object.keys(mapping).forEach(k => { const val = params.get(k); if(!val) return; const cfg = mapping[k]; const el = document.querySelector(cfg.selector); if(!el) return; el.value = val; if(cfg.role) verifyAccountGlobal(val, cfg.role, cfg.options||{}); }); }catch(e){}
    }

    // expose
    window.Transacoes = window.Transacoes || {};
    window.Transacoes.postJson = postJson;
    window.Transacoes.loadMoedas = loadMoedas;
    window.Transacoes.debounce = debounce;
    window.Transacoes.verifyAccountGlobal = verifyAccountGlobal;
    window.Transacoes.renderTransacaoDetailsTo = renderTransacaoDetailsTo;
    window.Transacoes.prefillFromQuery = prefillFromQuery;

})(window);
