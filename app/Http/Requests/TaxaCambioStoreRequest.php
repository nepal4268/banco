<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxaCambioStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'moeda_origem_id' => ['required', 'integer', 'exists:moedas,id'],
            'moeda_destino_id' => ['required', 'integer', 'different:moeda_origem_id', 'exists:moedas,id'],
            'taxa_compra' => ['required', 'numeric', 'gt:0'],
            'taxa_venda' => ['required', 'numeric', 'gt:0'],
            'ativa' => ['sometimes', 'boolean'],
        ];
    }
}


