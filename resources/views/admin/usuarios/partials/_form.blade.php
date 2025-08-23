<div class="row">
    <div class="col-md-6 mb-3">
        <label for="nome" class="form-label">Nome Completo *</label>
        <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome"
               value="{{ old('nome', optional($usuario)->nome) }}" required>
        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email *</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
               value="{{ old('email', optional($usuario)->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="senha" class="form-label">Senha {{ isset($usuario) ? '(deixe em branco para manter)' : '*' }}</label>
        <input type="password" class="form-control @error('senha') is-invalid @enderror" id="senha" name="senha" {{ isset($usuario) ? '' : 'required' }}>
        @error('senha')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="senha_confirmation" class="form-label">Confirmar Senha</label>
        <input type="password" class="form-control" id="senha_confirmation" name="senha_confirmation" {{ isset($usuario) ? '' : 'required' }}>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="bi" class="form-label">Número do BI *</label>
        <input type="text" class="form-control @error('bi') is-invalid @enderror" id="bi" name="bi"
               value="{{ old('bi', optional($usuario)->bi) }}" required>
        @error('bi')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="sexo" class="form-label">Sexo *</label>
        <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo" required>
            <option value="">Selecione...</option>
            <option value="M" {{ old('sexo', optional($usuario)->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo', optional($usuario)->sexo) == 'F' ? 'selected' : '' }}>Feminino</option>
        </select>
        @error('sexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="telefone" class="form-label">Telefone *</label>
        <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone"
               value="{{ old('telefone', is_array(optional($usuario)->telefone) ? optional($usuario)->telefone[0] : optional($usuario)->telefone) }}" required>
        @error('telefone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
        <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" id="data_nascimento" name="data_nascimento"
               value="{{ old('data_nascimento', optional($usuario)->data_nascimento ? optional($usuario)->data_nascimento->format('Y-m-d') : '') }}" required>
        @error('data_nascimento')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="perfil_id" class="form-label">Perfil *</label>
        <select class="form-select @error('perfil_id') is-invalid @enderror" id="perfil_id" name="perfil_id" required>
            <option value="">Selecione um perfil...</option>
            @foreach($perfis as $perfil)
                <option value="{{ $perfil->id }}" {{ (int)old('perfil_id', optional($usuario)->perfil_id) === $perfil->id ? 'selected' : '' }}>{{ $perfil->nome }}</option>
            @endforeach
        </select>
        @error('perfil_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="agencia_id" class="form-label">Agência</label>
        <select class="form-select @error('agencia_id') is-invalid @enderror" id="agencia_id" name="agencia_id">
            <option value="">Selecione uma agência...</option>
            @foreach($agencias as $agencia)
                <option value="{{ $agencia->id }}" {{ (int)old('agencia_id', optional($usuario)->agencia_id) === $agencia->id ? 'selected' : '' }}>{{ $agencia->nome }}</option>
            @endforeach
        </select>
        @error('agencia_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <label for="endereco" class="form-label">Endereço</label>
        <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco" name="endereco"
               value="{{ old('endereco', optional($usuario)->endereco) }}">
        @error('endereco')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="cidade" class="form-label">Cidade</label>
        <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade" name="cidade"
               value="{{ old('cidade', optional($usuario)->cidade) }}">
        @error('cidade')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="provincia" class="form-label">Província</label>
        <input type="text" class="form-control @error('provincia') is-invalid @enderror" id="provincia" name="provincia"
               value="{{ old('provincia', optional($usuario)->provincia) }}">
        @error('provincia')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="status_usuario" class="form-label">Status</label>
        <select class="form-select" id="status_usuario" name="status_usuario">
            <option value="ativo" {{ old('status_usuario', optional($usuario)->status_usuario) === 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ old('status_usuario', optional($usuario)->status_usuario) === 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>
</div>
