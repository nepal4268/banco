@extends('layouts.admin')

@section('title', 'Detalhes do Perfil')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h4>Perfil: {{ $perfil->nome ?? '—' }}</h4></div>
        <div class="card-body">
            <p><strong>Descrição:</strong> {{ $perfil->descricao ?? '-' }}</p>
            <p><strong>Permissões:</strong>
                @if($perfil->permissoes->isEmpty()) - @else
                    <ul>
                        @foreach($perfil->permissoes as $p)
                            <li>{{ $p->label }} ({{ $p->code }})</li>
                        @endforeach
                    </ul>
                @endif
            </p>
            <a href="{{ route('admin.perfis.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection
