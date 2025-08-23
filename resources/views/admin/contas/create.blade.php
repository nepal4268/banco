@extends('layouts.app')

@section('title', 'Nova Conta')
@section('page-title', 'Nova Conta')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.contas.index') }}">Contas</a></li>
<li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Criar Conta</h3></div>
            <form method="POST" action="{{ route('admin.contas.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_id">Cliente *</label>
                                <select name="cliente_id" id="cliente_id" class="form-control">
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="agencia_id">Agência *</label>
                                <select name="agencia_id" id="agencia_id" class="form-control">
                                    @foreach($agencias as $agencia)
                                        <option value="{{ $agencia->id }}" {{ old('agencia_id') == $agencia->id ? 'selected' : '' }}>{{ $agencia->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo_conta_id">Tipo de Conta *</label>
                                <select name="tipo_conta_id" id="tipo_conta_id" class="form-control">
                                    @foreach($tiposConta as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_conta_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="moeda_id">Moeda</label>
                                <select name="moeda_id" id="moeda_id" class="form-control">
                                    @foreach($moedas as $moeda)
                                        <option value="{{ $moeda->id }}">{{ $moeda->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_conta_id">Status</label>
                                <select name="status_conta_id" id="status_conta_id" class="form-control">
                                    @foreach($statusConta as $status)
                                        <option value="{{ $status->id }}" {{ old('status_conta_id') == $status->id ? 'selected' : '' }}>{{ $status->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="saldo_inicial">Saldo Inicial</label>
                                <input type="number" step="0.01" name="saldo_inicial" id="saldo_inicial" class="form-control" value="{{ old('saldo_inicial', 0) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="limite_credito">Limite de Crédito</label>
                                <input type="number" step="0.01" name="limite_credito" id="limite_credito" class="form-control" value="{{ old('limite_credito') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Criar Conta</button>
                    <a href="{{ route('admin.contas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
