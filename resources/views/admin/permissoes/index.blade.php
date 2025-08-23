@extends('layouts.admin')

@section('title', 'Permissões')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Permissões</h4>
            <a href="{{ route('admin.permissoes.create') }}" class="btn btn-primary">Nova Permissão</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark"><tr><th>ID</th><th>Code</th><th>Label</th><th>Descrição</th><th>Ações</th></tr></thead>
                    <tbody>
                        @foreach($permissoes as $perm)
                        <tr>
                            <td>{{ $perm->id }}</td>
                            <td>{{ $perm->code }}</td>
                            <td>{{ $perm->label }}</td>
                            <td>{{ $perm->descricao }}</td>
                            <td>
                                <a href="{{ route('admin.permissoes.edit', $perm->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('admin.permissoes.destroy', $perm->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirma exclusão?')">
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
            <div class="d-flex justify-content-center">{{ $permissoes->links() }}</div>
        </div>
    </div>
</div>
@endsection
