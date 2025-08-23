@extends('layouts.admin')

@section('title', 'Permissão')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4>Permissão: {{ $permissao->label ?? $permissao->code }}</h4></div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Código</dt>
                <dd class="col-sm-9">{{ $permissao->code }}</dd>

                <dt class="col-sm-3">Label</dt>
                <dd class="col-sm-9">{{ $permissao->label }}</dd>

                <dt class="col-sm-3">Descrição</dt>
                <dd class="col-sm-9">{{ $permissao->descricao }}</dd>
            </dl>

            <a href="{{ route('admin.permissoes.edit', $permissao->id) }}" class="btn btn-primary">Editar</a>
            <a href="{{ route('admin.permissoes.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection
