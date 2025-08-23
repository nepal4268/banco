@extends('layouts.auth')

@section('title', 'Recuperar Senha')

@section('content')
<div class="card-body">
    <p class="login-box-msg">Digite seu e-mail para recuperar sua senha</p>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   placeholder="Email" value="{{ old('email') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    Enviar Link de Recuperação
                </button>
            </div>
        </div>
    </form>

    <p class="mt-3 mb-1">
        <a href="{{ route('login') }}">Voltar para Login</a>
    </p>
</div>
@endsection
