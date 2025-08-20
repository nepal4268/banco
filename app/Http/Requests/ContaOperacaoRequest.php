<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContaOperacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor' => ['required', 'numeric', 'gt:0'],
            'moeda_id' => ['required', 'integer', 'exists:moedas,id'],
            'descricao' => ['sometimes', 'nullable', 'string', 'max:255'],
            'referencia_externa' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}


