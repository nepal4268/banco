<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RelatorioTransacoesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_inicio' => ['sometimes', 'date'],
            'data_fim' => ['sometimes', 'date', 'after_or_equal:data_inicio'],
            'tipo' => ['sometimes', 'string', 'max:50'],
            'moeda' => ['sometimes', 'string', 'max:10'],
            'per_page' => ['sometimes', 'integer', 'between:1,200'],
        ];
    }
}


