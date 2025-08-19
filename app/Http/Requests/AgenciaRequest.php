<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgenciaRequest extends FormRequest
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
        $agenciaId = $this->route('agencia') ? $this->route('agencia')->id : null;

        return [
            'codigo_banco' => ['required', 'string', 'size:4'],
            'codigo_agencia' => [
                'required',
                'string',
                'size:4',
                Rule::unique('agencias', 'codigo_agencia')->ignore($agenciaId)
            ],
            'nome' => ['required', 'string', 'max:100'],
            'endereco' => ['required', 'string', 'max:255'],
            'telefones' => ['nullable', 'array'],
            'telefones.*' => ['string', 'regex:/^[0-9]{9}$/', 'distinct'],
            'email' => ['nullable', 'email', 'max:100'],
            'ativa' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'codigo_banco.required' => 'O código do banco é obrigatório.',
            'codigo_banco.size' => 'O código do banco deve ter exatamente 4 dígitos.',
            'codigo_agencia.required' => 'O código da agência é obrigatório.',
            'codigo_agencia.size' => 'O código da agência deve ter exatamente 4 dígitos.',
            'codigo_agencia.unique' => 'Este código de agência já existe.',
            'nome.required' => 'O nome da agência é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 100 caracteres.',
            'endereco.required' => 'O endereço é obrigatório.',
            'endereco.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'telefones.array' => 'Os telefones devem ser fornecidos como uma lista.',
            'telefones.*.regex' => 'O telefone deve ter exatamente 9 dígitos (formato: 930202034).',
            'telefones.*.distinct' => 'Não é possível ter telefones duplicados.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.max' => 'O email não pode ter mais de 100 caracteres.',
            'ativa.boolean' => 'O status ativo deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Garantir que telefones seja um array se não for fornecido
        if (!$this->has('telefones') || is_null($this->telefones)) {
            $this->merge(['telefones' => []]);
        }

        // Garantir que ativa seja boolean
        if ($this->has('ativa')) {
            $this->merge(['ativa' => filter_var($this->ativa, FILTER_VALIDATE_BOOLEAN)]);
        }
    }
}