@extends('layouts.app')

@section('title', 'PIX')
@section('page-title', 'PIX')

@section('content')
<div class="card">
    <div class="card-body">
        <h5>PIX</h5>
        <p class="text-muted">Formulário para efetuar PIX.</p>

        @include('admin.transacoes.partials.op_transfer')
    </div>
</div>
@endsection
