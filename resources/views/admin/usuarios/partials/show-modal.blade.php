<!-- Modal Visualizar Usuário -->
<div class="modal fade" id="showUsuarioModal" tabindex="-1" aria-labelledby="showUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showUsuarioModalLabel">
                    <i class="fas fa-user me-2"></i>Detalhes do Usuário
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nome Completo:</label>
                        <p id="show_nome" class="form-control-plaintext"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <p id="show_email" class="form-control-plaintext"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Número do BI:</label>
                        <p id="show_bi" class="form-control-plaintext"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Sexo:</label>
                        <p id="show_sexo" class="form-control-plaintext"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Telefone:</label>
                        <p id="show_telefone" class="form-control-plaintext"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Data de Nascimento:</label>
                        <p id="show_data_nascimento" class="form-control-plaintext"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Perfil:</label>
                        <p id="show_perfil" class="form-control-plaintext"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Agência:</label>
                        <p id="show_agencia" class="form-control-plaintext"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Endereço:</label>
                        <p id="show_endereco" class="form-control-plaintext"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Cidade:</label>
                        <p id="show_cidade" class="form-control-plaintext"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Província:</label>
                        <p id="show_provincia" class="form-control-plaintext"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status:</label>
                        <p id="show_ativo" class="form-control-plaintext"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Data de Criação:</label>
                        <p id="show_created_at" class="form-control-plaintext"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</div>