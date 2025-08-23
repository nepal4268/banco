@extends('layouts.app')

@section('title', 'Procurar por BI')
@section('page-title', 'Procurar Cliente por BI')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.contas.index') }}">Contas</a></li>
<li class="breadcrumb-item active">Procurar por BI</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Buscar Cliente por BI</h3></div>
            <form method="POST" action="{{ route('admin.contas.findByBi') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="bi">Número do BI</label>
                        <input type="text" name="bi" id="bi" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Procurar</button>
                    <a href="{{ route('admin.contas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
