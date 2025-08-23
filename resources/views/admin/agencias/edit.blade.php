@extends('layouts.admin')

@section('title', 'Editar Agência')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar Agência</div>
                <div class="card-body">
                    <form action="{{ route('admin.agencias.update', $agencia->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="codigo_banco" class="form-label">Código do Banco</label>
                            <input type="text" name="codigo_banco" id="codigo_banco" class="form-control" value="{{ old('codigo_banco', $agencia->codigo_banco) }}">
                            @error('codigo_banco')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="codigo_agencia" class="form-label">Código da Agência</label>
                            <input type="text" name="codigo_agencia" id="codigo_agencia" class="form-control" value="{{ old('codigo_agencia', $agencia->codigo_agencia) }}">
                            @error('codigo_agencia')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $agencia->nome) }}" required>
                            @error('nome')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" name="endereco" id="endereco" class="form-control" value="{{ old('endereco', $agencia->endereco) }}">
                            @error('endereco')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" name="cidade" id="cidade" class="form-control" value="{{ old('cidade', $agencia->cidade) }}">
                            @error('cidade')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="provincia" class="form-label">Província</label>
                            <input type="text" name="provincia" id="provincia" class="form-control" value="{{ old('provincia', $agencia->provincia) }}">
                            @error('provincia')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone (separar por vírgula)</label>
                            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone', is_array($agencia->telefone) ? implode(',', $agencia->telefone) : $agencia->telefone) }}">
                            @error('telefone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $agencia->email) }}">
                            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="hidden" name="ativa" value="0">
                            <input type="checkbox" name="ativa" id="ativa" class="form-check-input" value="1" {{ old('ativa', $agencia->ativa) ? 'checked' : '' }}>
                            <label for="ativa" class="form-check-label">Ativa</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.agencias.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
