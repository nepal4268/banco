<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApoliceRequest extends FormRequest
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
        $apoliceId = $this->route('apolice') ? $this->route('apolice')->id : null;

        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'tipo_seguro_id' => ['required', 'exists:tipos_seguro,id'],
            'numero_apolice' => [
                'required',
                'string',
                'max:50',
                Rule::unique('apolices', 'numero_apolice')->ignore($apoliceId)
            ],
            'inicio_vigencia' => ['required', 'date'],
            'fim_vigencia' => ['required', 'date', 'after:inicio_vigencia'],
            'status_apolice_id' => ['required', 'exists:status_apolice,id'],
            'premio_mensal' => ['required', 'numeric', 'min:0.01', 'max:999999999999999999.99'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cliente_id.required' => 'O cliente é obrigatório.',
            'cliente_id.exists' => 'O cliente selecionado é inválido.',
            'tipo_seguro_id.required' => 'O tipo de seguro é obrigatório.',
            'tipo_seguro_id.exists' => 'O tipo de seguro selecionado é inválido.',
            'numero_apolice.required' => 'O número da apólice é obrigatório.',
            'numero_apolice.unique' => 'Este número de apólice já existe.',
            'numero_apolice.max' => 'O número da apólice não pode ter mais de 50 caracteres.',
            'inicio_vigencia.required' => 'A data de início de vigência é obrigatória.',
            'inicio_vigencia.date' => 'A data de início de vigência deve ser uma data válida.',
            'fim_vigencia.required' => 'A data de fim de vigência é obrigatória.',
            'fim_vigencia.date' => 'A data de fim de vigência deve ser uma data válida.',
            'fim_vigencia.after' => 'A data de fim de vigência deve ser posterior à data de início.',
            'status_apolice_id.required' => 'O status da apólice é obrigatório.',
            'status_apolice_id.exists' => 'O status da apólice selecionado é inválido.',
            'premio_mensal.required' => 'O prêmio mensal é obrigatório.',
            'premio_mensal.numeric' => 'O prêmio mensal deve ser numérico.',
            'premio_mensal.min' => 'O prêmio mensal deve ser maior que zero.',
            'premio_mensal.max' => 'O prêmio mensal excede o limite máximo permitido.',
        ];
    }
}