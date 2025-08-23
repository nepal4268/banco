<!-- Modal Editar Usuário -->
<div class="modal fade" id="editUsuarioModal" tabindex="-1" aria-labelledby="editUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editUsuarioModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Editar Usuário
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUsuarioForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nome" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="edit_nome" name="nome" required>
                            <div class="invalid-feedback" id="error_edit_nome"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback" id="error_edit_email"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_senha" class="form-label">Nova Senha (deixe em branco para manter)</label>
                            <input type="password" class="form-control" id="edit_senha" name="senha">
                            <div class="invalid-feedback" id="error_edit_senha"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_senha_confirmation" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="edit_senha_confirmation" name="senha_confirmation">
                            <div class="invalid-feedback" id="error_edit_senha_confirmation"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_bi" class="form-label">Número do BI *</label>
                            <input type="text" class="form-control" id="edit_bi" name="bi" required>
                            <div class="invalid-feedback" id="error_edit_bi"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_sexo" class="form-label">Sexo *</label>
                            <select class="form-select" id="edit_sexo" name="sexo" required>
                                <option value="">Selecione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>
                            <div class="invalid-feedback" id="error_edit_sexo"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_telefone" class="form-label">Telefone *</label>
                            <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                            <div class="invalid-feedback" id="error_edit_telefone"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_data_nascimento" class="form-label">Data de Nascimento *</label>
                            <input type="date" class="form-control" id="edit_data_nascimento" name="data_nascimento" required>
                            <div class="invalid-feedback" id="error_edit_data_nascimento"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_perfil_id" class="form-label">Perfil *</label>
                            <select class="form-select" id="edit_perfil_id" name="perfil_id" required>
                                <option value="">Selecione um perfil...</option>
                                @foreach($perfis as $perfil)
                                    <option value="{{ $perfil->id }}">{{ $perfil->nome }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error_edit_perfil_id"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_agencia_id" class="form-label">Agência</label>
                            <select class="form-select" id="edit_agencia_id" name="agencia_id">
                                <option value="">Selecione uma agência...</option>
                                @foreach($agencias as $agencia)
                                    <option value="{{ $agencia->id }}">{{ $agencia->nome }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error_edit_agencia_id"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="edit_endereco" name="endereco">
                            <div class="invalid-feedback" id="error_edit_endereco"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="edit_cidade" name="cidade">
                            <div class="invalid-feedback" id="error_edit_cidade"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_provincia" class="form-label">Província</label>
                            <input type="text" class="form-control" id="edit_provincia" name="provincia">
                            <div class="invalid-feedback" id="error_edit_provincia"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_status_usuario" class="form-label">Status</label>
                            <select class="form-select" id="edit_status_usuario" name="status_usuario">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                            <div class="invalid-feedback" id="error_edit_status_usuario"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>