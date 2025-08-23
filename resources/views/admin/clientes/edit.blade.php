@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('page-title', 'Editar Cliente')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.clientes.index') }}">Clientes</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Editar Cliente</h3>
            </div>

            <form method="POST" action="{{ route('admin.clientes.update', $cliente->id) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome Completo *</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $cliente->nome) }}" required>
                                @error('nome')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $cliente->email) }}">
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bi">Bilhete de Identidade *</label>
                                <input type="text" class="form-control @error('bi') is-invalid @enderror" id="bi" name="bi" value="{{ old('bi', $cliente->bi) }}" required>
                                @error('bi')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefone">Telefone *</label>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone', is_array($cliente->telefone) ? implode(', ', $cliente->telefone) : $cliente->telefone) }}" required>
                                @error('telefone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="data_nascimento">Data de Nascimento *</label>
                                <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento', $cliente->data_nascimento ? $cliente->data_nascimento->format('Y-m-d') : '') }}">
                                @error('data_nascimento')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sexo">Sexo *</label>
                                <select class="form-control @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                                    <option value="">Selecione</option>
                                    <option value="M" {{ old('sexo', $cliente->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('sexo', $cliente->sexo) == 'F' ? 'selected' : '' }}>Feminino</option>
                                </select>
                                @error('sexo')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_cliente_id">Tipo de Cliente *</label>
                                <select class="form-control @error('tipo_cliente_id') is-invalid @enderror" id="tipo_cliente_id" name="tipo_cliente_id">
                                    @foreach($tiposCliente as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_cliente_id', $cliente->tipo_cliente_id) == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_cliente_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_cliente_id">Status *</label>
                                <select class="form-control @error('status_cliente_id') is-invalid @enderror" id="status_cliente_id" name="status_cliente_id">
                                    @foreach($statusCliente as $status)
                                        <option value="{{ $status->id }}" {{ old('status_cliente_id', $cliente->status_cliente_id) == $status->id ? 'selected' : '' }}>{{ $status->nome }}</option>
                                    @endforeach
                                </select>
                                @error('status_cliente_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="endereco">Endereço</label>
                                <textarea class="form-control @error('endereco') is-invalid @enderror" id="endereco" name="endereco" rows="3">{{ old('endereco', $cliente->endereco) }}</textarea>
                                @error('endereco')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade" name="cidade" value="{{ old('cidade', $cliente->cidade) }}">
                                @error('cidade')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="provincia">Província</label>
                                <input type="text" class="form-control @error('provincia') is-invalid @enderror" id="provincia" name="provincia" value="{{ old('provincia', $cliente->provincia) }}">
                                @error('provincia')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
