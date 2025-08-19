<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conta_origem_id' => ['nullable', 'exists:contas,id'],
            'origem_externa' => ['boolean'],
            'conta_externa_origem' => ['nullable', 'string', 'max:64'],
            'banco_externo_origem' => ['nullable', 'string', 'max:100'],

            'conta_destino_id' => ['nullable', 'exists:contas,id'],
            'destino_externa' => ['boolean'],
            'conta_externa_destino' => ['nullable', 'string', 'max:64'],
            'banco_externo_destino' => ['nullable', 'string', 'max:100'],

            'tipo_transacao_id' => ['required', 'exists:tipos_transacao,id'],
            'valor' => ['required', 'numeric', 'min:0.01', 'max:999999999999999999.99'],
            'moeda_id' => ['required', 'exists:moedas,id'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'referencia_externa' => ['nullable', 'string', 'max:100'],
            'status_transacao_id' => ['required', 'exists:status_transacao,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'conta_origem_id.exists' => 'A conta de origem selecionada é inválida.',
            'conta_destino_id.exists' => 'A conta de destino selecionada é inválida.',
            'tipo_transacao_id.required' => 'O tipo de transação é obrigatório.',
            'tipo_transacao_id.exists' => 'O tipo de transação selecionado é inválido.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser numérico.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'valor.max' => 'O valor excede o limite máximo permitido.',
            'moeda_id.required' => 'A moeda é obrigatória.',
            'moeda_id.exists' => 'A moeda selecionada é inválida.',
            'descricao.max' => 'A descrição não pode ter mais de 255 caracteres.',
            'referencia_externa.max' => 'A referência externa não pode ter mais de 100 caracteres.',
            'status_transacao_id.required' => 'O status da transação é obrigatório.',
            'status_transacao_id.exists' => 'O status da transação selecionado é inválido.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $origemInformada = $this->conta_origem_id || ($this->boolean('origem_externa') && $this->conta_externa_origem && $this->banco_externo_origem);
            $destinoInformado = $this->conta_destino_id || ($this->boolean('destino_externa') && $this->conta_externa_destino && $this->banco_externo_destino);

            if (!$origemInformada && !$destinoInformado) {
                $validator->errors()->add('conta_origem_id', 'Informe origem ou destino, interno ou externo.');
            }

            if ($this->conta_origem_id && $this->conta_destino_id && $this->conta_origem_id == $this->conta_destino_id) {
                $validator->errors()->add('conta_destino_id', 'A conta de destino deve ser diferente da conta de origem.');
            }
        });
    }
}