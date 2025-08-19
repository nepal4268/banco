<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'conta_id' => ['required', 'exists:contas,id'],
            'parceiro' => ['required', 'string', 'max:100'],
            'referencia' => ['required', 'string', 'max:100'],
            'valor' => ['required', 'numeric', 'min:0.01', 'max:999999999999999999.99'],
            'moeda_id' => ['required', 'exists:moedas,id'],
            'data_pagamento' => ['nullable', 'date'],
            'status_pagamento_id' => ['required', 'exists:status_pagamento,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'conta_id.required' => 'A conta é obrigatória.',
            'conta_id.exists' => 'A conta selecionada é inválida.',
            'parceiro.required' => 'O parceiro é obrigatório.',
            'parceiro.max' => 'O parceiro não pode ter mais de 100 caracteres.',
            'referencia.required' => 'A referência é obrigatória.',
            'referencia.max' => 'A referência não pode ter mais de 100 caracteres.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'valor.max' => 'O valor excede o limite máximo permitido.',
            'moeda_id.required' => 'A moeda é obrigatória.',
            'moeda_id.exists' => 'A moeda selecionada é inválida.',
            'data_pagamento.date' => 'A data de pagamento deve ser uma data válida.',
            'status_pagamento_id.required' => 'O status do pagamento é obrigatório.',
            'status_pagamento_id.exists' => 'O status do pagamento selecionado é inválido.',
        ];
    }
}