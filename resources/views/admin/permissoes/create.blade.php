@extends('layouts.admin')

@section('title', 'Criar Permissão')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4>Criar Permissão</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.permissoes.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Label</label>
                    <input type="text" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label') }}" required>
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control">{{ old('descricao') }}</textarea>
                </div>
                <button class="btn btn-success">Salvar</button>
                <a href="{{ route('admin.permissoes.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
