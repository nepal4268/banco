@extends('layouts.app')

@section('title', 'TED')
@section('page-title', 'TED')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>TED</h5>
        <p class="text-muted">Formulário para efetuar TEDs (Transferência Eletrônica Disponível).</p>

        @include('admin.transacoes.partials.op_transfer')
    </div>
</div>
@endsection
