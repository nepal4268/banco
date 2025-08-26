<!-- Deposit form partial (reused by deposito page) -->
<form id="op_deposit" class="op_form" style="">
    <input type="hidden" name="conta_id" id="deposit_conta_id" />
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Número da conta</label>
            <div class="input-group">
                <input name="numero_conta" class="form-control conta-input" data-role="deposit" />
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
</form>
