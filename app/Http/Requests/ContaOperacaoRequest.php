<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContaOperacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
    // Make depositante required for deposit operations (teller/counter).
    $depositanteRule = ['sometimes', 'nullable', 'string', 'max:150'];
    // BI requirement: required for levantamentos and pagamentos
    $biRule = ['sometimes', 'nullable', 'string', 'max:50'];
        // Determine route or action: if route name contains 'depositar' or URI contains 'depositar', require depositante
        $routeName = $this->route()?->getName();
        $uri = $this->getRequestUri();
        if (($routeName && str_contains($routeName, 'deposit')) || str_contains($uri, '/depositar')) {
            $depositanteRule = ['required', 'string', 'max:150'];
        }
        // If route indicates levantar or pagar, require BI
        if (($routeName && (str_contains($routeName, 'levantar') || str_contains($routeName, 'pagar'))) || str_contains($uri, '/levantar') || str_contains($uri, '/pagar')) {
            $biRule = ['required', 'string', 'max:50'];
        }

        return [
            'valor' => ['required', 'numeric', 'gt:0'],
            'moeda_id' => ['required', 'integer', 'exists:moedas,id'],
            'descricao' => ['sometimes', 'nullable', 'string', 'max:255'],
            'depositante' => $depositanteRule,
            'referencia_externa' => ['sometimes', 'nullable', 'string', 'max:255'],
            // BI field (may be required depending on route)
            'bi' => $biRule,
        ];
    }
}


