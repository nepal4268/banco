@extends('layouts.admin')

@section('title', 'Editar ' . $cfg['title'])

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4>Editar {{ $cfg['title'] }}</h4></div>
        <div class="card-body">
            <form action="{{ route('admin.lookups.update', [$key, $item->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">{{ ucfirst($cfg['field']) }}</label>
                    <input type="text" name="{{ $cfg['field'] }}" class="form-control @error($cfg['field']) is-invalid @enderror" value="{{ old($cfg['field'], $item->{$cfg['field']}) }}" required>
                    @error($cfg['field'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-success">Salvar</button>
                <a href="{{ route('admin.lookups.index', $key) }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
