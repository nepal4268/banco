@extends('layouts.admin')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Detalhes do Usuário</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-warning btn-sm">Editar</a>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary btn-sm">Voltar</a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nome</dt>
                        <dd class="col-sm-8">{{ $usuario->nome }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $usuario->email }}</dd>

                        <dt class="col-sm-4">Perfil</dt>
                        <dd class="col-sm-8">{{ $usuario->perfil->nome ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Agência</dt>
                        <dd class="col-sm-8">{{ $usuario->agencia->nome ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Telefone</dt>
                        <dd class="col-sm-8">{{ is_array($usuario->telefone) ? implode(', ', $usuario->telefone) : $usuario->telefone }}</dd>

                        <dt class="col-sm-4">BI</dt>
                        <dd class="col-sm-8">{{ $usuario->bi }}</dd>

                        <dt class="col-sm-4">Data de Nascimento</dt>
                        <dd class="col-sm-8">{{ optional($usuario->data_nascimento)->format('d/m/Y') }}</dd>

                        <dt class="col-sm-4">Endereço</dt>
                        <dd class="col-sm-8">{{ $usuario->endereco }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ ($usuario->status_usuario ?? 'inativo') === 'ativo' ? 'Ativo' : 'Inativo' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Usuário')
@section('page-title', 'Usuário')

@section('content')
<div class="card">
    <div class="card-body">
        <p>Detalhes do usuário (placeholder).</p>
    </div>
</div>
@endsection
