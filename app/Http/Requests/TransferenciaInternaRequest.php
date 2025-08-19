<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferenciaInternaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conta_origem_id' => ['required', 'integer', 'exists:contas,id'],
            'conta_destino_id' => ['nullable', 'integer', 'exists:contas,id'],
            'iban_destino' => ['nullable', 'string', 'max:34'],
            'valor' => ['required', 'numeric', 'min:0.01', 'max:999999999999999999.99'],
            'moeda_id' => ['required', 'integer', 'exists:moedas,id'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->conta_destino_id && $this->conta_origem_id == $this->conta_destino_id) {
                $validator->errors()->add('conta_destino_id', 'A conta de destino deve ser diferente da conta de origem.');
            }

            // Exigir um dos campos destino: conta_destino_id ou iban_destino
            if (!$this->conta_destino_id && empty($this->iban_destino)) {
                $validator->errors()->add('conta_destino_id', 'Informe "conta_destino_id" ou "iban_destino".');
            }

            // Não permitir campos de contas externas neste endpoint
            $externos = ['origem_externa','destino_externa','conta_externa_origem','banco_externo_origem','conta_externa_destino','banco_externo_destino'];
            foreach ($externos as $campo) {
                if ($this->has($campo)) {
                    $validator->errors()->add($campo, 'Campo não permitido para transferência interna.');
                }
            }
        });
    }
}

