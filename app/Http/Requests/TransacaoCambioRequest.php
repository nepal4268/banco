<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransacaoCambioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['sometimes', 'nullable', 'integer', 'exists:clientes,id'],
            'conta_origem_id' => ['required', 'integer', 'exists:contas,id'],
            'conta_destino_id' => ['required', 'integer', 'different:conta_origem_id', 'exists:contas,id'],
            'moeda_origem_id' => ['required', 'integer', 'exists:moedas,id'],
            'moeda_destino_id' => ['required', 'integer', 'different:moeda_origem_id', 'exists:moedas,id'],
            'valor_origem' => ['required', 'numeric', 'gt:0'],
            'descricao' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}


