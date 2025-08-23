@extends('layouts.admin')

@section('title', 'Gestão de Usuários')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>Gestão de Usuários
                    </h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUsuarioModal">
                        <i class="fas fa-plus me-1"></i>Novo Usuário
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filtroNome" placeholder="Filtrar por nome...">
                        </div>
                        <div class="col-md-3">
                            <input type="email" class="form-control" id="filtroEmail" placeholder="Filtrar por email...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filtroPerfil">
                                <option value="">Todos os perfis</option>
                                @foreach($perfis as $perfil)
                                    <option value="{{ $perfil->id }}">{{ $perfil->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        </div>
                    </div>

                    <!-- Tabela -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="usuariosTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Agência</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr data-user-id="{{ $usuario->id }}" data-perfil-id="{{ $usuario->perfil_id ?? '' }}">
                                    <td class="col-id">{{ $usuario->id }}</td>
                                    <td class="col-nome">{{ $usuario->nome }}</td>
                                    <td class="col-email">{{ $usuario->email }}</td>
                                    <td class="col-perfil">
                                        <span class="badge bg-info">{{ $usuario->perfil->nome ?? 'N/A' }}</span>
                                    </td>
                                    <td class="col-agencia">{{ $usuario->agencia->nome ?? 'N/A' }}</td>
                                    <td class="col-status">
                                        @if(($usuario->status_usuario ?? 'inativo') === 'ativo')
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="col-acoes">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" onclick="visualizarUsuario({{ $usuario->id }})" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editarUsuario({{ $usuario->id }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="excluirUsuario({{ $usuario->id }})" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="d-flex justify-content-center">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Usuário -->
@include('admin.usuarios.partials.create-modal')

<!-- Modal Editar Usuário -->
@include('admin.usuarios.partials.edit-modal')

<!-- Modal Visualizar Usuário -->
@include('admin.usuarios.partials.show-modal')

<!-- Modal Confirmar Exclusão -->
@include('admin.usuarios.partials.delete-modal')

@endsection

@push('scripts')
<script>
function limparFiltros() {
    document.getElementById('filtroNome').value = '';
    document.getElementById('filtroEmail').value = '';
    document.getElementById('filtroPerfil').value = '';
    filtrarUsuarios();
}

function filtrarUsuarios() {
    const nome = document.getElementById('filtroNome').value.toLowerCase();
    const email = document.getElementById('filtroEmail').value.toLowerCase();
    const perfil = document.getElementById('filtroPerfil').value;
    
    const tbody = document.querySelector('#usuariosTable tbody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach(row => {
        const nomeCell = row.cells[1].textContent.toLowerCase();
        const emailCell = row.cells[2].textContent.toLowerCase();
    const perfilId = row.getAttribute('data-perfil-id') || '';

    const matchNome = nome === '' || nomeCell.includes(nome);
    const matchEmail = email === '' || emailCell.includes(email);
    const matchPerfil = perfil === '' || perfilId === perfil;
        
        row.style.display = matchNome && matchEmail && matchPerfil ? '' : 'none';
    });
}

function clearFormErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
}

function applyFormErrors(form, errors, prefix = '') {
    clearFormErrors(form);
    Object.keys(errors).forEach(field => {
        const inputName = prefix ? `${prefix}_${field}` : field;
        // try by id 'edit_field' or plain name
        const elById = form.querySelector(`#${inputName}`);
        const elByName = form.querySelector(`[name="${field}"]`);

        const target = elById || elByName;
        if (target) {
            target.classList.add('is-invalid');
            const feedback = form.querySelector(`#error_${inputName}`) || (target.parentElement ? target.parentElement.querySelector('.invalid-feedback') : null);
            if (feedback) feedback.textContent = errors[field][0];
        }
    });
}

