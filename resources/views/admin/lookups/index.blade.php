@extends('layouts.admin')

@section('title', $cfg['title'])

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $cfg['title'] }}</h4>
            <a href="{{ route('admin.lookups.create', $key) }}" class="btn btn-primary">Nova</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>{{ ucfirst($cfg['field']) }}</th><th>Ações</th></tr></thead>
                    <tbody>
                        @foreach($items as $it)
                        <tr>
                            <td>{{ $it->id }}</td>
                            <td>{{ $it->{$cfg['field']} }}</td>
                            <td>
                                <a href="{{ route('admin.lookups.edit', [$key, $it->id]) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('admin.lookups.destroy', [$key, $it->id]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirma exclusão?')">
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
            <div class="d-flex justify-content-center">{{ $items->links() }}</div>
        </div>
    </div>
</div>
@endsection
