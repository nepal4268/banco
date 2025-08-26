<!-- Payment form partial -->
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
