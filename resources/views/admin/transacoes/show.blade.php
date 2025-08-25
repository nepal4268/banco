@extends('layouts.app')

@section('title', 'Transação')
@section('page-title', 'Transação')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Operações em Conta</h5>
        <p class="text-muted">Execute operações como em um banco real. A moeda será automaticamente definida pela conta selecionada.</p>

        <div id="ops_alert"></div>

        <div class="btn-group mb-3" role="group">
            <button class="btn btn-sm btn-outline-success" data-op="deposit">Depósito</button>
            <button class="btn btn-sm btn-outline-warning" data-op="withdraw">Levantamento</button>
            <button class="btn btn-sm btn-outline-primary" data-op="transfer">Transferência</button>
            <button class="btn btn-sm btn-outline-danger" data-op="pay">Pagamento</button>
        </div>

        <div id="ops_forms">
                    <!-- Deposit form -->
                    <form id="op_deposit" class="op_form" style="display:none;">
                        <input type="hidden" name="conta_id" id="deposit_conta_id" />
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                        <label>Número da conta</label>
                                        <div class="input-group">
                                            <input name="numero_conta" class="form-control conta-input" value="{{ $transacao->contaDestino->numero_conta ?? '' }}" data-role="deposit" />
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-verify" type="button" data-role="deposit">🔎</button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted" id="deposit_account_info"></small>
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
                                        <select name="moeda_id" id="deposit_moeda" class="form-control"></select>
                                        <div class="invalid-feedback" data-field="moeda_id"></div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-9">
                                        <label>Descrição</label>
                                        <input name="descricao" class="form-control" />
                                    </div>
                                    <div class="form-group col-md-3 text-right align-self-end">
                                        <button class="btn btn-success">Executar Depósito</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Withdraw form -->
                    <form id="op_withdraw" class="op_form" style="display:none;">
                        <input type="hidden" name="conta_id" id="withdraw_conta_id" />
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Número da conta</label>
                                <div class="input-group">
                                    <input name="numero_conta" class="form-control conta-input" value="{{ $transacao->contaOrigem->numero_conta ?? '' }}" data-role="withdraw" />
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
                        </div>
                    </form>

                    <!-- Transfer form -->
                    <form id="op_transfer" class="op_form" style="display:none;">
                        <input type="hidden" name="conta_origem_id" id="transfer_conta_origem_id" />
                        <input type="hidden" name="conta_destino_id" id="transfer_conta_destino_id" />
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Conta Origem (número)</label>
                                <div class="input-group">
                                    <input name="numero_origem" class="form-control conta-input" value="{{ $transacao->contaOrigem->numero_conta ?? '' }}" data-role="transfer-origem" />
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
                                    <input name="numero_destino" class="form-control conta-input" value="{{ $transacao->contaDestino->numero_conta ?? '' }}" data-role="transfer-destino" />
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

                    <!-- Payment form -->
                    <form id="op_pay" class="op_form" style="display:none;">
                        <input type="hidden" name="conta_id" id="pay_conta_id" />
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Conta (número)</label>
                                <div class="input-group">
                                    <input name="numero_conta" class="form-control conta-input" value="{{ $transacao->contaOrigem->numero_conta ?? '' }}" data-role="pay" />
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
                        <div class="form-row">
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
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Última operação: será preenchida após executar uma operação -->
        <div class="card mt-3" id="last_operation_card" style="display:none;">
            <div class="card-header"><h5 class="card-title">Detalhes da Última Operação</h5></div>
            <div class="card-body" id="last_operation_details">
                <!-- preenchido via JS com dados da transação retornada pela API -->
            </div>
        </div>

        <!-- Exibir abaixo os detalhes originais da transação (histórico) -->
        <hr class="my-4" />
        <h5 class="mt-3">Transação #{{ $transacao->id }}</h5>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th>Data / Hora</th>
                        <td>{{ optional($transacao->created_at)->format('Y-m-d H:i:s') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td>{{ $transacao->tipoTransacao->nome ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Valor</th>
                        <td>{{ $transacao->valor !== null ? number_format($transacao->valor,2,',','.') : '—' }}</td>
                    </tr>
                    <tr>
                        <th>Moeda</th>
                        <td>{{ $transacao->moeda->codigo ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $transacao->statusTransacao->nome ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Descrição</th>
                        <td>{{ $transacao->descricao ?? '—' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Conta Origem</th>
                        <td>
                            @if($transacao->contaOrigem)
                                {{ $transacao->contaOrigem->numero_conta }}
                                <button class="btn btn-sm btn-link open-conta-modal" data-conta="{{ $transacao->contaOrigem->numero_conta }}">Abrir</button>
                            @else
                                Externa: {{ $transacao->conta_externa_origem ?? '—' }}
                            @endif
                        </td>
                    </tr>
                    <tr><th>Conta Destino</th>
                        <td>
                            @if($transacao->contaDestino)
                                {{ $transacao->contaDestino->numero_conta }}
                                <button class="btn btn-sm btn-link open-conta-modal" data-conta="{{ $transacao->contaDestino->numero_conta }}">Abrir</button>
                            @else
                                Externa: {{ $transacao->conta_externa_destino ?? '—' }}
                            @endif
                        </td>
                    </tr>
                    <tr><th>Referência</th><td>{{ $transacao->referencia_externa ?? '—' }}</td></tr>
                    <tr><th>Origem Externa</th><td>{{ $transacao->origem_externa ? 'Sim' : 'Não' }}</td></tr>
                    <tr><th>Destino Externa</th><td>{{ $transacao->destino_externa ? 'Sim' : 'Não' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // initial moeda coming from the transaction's related contas (if present)
    const initialMoedaId = @json($transacao->contaDestino->moeda->id ?? $transacao->contaOrigem->moeda->id ?? null);
    function setOpsAlert(msg, type='success'){
        const d = document.getElementById('ops_alert'); d.innerHTML = '<div class="alert alert-'+type+'">'+msg+'</div>';
        setTimeout(()=>{ d.innerHTML=''; }, 4000);
    }

    document.querySelectorAll('#ops_forms .op_form').forEach(f => f.querySelectorAll('input,select').forEach(i => i.addEventListener('input', () => {})));

    document.querySelectorAll('.btn-group [data-op]').forEach(b => b.addEventListener('click', function(){
        const op = this.dataset.op;
        document.querySelectorAll('.op_form').forEach(f => f.style.display='none');
        if(op === 'deposit') document.getElementById('op_deposit').style.display='block';
        if(op === 'withdraw') document.getElementById('op_withdraw').style.display='block';
        if(op === 'transfer') document.getElementById('op_transfer').style.display='block';
        if(op === 'pay') document.getElementById('op_pay').style.display='block';
    }));

    async function postJson(url, data){
        const r = await fetch(url, { 
            method: 'POST', 
            headers: { 
                'Content-Type':'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }, 
            credentials: 'same-origin',
            body: JSON.stringify(data) 
        });
        // read as text first to avoid JSON.parse errors for HTML/error pages
        const text = await r.text();
        let json = null;
        try{
            json = text ? JSON.parse(text) : null;
        }catch(e){
            // not JSON
            if(!r.ok){
                if(r.status === 401 || r.status === 403) throw new Error('Não autorizado');
                // return server text if available
                throw new Error(text || 'Erro no servidor');
            }
            // ok response but not JSON -> return raw text
            return { data: text };
        }
        if(!r.ok) throw new Error(json.error || json.message || 'Erro');
        return json;
    }

    // Load moedas into selects
    // Load moedas and cache them; selects will be restored from cache when needed
    let allMoedas = [];
    async function loadMoedasInto(selectIds){
        try{
            const r = await fetch('/api/moedas', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' }); if(!r.ok) return; const json = await r.json(); const data = json.data || [];
            allMoedas = data;
            selectIds.forEach(id => {
                const sel = document.getElementById(id); if(!sel) return; sel.innerHTML = '';
                data.forEach(m => { const opt = document.createElement('option'); opt.value = m.id; opt.textContent = (m.codigo?m.codigo+' - ':'') + (m.nome||''); sel.appendChild(opt); });
                // if initialMoedaId is available, show only that moeda and disable
                if(initialMoedaId){
                    const chosen = data.find(x => x.id == initialMoedaId);
                    if(chosen){ sel.innerHTML = ''; const opt = document.createElement('option'); opt.value = chosen.id; opt.textContent = (chosen.codigo?chosen.codigo+' - ':'') + (chosen.nome||''); sel.appendChild(opt); sel.value = chosen.id; }
                }
            });
        }catch(e){ console.warn('Erro carregando moedas', e); }
    }
    loadMoedasInto(['deposit_moeda','withdraw_moeda','transfer_moeda','pay_moeda']);

    // debounce helper
    function debounce(fn, wait){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); }; }

    // verify account helper (fills info and sets moeda)
    async function verifyAccount(numero, role){
        if(!numero) return { error: 'Informe número' };
        try{
            const json = await postJson('{{ route('transacoes.findConta') }}', { numero_conta: numero });
            const conta = json.conta;
            // show holder/name/agencia
            const infoEl = document.getElementById(role + '_account_info');
            if(infoEl){ infoEl.textContent = (conta.cliente ? (conta.cliente.nome || '') : '') + ' — ' + (conta.agencia ? (conta.agencia.nome||conta.agencia.id) : ''); }
            // set moeda select for the specific role
                if(conta && conta.moeda){
                    // Helper to set single-option select
                    const setSingle = (selId, moeda) => {
                        const s = document.getElementById(selId); if(!s) return;
                        s.innerHTML = '';
                        const opt = document.createElement('option'); opt.value = moeda.id; opt.textContent = (moeda.codigo?moeda.codigo+' - ':'') + (moeda.nome||''); s.appendChild(opt);
                        s.value = moeda.id;
                    };
                    if(role === 'deposit'){
                        setSingle('deposit_moeda', conta.moeda);
                    } else if(role === 'withdraw'){
                        setSingle('withdraw_moeda', conta.moeda);
                    } else if(role === 'pay'){
                        setSingle('pay_moeda', conta.moeda);
                    } else if(role.startsWith('transfer')){
                        // when verifying transfer accounts, prefer destination currency when both present
                        const ori = document.querySelector('.conta-input[data-role="transfer-origem"]');
                        const dst = document.querySelector('.conta-input[data-role="transfer-destino"]');
                        // if verifying destination, always set to destination moeda
                        if(role === 'transfer-destino'){
                            setSingle('transfer_moeda', conta.moeda);
                        } else if(role === 'transfer-origem'){
                            // only set if destination not yet verified
                            if(!(dst && dst.dataset.contaId)) setSingle('transfer_moeda', conta.moeda);
                        }
                    }
                }
            // store conta id on the input to mark verified and also set hidden form fields
            const input = document.querySelector('.conta-input[data-role="'+role+'"]');
                    if(input) {
                        input.dataset.contaId = conta.id;
                        if(conta.moeda && conta.moeda.id) input.dataset.moedaId = conta.moeda.id;
                    }
            try{
                if(role === 'deposit') { document.getElementById('deposit_conta_id').value = conta.id; }
                else if(role === 'withdraw') { document.getElementById('withdraw_conta_id').value = conta.id; }
                else if(role === 'pay') { document.getElementById('pay_conta_id').value = conta.id; }
                else if(role === 'transfer-origem') { document.getElementById('transfer_conta_origem_id').value = conta.id; }
                else if(role === 'transfer-destino') { document.getElementById('transfer_conta_destino_id').value = conta.id; }
            }catch(e){}
            // show operation body only when verified (transfer needs both)
            if(role.startsWith('transfer')){
                const ori = document.querySelector('.conta-input[data-role="transfer-origem"]');
                const dst = document.querySelector('.conta-input[data-role="transfer-destino"]');
                const opBody = document.querySelector('#op_transfer .op_body');
                if(opBody){
                    if(ori && ori.dataset.contaId && dst && dst.dataset.contaId){
                        // both accounts verified: ensure same moeda
                        const oriMoeda = ori.dataset.moedaId;
                        const dstMoeda = dst.dataset.moedaId;
                        const oriInfo = document.getElementById('transfer_origem_info');
                        const dstInfo = document.getElementById('transfer_destino_info');
                        if(oriMoeda && dstMoeda && oriMoeda !== dstMoeda){
                            // show inline error and block operation
                            const msg = 'Moedas diferentes: origem (' + (oriMoeda) + ') ≠ destino (' + (dstMoeda) + '). Transferência não permitida.';
                            if(oriInfo){ oriInfo.textContent = msg; oriInfo.classList.add('text-danger'); }
                            if(dstInfo){ dstInfo.textContent = msg; dstInfo.classList.add('text-danger'); }
                            opBody.style.display = 'none';
                        } else {
                            // clear any previous error styling
                            if(oriInfo){ oriInfo.classList.remove('text-danger'); oriInfo.textContent = (ori.dataset.contaId ? oriInfo.textContent : oriInfo.textContent); }
                            if(dstInfo){ dstInfo.classList.remove('text-danger'); dstInfo.textContent = (dst.dataset.contaId ? dstInfo.textContent : dstInfo.textContent); }
                            // set transfer moeda to the common currency (if known)
                            const moedaId = oriMoeda || dstMoeda;
                            if(moedaId){
                                const moedaObj = allMoedas.find(x => x.id == moedaId || x.id == String(moedaId));
                                if(moedaObj){
                                    const s = document.getElementById('transfer_moeda');
                                    if(s){ s.innerHTML = ''; const opt = document.createElement('option'); opt.value = moedaObj.id; opt.textContent = (moedaObj.codigo?moedaObj.codigo+' - ':'') + (moedaObj.nome||''); s.appendChild(opt); s.value = moedaObj.id; }
                                }
                            }
                            opBody.style.display = 'block';
                        }
                    } else {
                        opBody.style.display = 'none';
                    }
                }
            } else {
                const map = { deposit: 'op_deposit', withdraw: 'op_withdraw', pay: 'op_pay' };
                const form = document.getElementById(map[role]); if(form){ const body = form.querySelector('.op_body'); if(body) body.style.display='block'; }
            }
            return { conta };
        }catch(e){
            const infoEl = document.getElementById(role + '_account_info'); if(infoEl) infoEl.textContent = 'Conta não encontrada';
            // remove any stored conta id
            const input = document.querySelector('.conta-input[data-role="'+role+'"]'); if(input) delete input.dataset.contaId;
            try{
                if(role === 'deposit') { document.getElementById('deposit_conta_id').value = ''; }
                else if(role === 'withdraw') { document.getElementById('withdraw_conta_id').value = ''; }
                else if(role === 'pay') { document.getElementById('pay_conta_id').value = ''; }
                else if(role === 'transfer-origem') { document.getElementById('transfer_conta_origem_id').value = ''; }
                else if(role === 'transfer-destino') { document.getElementById('transfer_conta_destino_id').value = ''; }
            }catch(e){}
            // hide related op body
            if(role.startsWith('transfer')){
                const opBody = document.querySelector('#op_transfer .op_body'); if(opBody) opBody.style.display='none';
            } else {
                const map = { deposit: 'op_deposit', withdraw: 'op_withdraw', pay: 'op_pay' };
                const form = document.getElementById(map[role]); if(form){ const body = form.querySelector('.op_body'); if(body) body.style.display='none'; }
            }
            // re-enable selects and restore full options from allMoedas if available
            if(!initialMoedaId && allMoedas && allMoedas.length){
                ['deposit_moeda','withdraw_moeda','transfer_moeda','pay_moeda'].forEach(id => {
                    const s = document.getElementById(id); if(!s) return; s.disabled = false; s.innerHTML = ''; allMoedas.forEach(m => { const opt = document.createElement('option'); opt.value = m.id; opt.textContent = (m.codigo?m.codigo+' - ':'') + (m.nome||''); s.appendChild(opt); });
                });
            }
            return { error: e.message || 'Conta não encontrada' };
        }
    }

    // attach verify buttons
    document.querySelectorAll('.btn-verify').forEach(b => b.addEventListener('click', function(){
        const role = this.dataset.role;
        const input = document.querySelector('.conta-input[data-role="'+role+'"]');
        if(!input) return;
        verifyAccount(input.value.trim(), role);
    }));

    // wire external account checkboxes for transfer
    const oriExternaCb = document.getElementById('transfer_origem_externa');
    const dstExternaCb = document.getElementById('transfer_destino_externa');
    const oriExternaNum = document.getElementById('transfer_origem_externa_num');
    const dstExternaNum = document.getElementById('transfer_destino_externa_num');
    if(oriExternaCb){ oriExternaCb.addEventListener('change', function(){ if(this.checked){ oriExternaNum.style.display='block'; } else { oriExternaNum.style.display='none'; } }); }
    if(dstExternaCb){ dstExternaCb.addEventListener('change', function(){ if(this.checked){ dstExternaNum.style.display='block'; } else { dstExternaNum.style.display='none'; } }); }

    // attach blur with debounce to inputs
    document.querySelectorAll('.conta-input').forEach(inp => {
        inp.addEventListener('blur', debounce(function(e){ const role = this.dataset.role; verifyAccount(this.value.trim(), role); }, 600));
    });

    // form helpers
    function clearValidation(form){ form.querySelectorAll('.is-invalid').forEach(i => i.classList.remove('is-invalid')); form.querySelectorAll('.invalid-feedback').forEach(d => d.textContent=''); }
    function applyValidationErrors(form, errors){ for(const k in errors){ const field = form.querySelector('[name="'+k+'"]'); if(field){ field.classList.add('is-invalid'); const fb = form.querySelector('.invalid-feedback[data-field="'+k+'"]'); if(fb) fb.textContent = errors[k]; } } }

    // Deposit
    document.getElementById('op_deposit').addEventListener('submit', async function(e){ e.preventDefault();
        try{
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            console.log('DEPOSIT moeda_id:', data.moeda_id, 'form:', this.querySelector('[name="moeda_id"]').value, 'disabled:', this.querySelector('[name="moeda_id"]').disabled);
            // prefer conta_id hidden field
            const contaId = formData.get('conta_id') || (this.querySelector('.conta-input[data-role="deposit"]')?.dataset.contaId);
            if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger');
            // disable submit
            let oldHtml = null;
            const btn = this.querySelector('button[type="submit"], button.btn-success'); if(btn){ btn.disabled = true; oldHtml = btn.innerHTML; btn.innerHTML = 'Processando...'; }
            // set moeda from conta and lock selects
            
            const resp = await postJson('/api/contas/' + contaId + '/depositar', { valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao, referencia_externa: data.referencia_externa });
            setOpsAlert(resp.message || 'Depósito efetuado','success');
            // refresh page part: update saldo element if present
            if(resp.conta) document.getElementById('mi_saldo') && (document.getElementById('mi_saldo').textContent = Number(resp.conta.saldo).toFixed(2));
            // re-verify account to refresh lastTransactions and other UI pieces
            try{ const inEl = this.querySelector('.conta-input[data-role="deposit"]'); if(inEl) verifyAccount(inEl.value.trim(), 'deposit'); }catch(e){}
            // show full transacao details using API
            if(resp.transacao && resp.transacao.id){
                const r2 = await fetch('/api/transacoes/' + resp.transacao.id);
                if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); }
            }
            if(btn){ btn.disabled = false; btn.innerHTML = oldHtml; }
        }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); }
    });

    // Withdraw
    document.getElementById('op_withdraw').addEventListener('submit', async function(e){ e.preventDefault();
        try{
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            console.log('WITHDRAW moeda_id:', data.moeda_id, 'form:', this.querySelector('[name="moeda_id"]').value, 'disabled:', this.querySelector('[name="moeda_id"]').disabled);
            const contaId = formData.get('conta_id') || (this.querySelector('.conta-input[data-role="withdraw"]')?.dataset.contaId);
            if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger');
            let oldHtml = null;
            const btn = this.querySelector('button[type="submit"], button.btn-warning'); if(btn){ btn.disabled = true; oldHtml = btn.innerHTML; btn.innerHTML = 'Processando...'; }
            const resp = await postJson('/api/contas/' + contaId + '/levantar', { valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao, referencia_externa: data.referencia_externa });
            setOpsAlert(resp.message || 'Levantamento efetuado','success');
            if(resp.conta) document.getElementById('mi_saldo') && (document.getElementById('mi_saldo').textContent = Number(resp.conta.saldo).toFixed(2));
            try{ const inEl = this.querySelector('.conta-input[data-role="withdraw"]'); if(inEl) verifyAccount(inEl.value.trim(), 'withdraw'); }catch(e){}
            if(resp.transacao && resp.transacao.id){ const r2 = await fetch('/api/transacoes/' + resp.transacao.id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } }
            if(btn){ btn.disabled = false; btn.innerHTML = oldHtml; }
        }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); }
    });

    // Transfer
    document.getElementById('op_transfer').addEventListener('submit', async function(e){ e.preventDefault();
        try{
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            console.log('TRANSFER moeda_id:', data.moeda_id, 'form:', this.querySelector('[name="moeda_id"]').value, 'disabled:', this.querySelector('[name="moeda_id"]').disabled);
            const origemInput = this.querySelector('.conta-input[data-role="transfer-origem"]');
            const destinoInput = this.querySelector('.conta-input[data-role="transfer-destino"]');
            const contaOrigemId = formData.get('conta_origem_id') || (origemInput?.dataset.contaId);
            const contaDestinoId = formData.get('conta_destino_id') || (destinoInput?.dataset.contaId);
            // ensure both accounts verified
            if(!contaOrigemId || !contaDestinoId) return setOpsAlert('Verifique origem e destino antes de submeter', 'danger');
            // determine whether this is an external transfer (either checkbox checked) -> allow if backend supports external
            const origemExterna = document.getElementById('transfer_origem_externa')?.checked;
            const destinoExterna = document.getElementById('transfer_destino_externa')?.checked;
            const origemMoeda = origemInput?.dataset.moedaId;
            const destinoMoeda = destinoInput?.dataset.moedaId;
            console.log('TRANSFER moeda_id:', data.moeda_id, 'origemMoeda:', origemMoeda, 'destinoMoeda:', destinoMoeda, 'origemExterna:', origemExterna, 'destinoExterna:', destinoExterna);
            // if both accounts are internal (not externa), enforce same moeda
            if(!origemExterna && !destinoExterna && origemMoeda && destinoMoeda && origemMoeda !== destinoMoeda){
                return setOpsAlert('Não é possível transferir entre contas com moedas diferentes.', 'danger');
            }
            const contaId = contaOrigemId;
            let oldHtml = null;
            const btn = this.querySelector('button[type="submit"], button.btn-primary'); if(btn){ btn.disabled = true; oldHtml = btn.innerHTML; btn.innerHTML = 'Processando...'; }
            // route to external transfer endpoint when appropriate
            let endpoint = '/api/transacoes/transferir';
            const payload = { conta_origem_id: contaOrigemId, conta_destino_id: contaDestinoId, valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao };
            if(origemExterna || destinoExterna){
                endpoint = '/api/transacoes/transferir-externo';
                payload.origem_externa = origemExterna ? true : false;
                payload.destino_externa = destinoExterna ? true : false;
                if(oriExternaNum && oriExternaNum.value) payload.conta_externa_origem = oriExternaNum.value;
                if(dstExternaNum && dstExternaNum.value) payload.conta_externa_destino = dstExternaNum.value;
            }
            const resp = await postJson(endpoint, payload);
            setOpsAlert(resp.message || 'Transferência efetuada','success');
            if(resp.conta) document.getElementById('mi_saldo') && (document.getElementById('mi_saldo').textContent = Number(resp.conta.saldo).toFixed(2));
            try{ const inOri = this.querySelector('.conta-input[data-role="transfer-origem"]'); if(inOri) verifyAccount(inOri.value.trim(), 'transfer-origem'); const inDst = this.querySelector('.conta-input[data-role="transfer-destino"]'); if(inDst) verifyAccount(inDst.value.trim(), 'transfer-destino'); }catch(e){}
            if(resp.transacao && resp.transacao.id){ const r2 = await fetch('/api/transacoes/' + resp.transacao.id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } }
            if(btn){ btn.disabled = false; btn.innerHTML = oldHtml; }
        }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); }
    });

    // Pay
    document.getElementById('op_pay').addEventListener('submit', async function(e){ e.preventDefault();
        try{
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            console.log('PAY moeda_id:', data.moeda_id, 'form:', this.querySelector('[name="moeda_id"]').value, 'disabled:', this.querySelector('[name="moeda_id"]').disabled);
            const contaId = formData.get('conta_id') || (this.querySelector('.conta-input[data-role="pay"]')?.dataset.contaId);
            if(!contaId) return setOpsAlert('Verifique a conta antes de submeter', 'danger');
            let oldHtml = null;
            const btn = this.querySelector('button[type="submit"], button.btn-danger'); if(btn){ btn.disabled = true; oldHtml = btn.innerHTML; btn.innerHTML = 'Processando...'; }
            const resp = await postJson('/api/contas/' + contaId + '/pagar', { parceiro: data.parceiro, referencia: data.referencia, valor: data.valor, moeda_id: data.moeda_id, descricao: data.descricao });
            setOpsAlert(resp.message || 'Pagamento efetuado','success');
            if(resp.conta) document.getElementById('mi_saldo') && (document.getElementById('mi_saldo').textContent = Number(resp.conta.saldo).toFixed(2));
            try{ const inEl = this.querySelector('.conta-input[data-role="pay"]'); if(inEl) verifyAccount(inEl.value.trim(), 'pay'); }catch(e){}
            // pagamento may return pagamento object; try to find transacao id
            if(resp.transacao && resp.transacao.id){ const r2 = await fetch('/api/transacoes/' + resp.transacao.id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } }
            else if(resp.pagamento && resp.pagamento.transacao_id){ const r2 = await fetch('/api/transacoes/' + resp.pagamento.transacao_id); if(r2.ok){ const json = await r2.json(); renderTransacaoDetails(json); } }
            if(btn){ btn.disabled = false; btn.innerHTML = oldHtml; }
        }catch(err){ setOpsAlert(err.message || 'Erro', 'danger'); }
    });
    document.querySelectorAll('.open-conta-modal').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const numero = btn.dataset.conta;
            if(!numero) return;
            // open the global modal and populate
            $('#contaOperationsModal').modal('show');
            document.getElementById('modal_numero_conta').value = numero;
            // trigger load
            document.getElementById('modal_load_conta').click();
        });
    });

    // Render transacao (full) into the last_operation_details card
    async function renderTransacaoDetails(t){
        if(!t) return;
        const container = document.getElementById('last_operation_details');
        const card = document.getElementById('last_operation_card');
        // guarantee we have full object (API returns with relations)
        const dt = t.created_at || t.createdAt || null;
        let html = '<table class="table table-sm">';
        html += '<tr><th>ID</th><td>'+ (t.id||'—') +'</td></tr>';
        html += '<tr><th>Data / Hora</th><td>'+ (dt ? dt.replace('T',' ').replace('Z','') : '—') +'</td></tr>';
        html += '<tr><th>Tipo</th><td>'+ ((t.tipoTransacao && t.tipoTransacao.nome) || (t.tipo_transacao && t.tipo_transacao.nome) || '—') +'</td></tr>';
        html += '<tr><th>Valor</th><td>'+ (t.valor !== undefined ? Number(t.valor).toFixed(2) : '—') +'</td></tr>';
        html += '<tr><th>Moeda</th><td>'+ ((t.moeda && (t.moeda.codigo || t.moeda.nome)) || '—') +'</td></tr>';
        html += '<tr><th>Status</th><td>'+ ((t.statusTransacao && t.statusTransacao.nome) || '—') +'</td></tr>';
        html += '<tr><th>Descrição</th><td>'+ (t.descricao || '—') +'</td></tr>';
        html += '<tr><th>Conta Origem</th><td>' + ((t.contaOrigem && t.contaOrigem.numero_conta) || (t.conta_externa_origem) || '—') + '</td></tr>';
        html += '<tr><th>Conta Destino</th><td>' + ((t.contaDestino && t.contaDestino.numero_conta) || (t.conta_externa_destino) || '—') + '</td></tr>';
        html += '<tr><th>Referência</th><td>' + (t.referencia_externa || '—') + '</td></tr>';
        html += '</table>';
        container.innerHTML = html;
        card.style.display = 'block';
        // scroll into view to show user the result
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endpush
