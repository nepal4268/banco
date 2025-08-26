<!-- Transfer form partial -->
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
