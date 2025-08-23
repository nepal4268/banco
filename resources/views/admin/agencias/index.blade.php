@extends('layouts.admin')

@section('title', 'Gestão de Agências')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-building me-2"></i>Gestão de Agências</h4>
                    <a href="{{ route('admin.agencias.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nova Agência
                    </a>
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
                                    <td>{{ is_array($agencia->telefone) ? implode(', ', $agencia->telefone) : $agencia->telefone }}</td>
                                    <td>{{ $agencia->email }}</td>
                                    <td>
                                        @if($agencia->ativa)
                                            <span class="badge bg-success">Ativa</span>
                                        @else
                                            <span class="badge bg-danger">Inativa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.agencias.show', $agencia->id) }}" class="btn btn-sm btn-info" title="Visualizar"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.agencias.edit', $agencia->id) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('admin.agencias.dashboard', $agencia->id) }}" class="btn btn-sm btn-primary" title="Dashboard"><i class="fas fa-chart-line"></i></a>
                                        <form action="{{ route('admin.agencias.destroy', $agencia->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Deseja excluir esta agência?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                        </form>
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

// attach listeners safely
document.addEventListener('DOMContentLoaded', function() {
    const fNome = document.getElementById('filtroNome');
    const fEndereco = document.getElementById('filtroEndereco');
    if (fNome) fNome.addEventListener('input', filtrarAgencias);
    if (fEndereco) fEndereco.addEventListener('input', filtrarAgencias);
});
</script>
@endpush