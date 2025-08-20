<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
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
        $clienteId = $this->route('cliente') ? ($this->route('cliente')->id ?? $this->route('cliente')) : null;

        $requiredOrSometimes = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'nome' => [$requiredOrSometimes, 'string', 'max:100'],
            // Migration usa enum('M','F'); alinhar validação para valores aceitos pelo banco
            'sexo' => [$requiredOrSometimes, 'in:M,F'],
            'bi' => [
                $requiredOrSometimes,
                'string',
                'max:25',
                'regex:/^\d{9}[A-Z]{2}\d{3}$/',
                Rule::unique('clientes', 'bi')->ignore($clienteId)
            ],
            'email' => ['nullable', 'email', Rule::unique('clientes', 'email')->ignore($clienteId)],
            'tipo_cliente_id' => [$requiredOrSometimes, 'exists:tipos_cliente,id'],
            'status_cliente_id' => [$requiredOrSometimes, 'exists:status_cliente,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'sexo.required' => 'O sexo é obrigatório.',
            'sexo.in' => 'O sexo deve ser M ou F.',
            'bi.required' => 'O BI é obrigatório.',
            'bi.unique' => 'Este BI já está cadastrado.',
            'bi.max' => 'O BI não pode ter mais de 25 caracteres.',
            'bi.regex' => 'O BI deve seguir o formato angolano: 9 dígitos, 2 letras maiúsculas, 3 dígitos (ex: 123456789AZ123).',
            'tipo_cliente_id.required' => 'O tipo de cliente é obrigatório.',
            'tipo_cliente_id.exists' => 'O tipo de cliente selecionado é inválido.',
            'status_cliente_id.required' => 'O status do cliente é obrigatório.',
            'status_cliente_id.exists' => 'O status do cliente selecionado é inválido.',
        ];
    }
}