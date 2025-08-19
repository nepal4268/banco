<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferenciaExternaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conta_origem_id' => ['nullable', 'integer', 'exists:contas,id'],
            'conta_destino_id' => ['nullable', 'integer', 'exists:contas,id'],
            'origem_externa' => ['sometimes', 'boolean'],
            'destino_externa' => ['sometimes', 'boolean'],
            'conta_externa_origem' => ['nullable', 'string', 'max:64'],
            'banco_externo_origem' => ['nullable', 'string', 'max:100'],
            'conta_externa_destino' => ['nullable', 'string', 'max:64'],
            'banco_externo_destino' => ['nullable', 'string', 'max:100'],
            'valor' => ['required', 'numeric', 'min:0.01', 'max:999999999999999999.99'],
            'moeda_id' => ['required', 'integer', 'exists:moedas,id'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'referencia_externa' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $origemExterna = $this->boolean('origem_externa');
            $destinoExterna = $this->boolean('destino_externa');

            // Pelo menos um lado deve ser interno
            if ($origemExterna && $destinoExterna) {
                $validator->errors()->add('destino_externa', 'Pelo menos uma das pontas deve ser interna.');
            }

            // Se origem é externa, exigir campos externos de origem
            if ($origemExterna) {
                if (!$this->conta_externa_origem || !$this->banco_externo_origem) {
                    $validator->errors()->add('conta_externa_origem', 'Informe dados da origem externa.');
                }
            } else {
                if (!$this->conta_origem_id && !$destinoExterna) {
                    $validator->errors()->add('conta_origem_id', 'Informe a conta de origem interna.');
                }
            }

            // Se destino é externo, exigir campos externos de destino
            if ($destinoExterna) {
                if (!$this->conta_externa_destino || !$this->banco_externo_destino) {
                    $validator->errors()->add('conta_externa_destino', 'Informe dados do destino externo.');
                }
            } else {
                if (!$this->conta_destino_id && !$origemExterna) {
                    $validator->errors()->add('conta_destino_id', 'Informe a conta de destino interna.');
                }
            }
        });
    }
}

