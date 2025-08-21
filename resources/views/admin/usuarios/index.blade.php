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
                                <tr>
                                    <td>{{ $usuario->id }}</td>
                                    <td>{{ $usuario->nome }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $usuario->perfil->nome ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $usuario->agencia->nome ?? 'N/A' }}</td>
                                    <td>
                                        @if($usuario->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
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
        const perfilCell = row.cells[3].textContent;
        
        const matchNome = nome === '' || nomeCell.includes(nome);
        const matchEmail = email === '' || emailCell.includes(email);
        const matchPerfil = perfil === '' || perfilCell.includes(perfil);
        
        row.style.display = matchNome && matchEmail && matchPerfil ? '' : 'none';
    });
}

function visualizarUsuario(id) {
    // Implementar visualização
    window.location.href = `/admin/usuarios/${id}`;
}

function editarUsuario(id) {
    // Carregar dados do usuário no modal
    fetch(`/admin/usuarios/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            // Preencher modal com dados
            document.getElementById('editUsuarioModal').style.display = 'block';
        });
}

function excluirUsuario(id) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        fetch(`/admin/usuarios/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => {
            location.reload();
        });
    }
}

// Event listeners para filtros
document.getElementById('filtroNome').addEventListener('input', filtrarUsuarios);
document.getElementById('filtroEmail').addEventListener('input', filtrarUsuarios);
document.getElementById('filtroPerfil').addEventListener('change', filtrarUsuarios);
</script>
@endpush