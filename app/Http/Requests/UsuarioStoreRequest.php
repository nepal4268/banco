<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|string|min:6|confirmed',
            'perfil_id' => 'required|exists:perfis,id',
            'agencia_id' => 'nullable|exists:agencias,id',
            'bi' => 'required|string|max:25|unique:usuarios,bi',
            'sexo' => 'required|in:M,F',
            'telefone' => 'nullable|string|max:255',
            'data_nascimento' => 'nullable|date',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
            'status_usuario' => 'nullable|in:ativo,inativo',
        ];
    }
}
