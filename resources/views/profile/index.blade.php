@extends('layouts.master')

@section('title', 'Meu Perfil')

@section('content')
<div class="row">
    <div class="col-md-3">
        <x-card>
            <div class="text-center">
                <img class="profile-user-img img-fluid img-circle" 
                     src="{{ asset('img/user2-160x160.jpg') }}" 
                     alt="Foto do usuário">
            </div>
            <h3 class="profile-username text-center">{{ Auth::user()->nome }}</h3>
            <p class="text-muted text-center">{{ Auth::user()->perfil->nome ?? 'Sem perfil' }}</p>
            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                    <b>Email</b> <a class="float-right">{{ Auth::user()->email }}</a>
                </li>
                <li class="list-group-item">
                    <b>Agência</b> <a class="float-right">{{ Auth::user()->agencia->nome ?? 'N/A' }}</a>
                </li>
                <li class="list-group-item">
                    <b>Status</b> <a class="float-right">{{ Auth::user()->status_usuario }}</a>
                </li>
            </ul>
        </x-card>
    </div>

    <div class="col-md-9">
        <x-card>
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="#settings" data-toggle="tab">Configurações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#security" data-toggle="tab">Segurança</a>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <div class="active tab-pane" id="settings">
                    <x-form :action="route('profile.update')" method="PUT">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nome</label>
                            <div class="col-sm-10">
                                <input type="text" name="nome" class="form-control" value="{{ Auth::user()->nome }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Telefone</label>
                            <div class="col-sm-10">
                                <input type="text" name="telefone" class="form-control phone" 
                                       value="{{ is_array(Auth::user()->telefone) ? Auth::user()->telefone[0] : Auth::user()->telefone }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Endereço</label>
                            <div class="col-sm-10">
                                <input type="text" name="endereco" class="form-control" value="{{ Auth::user()->endereco }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Cidade</label>
                            <div class="col-sm-10">
                                <input type="text" name="cidade" class="form-control" value="{{ Auth::user()->cidade }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Província</label>
                            <div class="col-sm-10">
                                <input type="text" name="provincia" class="form-control" value="{{ Auth::user()->provincia }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            </div>
                        </div>
                    </x-form>
                </div>

                <div class="tab-pane" id="security">
                    <x-form :action="route('profile.password')" method="PUT">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Senha Atual</label>
                            <div class="col-sm-9">
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nova Senha</label>
                            <div class="col-sm-9">
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Confirmar Nova Senha</label>
                            <div class="col-sm-9">
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-primary">Alterar Senha</button>
                            </div>
                        </div>
                    </x-form>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection
