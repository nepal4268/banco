<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartaoRequest extends FormRequest
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
        $cartaoId = $this->route('cartao') ? $this->route('cartao')->id : null;

    // normalize incoming card number for validation (strip non-digits)
    // this helps when the UI sends a masked value with spaces
    // prepareForValidation ensures rules/closures see the cleaned value
    // Note: FormRequest has prepareForValidation lifecycle method, but we'll also ensure here

        return [
            'conta_id' => ['required', 'exists:contas,id'],
            'tipo_cartao_id' => ['required', 'exists:tipos_cartao,id'],
            'numero_cartao' => [
                'required',
                'string',
                // Removemos a validação de max aqui porque o campo persistido é criptografado
                function ($attribute, $value, $fail) use ($cartaoId) {
                    $hash = hash('sha256', $value);
                    $uniqueQuery = \App\Models\Cartao::where('numero_cartao_hash', $hash);
                    if ($cartaoId) {
                        $uniqueQuery->where('id', '!=', $cartaoId);
                    }
                    if ($uniqueQuery->exists()) {
                        $fail('Este número de cartão já existe.');
                    }
                }
            ],
            'validade' => ['required', 'date', 'after:today'],
            'status_cartao_id' => ['required', 'exists:status_cartao,id'],
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
            'tipo_cartao_id.required' => 'O tipo de cartão é obrigatório.',
            'tipo_cartao_id.exists' => 'O tipo de cartão selecionado é inválido.',
            'numero_cartao.required' => 'O número do cartão é obrigatório.',
            'numero_cartao.unique' => 'Este número de cartão já existe.',
            'numero_cartao.max' => 'O número do cartão não pode ter mais de 25 caracteres.',
            'validade.required' => 'A data de validade é obrigatória.',
            'validade.date' => 'A data de validade deve ser uma data válida.',
            'validade.after' => 'A data de validade deve ser posterior à data atual.',
            'status_cartao_id.required' => 'O status do cartão é obrigatório.',
            'status_cartao_id.exists' => 'O status do cartão selecionado é inválido.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('numero_cartao')) {
            $clean = preg_replace('/\D/', '', $this->input('numero_cartao'));
            $this->merge(['numero_cartao' => $clean]);
        }
        // also accept numero_cartao_clean if the UI provides it
        if ($this->has('numero_cartao_clean')) {
            $this->merge(['numero_cartao' => preg_replace('/\D/', '', $this->input('numero_cartao_clean'))]);
        }
    }
}