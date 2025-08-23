<!-- Modal Editar Agência -->
<div class="modal fade" id="editAgenciaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Agência</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editAgenciaForm" action="#" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" id="edit_nome" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Endereço</label>
            <input type="text" name="endereco" id="edit_endereco" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" id="edit_telefone" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="edit_email" class="form-control">
          </div>
          <div class="form-check">
            <input type="hidden" name="ativa" value="0">
            <input type="checkbox" name="ativa" id="edit_ativa" class="form-check-input" value="1">
            <label class="form-check-label" for="edit_ativa">Ativa</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
