<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Cliente;


class ContaStoreRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clienteId = $this->input('cliente_id');
            $tipoId = $this->input('tipo_conta_id');
            $moedaId = $this->input('moeda_id');

            if (!$clienteId || !$tipoId || !$moedaId) return;

            $cliente = Cliente::with('tipoCliente')->find($clienteId);
            if (!$cliente) return;

            // Heuristic: only enforce for pessoa fisica
            $isPessoaFisica = false;
            if ($cliente->tipoCliente && isset($cliente->tipoCliente->nome)) {
                $nomeTipo = mb_strtolower($cliente->tipoCliente->nome);
                if (str_contains($nomeTipo, 'fisic') || str_contains($nomeTipo, 'pessoa')) {
                    $isPessoaFisica = true;
                }
            }

            if (!$isPessoaFisica) return;

            // Check for existing active account with same tipo and moeda
            $exists = $cliente->contas()
                ->where('tipo_conta_id', $tipoId)
                ->where('moeda_id', $moedaId)
                ->whereHas('statusConta', function($q){
                    $q->where('nome', 'ativa');
                })->exists();

            if ($exists) {
                $validator->errors()->add('moeda_id', 'O cliente já possui uma conta deste tipo nesta moeda. Escolha outra moeda ou tipo.');
            }
        });
    }

    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_conta_id' => 'required|exists:tipos_conta,id',
            'status_conta_id' => 'required|exists:status_conta,id',
            'moeda_id' => 'required|exists:moedas,id',
            'saldo_inicial' => 'required|numeric|min:0',
            'limite_credito' => 'nullable|numeric|min:0',
            'numero_conta' => 'nullable|string|unique:contas,numero_conta',
            'iban' => 'nullable|string|unique:contas,iban',
            'agencia_id' => 'nullable|exists:agencias,id',
        ];
    }
}
