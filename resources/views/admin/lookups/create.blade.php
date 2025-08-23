@extends('layouts.admin')

@section('title', 'Criar ' . $cfg['title'])

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4>Criar {{ $cfg['title'] }}</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.lookups.store', $key) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">{{ ucfirst($cfg['field']) }}</label>
                    <input type="text" name="{{ $cfg['field'] }}" class="form-control @error($cfg['field']) is-invalid @enderror" value="{{ old($cfg['field']) }}" required>
                    @error($cfg['field'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-success">Salvar</button>
                <a href="{{ route('admin.lookups.index', $key) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
