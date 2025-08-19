<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SinistroRequest extends FormRequest
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
            'apolice_id' => ['required', 'exists:apolices,id'],
            'descricao' => ['nullable', 'string'],
            'valor_reivindicado' => ['required', 'numeric', 'min:0.01', 'max:999999999999999999.99'],
            'valor_pago' => ['nullable', 'numeric', 'min:0', 'max:999999999999999999.99'],
            'data_sinistro' => ['required', 'date', 'before_or_equal:today'],
            'status_sinistro_id' => ['required', 'exists:status_sinistro,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'apolice_id.required' => 'A apólice é obrigatória.',
            'apolice_id.exists' => 'A apólice selecionada é inválida.',
            'valor_reivindicado.required' => 'O valor reivindicado é obrigatório.',
            'valor_reivindicado.numeric' => 'O valor reivindicado deve ser numérico.',
            'valor_reivindicado.min' => 'O valor reivindicado deve ser maior que zero.',
            'valor_reivindicado.max' => 'O valor reivindicado excede o limite máximo permitido.',
            'valor_pago.numeric' => 'O valor pago deve ser numérico.',
            'valor_pago.min' => 'O valor pago não pode ser negativo.',
            'valor_pago.max' => 'O valor pago excede o limite máximo permitido.',
            'data_sinistro.required' => 'A data do sinistro é obrigatória.',
            'data_sinistro.date' => 'A data do sinistro deve ser uma data válida.',
            'data_sinistro.before_or_equal' => 'A data do sinistro não pode ser futura.',
            'status_sinistro_id.required' => 'O status do sinistro é obrigatório.',
            'status_sinistro_id.exists' => 'O status do sinistro selecionado é inválido.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validação customizada: valor pago não pode ser maior que o reivindicado
            if ($this->valor_pago && $this->valor_reivindicado && 
                $this->valor_pago > $this->valor_reivindicado) {
                $validator->errors()->add('valor_pago', 'O valor pago não pode ser maior que o valor reivindicado.');
            }
        });
    }
}