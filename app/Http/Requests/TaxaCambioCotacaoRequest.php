<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxaCambioCotacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'moeda_origem' => ['required', 'string', 'max:10'],
            'moeda_destino' => ['required', 'string', 'max:10'],
            'valor' => ['sometimes', 'nullable', 'numeric', 'gt:0'],
        ];
    }
}


