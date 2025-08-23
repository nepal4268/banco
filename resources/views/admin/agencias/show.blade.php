@extends('layouts.admin')

@section('title', 'Detalhes da Agência')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detalhes da Agência</div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID</dt>
                        <dd class="col-sm-8">{{ $agencia->id }}</dd>

                        <dt class="col-sm-4">Código do Banco</dt>
                        <dd class="col-sm-8">{{ $agencia->codigo_banco }}</dd>

                        <dt class="col-sm-4">Código da Agência</dt>
                        <dd class="col-sm-8">{{ $agencia->codigo_agencia }}</dd>

                        <dt class="col-sm-4">Nome</dt>
                        <dd class="col-sm-8">{{ $agencia->nome }}</dd>

                        <dt class="col-sm-4">Endereço</dt>
                        <dd class="col-sm-8">{{ $agencia->endereco }}</dd>

                        <dt class="col-sm-4">Cidade</dt>
                        <dd class="col-sm-8">{{ $agencia->cidade }}</dd>

                        <dt class="col-sm-4">Província</dt>
                        <dd class="col-sm-8">{{ $agencia->provincia }}</dd>

                        <dt class="col-sm-4">Telefone</dt>
                        <dd class="col-sm-8">{{ is_array($agencia->telefone) ? implode(', ', $agencia->telefone) : $agencia->telefone }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $agencia->email }}</dd>

                        <dt class="col-sm-4">Ativa</dt>
                        <dd class="col-sm-8">{{ $agencia->ativa ? 'Sim' : 'Não' }}</dd>
                    </dl>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.agencias.edit', $agencia->id) }}" class="btn btn-warning me-2">Editar</a>
                        <a href="{{ route('admin.agencias.index') }}" class="btn btn-secondary">Voltar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
