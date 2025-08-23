@extends('layouts.admin')

@section('title', 'Criar Perfil')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4>Criar Perfil</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.perfis.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" required>
                    @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control">{{ old('descricao') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissões</label>
                    <div class="row">
                        @foreach($permissoes as $perm)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissoes[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}">
                                <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->label }} <small class="text-muted">({{ $perm->code }})</small></label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button class="btn btn-success">Salvar</button>
                <a href="{{ route('admin.perfis.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
