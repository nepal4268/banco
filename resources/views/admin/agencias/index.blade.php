@extends('layouts.admin')

@section('title', 'Gestão de Agências')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>Gestão de Agências
                    </h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAgenciaModal">
                        <i class="fas fa-plus me-1"></i>Nova Agência
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="filtroNome" placeholder="Filtrar por nome...">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="filtroEndereco" placeholder="Filtrar por endereço...">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        </div>
                    </div>

                    <!-- Tabela -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="agenciasTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Endereço</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agencias as $agencia)
                                <tr>
                                    <td>{{ $agencia->id }}</td>
                                    <td>{{ $agencia->nome }}</td>
                                    <td>{{ $agencia->endereco }}</td>
                                    <td>{{ $agencia->telefone }}</td>
                                    <td>{{ $agencia->email }}</td>
                                    <td>
                                        @if($agencia->ativo)
                                            <span class="badge bg-success">Ativa</span>
                                        @else
                                            <span class="badge bg-danger">Inativa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" onclick="visualizarAgencia({{ $agencia->id }})" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editarAgencia({{ $agencia->id }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="excluirAgencia({{ $agencia->id }})" title="Excluir">
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
                        {{ $agencias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Agência -->
@include('admin.agencias.partials.create-modal')

<!-- Modal Editar Agência -->
@include('admin.agencias.partials.edit-modal')

<!-- Modal Visualizar Agência -->
@include('admin.agencias.partials.show-modal')

<!-- Modal Confirmar Exclusão -->
@include('admin.agencias.partials.delete-modal')

@endsection

@push('scripts')
<script>
function limparFiltros() {
    document.getElementById('filtroNome').value = '';
    document.getElementById('filtroEndereco').value = '';
    filtrarAgencias();
}

function filtrarAgencias() {
    const nome = document.getElementById('filtroNome').value.toLowerCase();
    const endereco = document.getElementById('filtroEndereco').value.toLowerCase();
    
    const tbody = document.querySelector('#agenciasTable tbody');
    const rows = tbody.querySelectorAll('tr');
    
    rows.forEach(row => {
        const nomeCell = row.cells[1].textContent.toLowerCase();
        const enderecoCell = row.cells[2].textContent.toLowerCase();
        
        const matchNome = nome === '' || nomeCell.includes(nome);
        const matchEndereco = endereco === '' || enderecoCell.includes(endereco);
        
        row.style.display = matchNome && matchEndereco ? '' : 'none';
    });
}

function visualizarAgencia(id) {
    fetch(`/admin/agencias/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('show_nome').textContent = data.nome;
            document.getElementById('show_endereco').textContent = data.endereco;
            document.getElementById('show_telefone').textContent = data.telefone;
            document.getElementById('show_email').textContent = data.email;
            document.getElementById('show_ativo').textContent = data.ativo ? 'Ativa' : 'Inativa';
            document.getElementById('show_created_at').textContent = new Date(data.created_at).toLocaleDateString('pt-BR');
            
            new bootstrap.Modal(document.getElementById('showAgenciaModal')).show();
        });
}

function editarAgencia(id) {
    fetch(`/admin/agencias/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_nome').value = data.nome;
            document.getElementById('edit_endereco').value = data.endereco;
            document.getElementById('edit_telefone').value = data.telefone;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_ativo').value = data.ativo ? '1' : '0';
            
            document.getElementById('editAgenciaForm').action = `/admin/agencias/${id}`;
            new bootstrap.Modal(document.getElementById('editAgenciaModal')).show();
        });
}

function excluirAgencia(id) {
    fetch(`/admin/agencias/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('delete_agencia_nome').textContent = data.nome;
            document.getElementById('deleteAgenciaForm').action = `/admin/agencias/${id}`;
            new bootstrap.Modal(document.getElementById('deleteAgenciaModal')).show();
        });
}

// Event listeners para filtros
document.getElementById('filtroNome').addEventListener('input', filtrarAgencias);
document.getElementById('filtroEndereco').addEventListener('input', filtrarAgencias);
</script>
@endpush