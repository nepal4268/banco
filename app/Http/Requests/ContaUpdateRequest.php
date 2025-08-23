<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContaUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_conta_id' => 'required|exists:tipos_conta,id',
            'status_conta_id' => 'required|exists:status_conta,id',
            'moeda_id' => 'required|exists:moedas,id',
            'limite_credito' => 'nullable|numeric|min:0',
        ];
    }
}
