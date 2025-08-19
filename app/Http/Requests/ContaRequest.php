<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContaRequest extends FormRequest
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
        $contaId = $this->route('conta') ? $this->route('conta')->id : null;
        $isUpdate = !is_null($contaId);

        $rules = [
            'tipo_conta_id' => ['required', 'exists:tipos_conta,id'],
            'moeda_id' => ['required', 'exists:moedas,id'],
            'saldo' => ['nullable', 'numeric', 'min:0', 'max:999999999999999999.99'],
            'status_conta_id' => ['required', 'exists:status_conta,id'],
        ];

        if ($isUpdate) {
            // Em atualização: campos imutáveis são proibidos
            $rules['cliente_id'] = ['prohibited'];
            $rules['agencia_id'] = ['prohibited'];
            $rules['numero_conta'] = ['prohibited'];
            $rules['iban'] = ['prohibited'];
        } else {
            // Em criação: cliente e agência são obrigatórios; numero/iban são gerados
            $rules['cliente_id'] = ['required', 'exists:clientes,id'];
            $rules['agencia_id'] = ['required', 'exists:agencias,id'];
            $rules['numero_conta'] = ['prohibited'];
            $rules['iban'] = ['prohibited'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cliente_id.required' => 'O cliente é obrigatório.',
            'cliente_id.exists' => 'O cliente selecionado é inválido.',
            'agencia_id.required' => 'A agência é obrigatória.',
            'agencia_id.exists' => 'A agência selecionada é inválida.',
            'tipo_conta_id.required' => 'O tipo de conta é obrigatório.',
            'tipo_conta_id.exists' => 'O tipo de conta selecionado é inválido.',
            'moeda_id.required' => 'A moeda é obrigatória.',
            'moeda_id.exists' => 'A moeda selecionada é inválida.',
            'saldo.numeric' => 'O saldo deve ser um valor numérico.',
            'saldo.min' => 'O saldo não pode ser negativo.',
            'saldo.max' => 'O saldo excede o limite máximo permitido.',
            'status_conta_id.required' => 'O status da conta é obrigatório.',
            'status_conta_id.exists' => 'O status da conta selecionado é inválido.',
            'numero_conta.unique' => 'Este número de conta já existe.',
            'numero_conta.max' => 'O número da conta não pode ter mais de 20 caracteres.',
            'iban.unique' => 'Este IBAN já existe.',
            'iban.max' => 'O IBAN não pode ter mais de 34 caracteres.',
        ];
    }
}