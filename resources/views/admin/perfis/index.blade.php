@extends('layouts.admin')

@section('title', 'Gestão de Perfis')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Perfis</h4>
            <a href="{{ route('admin.perfis.create') }}" class="btn btn-primary">Novo Perfil</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark"><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Permissões</th><th>Ações</th></tr></thead>
                    <tbody>
                        @foreach($perfis as $perfil)
                        <tr>
                            <td>{{ $perfil->id }}</td>
                            <td>{{ $perfil->nome }}</td>
                            <td>{{ $perfil->descricao }}</td>
                            <td>{{ $perfil->permissoes->pluck('label')->join(', ') }}</td>
                            <td>
                                <a href="{{ route('admin.perfis.show', $perfil->id) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('admin.perfis.edit', $perfil->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('admin.perfis.destroy', $perfil->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirma exclusão?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">{{ $perfis->links() }}</div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Gestão de Perfis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-tag me-2"></i>Gestão de Perfis
                    </h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPerfilModal">
                        <i class="fas fa-plus me-1"></i>Novo Perfil
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="filtroNome" placeholder="Filtrar por nome...">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtroNivel">
                                <option value="">Todos os níveis</option>
                                <option value="1">Nível 1</option>
                                <option value="2">Nível 2</option>
                                <option value="3">Nível 3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        </div>
                    </div>

                    <!-- Tabela -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="perfisTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Nível</th>
                                    <th>Permissões</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perfis as $perfil)
                                <tr>
                                    <td>{{ $perfil->id }}</td>
                                    <td>{{ $perfil->nome }}</td>
                                    <td>{{ $perfil->descricao }}</td>
                                    <td>
                                        <span class="badge bg-info">Nível {{ $perfil->nivel }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $perfil->permissoes->count() }} permissões</span>
                                    </td>
                                    <td>
                                        @if($perfil->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" onclick="visualizarPerfil({{ $perfil->id }})" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editarPerfil({{ $perfil->id }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" onclick="gerenciarPermissoes({{ $perfil->id }})" title="Permissões">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="excluirPerfil({{ $perfil->id }})" title="Excluir">
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
                        {{ $perfis->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Perfil -->
@include('admin.perfis.partials.create-modal')

<!-- Modal Editar Perfil -->
@include('admin.perfis.partials.edit-modal')

<!-- Modal Visualizar Perfil -->
@include('admin.perfis.partials.show-modal')

<!-- Modal Gerenciar Permissões -->
@include('admin.perfis.partials.permissoes-modal')

<!-- Modal Confirmar Exclusão -->
@include('admin.perfis.partials.delete-modal')

@endsection

@push('scripts')
<script>
function limparFiltros() {
    document.getElementById('filtroNome').value = '';
    document.getElementById('filtroNivel').value = '';
    filtrarPerfis();
}

function filtrarPerfis() {
    const nome = document.getElementById('filtroNome').value.toLowerCase();
    const nivel = document.getElementById('filtroNivel').value;
    
    const tbody = document.querySelector('#perfisTable tbody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach(row => {
        const nomeCell = row.cells[1].textContent.toLowerCase();
        const nivelCell = row.cells[3].textContent;
        
        const matchNome = nome === '' || nomeCell.includes(nome);
        const matchNivel = nivel === '' || nivelCell.includes(`Nível ${nivel}`);
        
        row.style.display = matchNome && matchNivel ? '' : 'none';
    });
}

function visualizarPerfil(id) {
    fetch(`/admin/perfis/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('show_nome').textContent = data.nome;
            document.getElementById('show_descricao').textContent = data.descricao;
            document.getElementById('show_nivel').textContent = `Nível ${data.nivel}`;
            document.getElementById('show_ativo').textContent = data.ativo ? 'Ativo' : 'Inativo';
            document.getElementById('show_created_at').textContent = new Date(data.created_at).toLocaleDateString('pt-BR');
            
            new bootstrap.Modal(document.getElementById('showPerfilModal')).show();
        });
}

function editarPerfil(id) {
    fetch(`/admin/perfis/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_nome').value = data.nome;
            document.getElementById('edit_descricao').value = data.descricao;
            document.getElementById('edit_nivel').value = data.nivel;
            document.getElementById('edit_ativo').value = data.ativo ? '1' : '0';
            
            document.getElementById('editPerfilForm').action = `/admin/perfis/${id}`;
            new bootstrap.Modal(document.getElementById('editPerfilModal')).show();
        });
}

function gerenciarPermissoes(id) {
    fetch(`/admin/perfis/${id}/permissoes`)
        .then(response => response.json())
        .then(data => {
            // Carregar permissões do perfil
            const checkboxes = document.querySelectorAll('#permissoesModal input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = data.permissoes.includes(parseInt(checkbox.value));
            });
            
            document.getElementById('permissoesForm').action = `/admin/perfis/${id}/permissoes`;
            new bootstrap.Modal(document.getElementById('permissoesModal')).show();
        });
}

function excluirPerfil(id) {
    fetch(`/admin/perfis/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('delete_perfil_nome').textContent = data.nome;
            document.getElementById('deletePerfilForm').action = `/admin/perfis/${id}`;
            new bootstrap.Modal(document.getElementById('deletePerfilModal')).show();
        });
}

// Event listeners para filtros
document.getElementById('filtroNome').addEventListener('input', filtrarPerfis);
document.getElementById('filtroNivel').addEventListener('change', filtrarPerfis);
</script>
@endpush