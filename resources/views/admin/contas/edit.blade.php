@extends('layouts.app')

@section('title', 'Editar Conta')
@section('page-title', 'Editar Conta')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.contas.index') }}">Contas</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Editar Conta</h3></div>
            <form method="POST" action="{{ route('admin.contas.update', $conta->id) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="agencia_id">Agência *</label>
                                <select name="agencia_id" id="agencia_id" class="form-control">
                                    @foreach($agencias as $agencia)
                                        <option value="{{ $agencia->id }}" {{ $conta->agencia_id == $agencia->id ? 'selected' : '' }}>{{ $agencia->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_conta_id">Tipo de Conta</label>
                                <select name="tipo_conta_id" id="tipo_conta_id" class="form-control">
                                    @foreach($tiposConta as $tipo)
                                        <option value="{{ $tipo->id }}" {{ $conta->tipo_conta_id == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="moeda_id">Moeda</label>
                                <select name="moeda_id" id="moeda_id" class="form-control">
                                    @foreach($moedas as $moeda)
                                        <option value="{{ $moeda->id }}" {{ $conta->moeda_id == $moeda->id ? 'selected' : '' }}>{{ $moeda->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_conta_id">Status</label>
                                <select name="status_conta_id" id="status_conta_id" class="form-control">
                                    @foreach($statusConta as $status)
                                        <option value="{{ $status->id }}" {{ $conta->status_conta_id == $status->id ? 'selected' : '' }}>{{ $status->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="saldo">Saldo</label>
                                <input type="number" step="0.01" name="saldo" id="saldo" class="form-control" value="{{ old('saldo', $conta->saldo) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Salvar</button>
                    <a href="{{ route('admin.contas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