function visualizarUsuario(id) {
    fetch(`/admin/usuarios/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('show_nome').textContent = data.nome;
        document.getElementById('show_email').textContent = data.email;
        document.getElementById('show_bi').textContent = data.bi;
        document.getElementById('show_sexo').textContent = data.sexo;
        document.getElementById('show_telefone').textContent = Array.isArray(data.telefone) ? data.telefone.join(', ') : data.telefone;
        document.getElementById('show_data_nascimento').textContent = data.data_nascimento ? new Date(data.data_nascimento).toLocaleDateString('pt-BR') : '';
        document.getElementById('show_perfil').textContent = data.perfil ? data.perfil.nome : 'N/A';
        document.getElementById('show_agencia').textContent = data.agencia ? data.agencia.nome : 'N/A';
        document.getElementById('show_endereco').textContent = data.endereco || '';
        document.getElementById('show_cidade').textContent = data.cidade || '';
        document.getElementById('show_provincia').textContent = data.provincia || '';
        document.getElementById('show_status_usuario').textContent = (data.status_usuario === 'ativo') ? 'Ativo' : 'Inativo';
        document.getElementById('show_created_at').textContent = data.created_at ? new Date(data.created_at).toLocaleString('pt-BR') : '';

        new bootstrap.Modal(document.getElementById('showUsuarioModal')).show();
    });
}

function editarUsuario(id) {
    fetch(`/admin/usuarios/${id}/edit`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        // preencher campos do modal de edição
        document.getElementById('edit_nome').value = data.nome || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_bi').value = data.bi || '';
        document.getElementById('edit_sexo').value = data.sexo || '';
        document.getElementById('edit_telefone').value = Array.isArray(data.telefone) ? data.telefone[0] : data.telefone || '';
        document.getElementById('edit_data_nascimento').value = data.data_nascimento ? data.data_nascimento.split('T')[0] : '';
        document.getElementById('edit_perfil_id').value = data.perfil_id || '';
        document.getElementById('edit_agencia_id').value = data.agencia_id || '';
        document.getElementById('edit_endereco').value = data.endereco || '';
        document.getElementById('edit_cidade').value = data.cidade || '';
        document.getElementById('edit_provincia').value = data.provincia || '';
        document.getElementById('edit_status_usuario').value = data.status_usuario || 'inativo';

        document.getElementById('editUsuarioForm').action = `/admin/usuarios/${id}`;
        new bootstrap.Modal(document.getElementById('editUsuarioModal')).show();
    });
}

function excluirUsuario(id) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        fetch(`/admin/usuarios/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(resp => {
            if (resp.ok) {
                toastr.success('Usuário excluído com sucesso');
                // remove row in-place
                const row = document.querySelector(`#usuariosTable tbody tr[data-user-id='${id}']`);
                if (row) row.remove();
            } else {
                resp.json().then(j => {
                    toastr.error(j.message || 'Erro ao excluir usuário');
                }).catch(() => toastr.error('Erro ao excluir usuário'));
            }
        });
    }
}

// Event listeners para filtros
document.getElementById('filtroNome').addEventListener('input', filtrarUsuarios);
document.getElementById('filtroEmail').addEventListener('input', filtrarUsuarios);
document.getElementById('filtroPerfil').addEventListener('change', filtrarUsuarios);

// AJAX submit para criar usuário
const createForm = document.getElementById('createUsuarioForm');
if (createForm) {
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(createForm);

            const submitBtn = createForm.querySelector('button[type="submit"]');
            const origHtml = submitBtn ? submitBtn.innerHTML : null;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Salvando';
            }

            const controller = new AbortController();
            const signal = controller.signal;
            const timeoutMs = 3000; // 15s timeout
            const timeoutId = setTimeout(() => {
                controller.abort();
                toastr.warning('A operação está demorando muito e foi cancelada. Tente novamente.');
            }, timeoutMs);

            fetch(createForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal
            }).then(async resp => {
                if (resp.ok) {
                    toastr.success('Usuário criado com sucesso');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createUsuarioModal'));
                    if (modal) modal.hide();
                    const json = await resp.json();
                    if (json.usuario) upsertUsuarioRow(json.usuario);
                } else if (resp.status === 422) {
                    const json = await resp.json();
                    const errors = json.errors || {};
                    applyFormErrors(createForm, errors);
                    const first = Object.values(errors)[0][0] || 'Erro de validação';
                    toastr.error(first);
                } else {
                    const text = await resp.text().catch(() => null);
                    toastr.error(text || 'Erro ao criar usuário');
                }
            }).catch(err => {
                if (err.name === 'AbortError') {
                    // already warned via timeout
                } else {
                    console.error(err);
                    toastr.error('Erro ao criar usuário');
                }
            }).finally(() => {
                clearTimeout(timeoutId);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origHtml;
                }
            });
    });
}

// AJAX submit para editar usuário (modal form)
const editForm = document.getElementById('editUsuarioForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const action = editForm.action;
        const formData = new FormData(editForm);

        const submitBtn = editForm.querySelector('button[type="submit"]');
        const origHtml = submitBtn ? submitBtn.innerHTML : null;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Atualizando';
        }

        const controller = new AbortController();
        const signal = controller.signal;
        const timeoutMs = 15000; // 15s timeout
        const timeoutId = setTimeout(() => {
            controller.abort();
            toastr.warning('A operação está demorando muito e foi cancelada. Tente novamente.');
        }, timeoutMs);

        fetch(action, {
            method: 'POST', // form uses _method PUT
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal
        }).then(async resp => {
            if (resp.ok) {
                toastr.success('Usuário atualizado com sucesso');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editUsuarioModal'));
                if (modal) modal.hide();
                const json = await resp.json();
                if (json.usuario) upsertUsuarioRow(json.usuario);
            } else if (resp.status === 422) {
                const json = await resp.json();
                const errors = json.errors || {};
                // apply errors to edit form (prefix fields with 'edit_' ids)
                applyFormErrors(editForm, errors, 'edit');
                const first = Object.values(errors)[0][0] || 'Erro de validação';
                toastr.error(first);
            } else {
                const text = await resp.text().catch(() => null);
                toastr.error(text || 'Erro ao atualizar usuário');
            }
        }).catch(err => {
            if (err.name === 'AbortError') {
                // already warned via timeout
            } else {
                console.error(err);
                toastr.error('Erro ao atualizar usuário');
            }
        }).finally(() => {
            clearTimeout(timeoutId);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = origHtml;
            }
        });
    });

// Helper to create or update a table row for a usuario object
function upsertUsuarioRow(usuario) {
    const tbody = document.querySelector('#usuariosTable tbody');
    let row = tbody.querySelector(`tr[data-user-id='${usuario.id}']`);
    const perfilNome = usuario.perfil ? usuario.perfil.nome : 'N/A';
    const agenciaNome = usuario.agencia ? usuario.agencia.nome : 'N/A';
    const statusHtml = (usuario.status_usuario === 'ativo') ? "<span class='badge bg-success'>Ativo</span>" : "<span class='badge bg-danger'>Inativo</span>";

    const rowHtml = `
        <td class="col-id">${usuario.id}</td>
        <td class="col-nome">${escapeHtml(usuario.nome || '')}</td>
        <td class="col-email">${escapeHtml(usuario.email || '')}</td>
        <td class="col-perfil"><span class="badge bg-info">${escapeHtml(perfilNome)}</span></td>
        <td class="col-agencia">${escapeHtml(agenciaNome)}</td>
        <td class="col-status">${statusHtml}</td>
        <td class="col-acoes">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-info" onclick="visualizarUsuario(${usuario.id})" title="Visualizar"><i class="fas fa-eye"></i></button>
                <button type="button" class="btn btn-sm btn-warning" onclick="editarUsuario(${usuario.id})" title="Editar"><i class="fas fa-edit"></i></button>
                <button type="button" class="btn btn-sm btn-danger" onclick="excluirUsuario(${usuario.id})" title="Excluir"><i class="fas fa-trash"></i></button>
            </div>
        </td>
    `;

    if (row) {
        row.innerHTML = rowHtml;
        row.setAttribute('data-perfil-id', usuario.perfil_id || '');
    } else {
        const tr = document.createElement('tr');
        tr.setAttribute('data-user-id', usuario.id);
        tr.setAttribute('data-perfil-id', usuario.perfil_id || '');
        tr.innerHTML = rowHtml;
        // insert at top
        if (tbody.firstChild) tbody.insertBefore(tr, tbody.firstChild);
        else tbody.appendChild(tr);
    }
}

function escapeHtml(unsafe) {
    return String(unsafe)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}
}
</script>
@endpush