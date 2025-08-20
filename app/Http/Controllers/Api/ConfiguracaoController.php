<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{
    TipoCliente, TipoConta, TipoCartao, TipoSeguro, TipoTransacao,
    StatusCliente, StatusConta, StatusCartao, StatusPagamento,
    StatusSinistro, StatusTransacao, StatusApolice
};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConfiguracaoController extends Controller
{
    public function tipos(): JsonResponse
    {
        return response()->json([
            'tipos_cliente' => TipoCliente::all(),
            'tipos_conta' => TipoConta::all(),
            'tipos_cartao' => TipoCartao::all(),
            'tipos_seguro' => TipoSeguro::all(),
            'tipos_transacao' => TipoTransacao::all(),
        ]);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'status_cliente' => StatusCliente::all(),
            'status_conta' => StatusConta::all(),
            'status_cartao' => StatusCartao::all(),
            'status_pagamento' => StatusPagamento::all(),
            'status_sinistro' => StatusSinistro::all(),
            'status_transacao' => StatusTransacao::all(),
            'status_apolice' => StatusApolice::all(),
        ]);
    }

    public function lookups(): JsonResponse
    {
        return response()->json([
            'tipos' => [
                'cliente' => TipoCliente::all(),
                'conta' => TipoConta::all(),
                'cartao' => TipoCartao::all(),
                'seguro' => TipoSeguro::all(),
                'transacao' => TipoTransacao::all(),
            ],
            'status' => [
                'cliente' => StatusCliente::all(),
                'conta' => StatusConta::all(),
                'cartao' => StatusCartao::all(),
                'pagamento' => StatusPagamento::all(),
                'sinistro' => StatusSinistro::all(),
                'transacao' => StatusTransacao::all(),
                'apolice' => StatusApolice::all(),
            ]
        ]);
    }

    // Tipos - listagem
    public function tiposCliente(): JsonResponse { return response()->json(TipoCliente::all()); }
    public function tiposConta(): JsonResponse { return response()->json(TipoConta::all()); }
    public function tiposCartao(): JsonResponse { return response()->json(TipoCartao::all()); }
    public function tiposSeguro(): JsonResponse { return response()->json(TipoSeguro::all()); }
    public function tiposTransacao(): JsonResponse { return response()->json(TipoTransacao::all()); }

    // Status - listagem
    public function statusCliente(): JsonResponse { return response()->json(StatusCliente::all()); }
    public function statusConta(): JsonResponse { return response()->json(StatusConta::all()); }
    public function statusCartao(): JsonResponse { return response()->json(StatusCartao::all()); }
    public function statusPagamento(): JsonResponse { return response()->json(StatusPagamento::all()); }
    public function statusSinistro(): JsonResponse { return response()->json(StatusSinistro::all()); }
    public function statusTransacao(): JsonResponse { return response()->json(StatusTransacao::all()); }
    public function statusApolice(): JsonResponse { return response()->json(StatusApolice::all()); }

    // Criação mínima existente
    public function storeTipoCliente(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:30', 'unique:tipos_cliente,nome']
        ]);
        $tipo = TipoCliente::create($validated);
        return response()->json(['message' => 'Tipo de cliente criado com sucesso', 'tipo' => $tipo], 201);
    }

    public function storeStatusCliente(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_cliente,nome']
        ]);
        $status = StatusCliente::create($validated);
        return response()->json(['message' => 'Status de cliente criado com sucesso', 'status' => $status], 201);
    }

    // ========== CRUD completo - Tipos ==========
    public function storeTipoConta(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:30', 'unique:tipos_conta,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $tipo = TipoConta::create($validated);
        return response()->json(['message' => 'Tipo de conta criado com sucesso', 'tipo' => $tipo], 201);
    }

    public function showTipoConta(TipoConta $tipoConta): JsonResponse { return response()->json($tipoConta); }

    public function updateTipoConta(Request $request, TipoConta $tipoConta): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:30', 'unique:tipos_conta,nome,' . $tipoConta->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $tipoConta->update($validated);
        return response()->json(['message' => 'Tipo de conta atualizado com sucesso', 'tipo' => $tipoConta]);
    }

    public function destroyTipoConta(TipoConta $tipoConta): JsonResponse
    {
        if ($tipoConta->contas()->exists()) { return response()->json(['message' => 'Não é possível excluir tipo de conta em uso'], 422); }
        $tipoConta->delete();
        return response()->json(['message' => 'Tipo de conta excluído com sucesso']);
    }

    public function storeTipoCartao(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:30', 'unique:tipos_cartao,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $tipo = TipoCartao::create($validated);
        return response()->json(['message' => 'Tipo de cartão criado com sucesso', 'tipo' => $tipo], 201);
    }

    public function showTipoCartao(TipoCartao $tipoCartao): JsonResponse { return response()->json($tipoCartao); }

    public function updateTipoCartao(Request $request, TipoCartao $tipoCartao): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:30', 'unique:tipos_cartao,nome,' . $tipoCartao->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $tipoCartao->update($validated);
        return response()->json(['message' => 'Tipo de cartão atualizado com sucesso', 'tipo' => $tipoCartao]);
    }

    public function destroyTipoCartao(TipoCartao $tipoCartao): JsonResponse
    {
        if ($tipoCartao->cartoes()->exists()) { return response()->json(['message' => 'Não é possível excluir tipo de cartão em uso'], 422); }
        $tipoCartao->delete();
        return response()->json(['message' => 'Tipo de cartão excluído com sucesso']);
    }

    public function storeTipoSeguro(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:50', 'unique:tipos_seguro,nome'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'cobertura' => ['sometimes', 'numeric', 'min:0'],
            'premio_mensal' => ['sometimes', 'numeric', 'min:0']
        ]);
        $tipo = TipoSeguro::create($validated);
        return response()->json(['message' => 'Tipo de seguro criado com sucesso', 'tipo' => $tipo], 201);
    }

    public function showTipoSeguro(TipoSeguro $tipoSeguro): JsonResponse { return response()->json($tipoSeguro); }

    public function updateTipoSeguro(Request $request, TipoSeguro $tipoSeguro): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:50', 'unique:tipos_seguro,nome,' . $tipoSeguro->id],
            'descricao' => ['nullable', 'string', 'max:255'],
            'cobertura' => ['sometimes', 'numeric', 'min:0'],
            'premio_mensal' => ['sometimes', 'numeric', 'min:0']
        ]);
        $tipoSeguro->update($validated);
        return response()->json(['message' => 'Tipo de seguro atualizado com sucesso', 'tipo' => $tipoSeguro]);
    }

    public function destroyTipoSeguro(TipoSeguro $tipoSeguro): JsonResponse
    {
        if ($tipoSeguro->apolices()->exists()) { return response()->json(['message' => 'Não é possível excluir tipo de seguro em uso'], 422); }
        $tipoSeguro->delete();
        return response()->json(['message' => 'Tipo de seguro excluído com sucesso']);
    }

    public function storeTipoTransacao(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:30', 'unique:tipos_transacao,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $tipo = TipoTransacao::create($validated);
        return response()->json(['message' => 'Tipo de transação criado com sucesso', 'tipo' => $tipo], 201);
    }

    public function showTipoTransacao(TipoTransacao $tipoTransacao): JsonResponse { return response()->json($tipoTransacao); }

    public function updateTipoTransacao(Request $request, TipoTransacao $tipoTransacao): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:30', 'unique:tipos_transacao,nome,' . $tipoTransacao->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $tipoTransacao->update($validated);
        return response()->json(['message' => 'Tipo de transação atualizado com sucesso', 'tipo' => $tipoTransacao]);
    }

    public function destroyTipoTransacao(TipoTransacao $tipoTransacao): JsonResponse
    {
        if ($tipoTransacao->transacoes()->exists()) { return response()->json(['message' => 'Não é possível excluir tipo de transação em uso'], 422); }
        $tipoTransacao->delete();
        return response()->json(['message' => 'Tipo de transação excluído com sucesso']);
    }

    public function showTipoCliente(TipoCliente $tipoCliente): JsonResponse { return response()->json($tipoCliente); }

    public function updateTipoCliente(Request $request, TipoCliente $tipoCliente): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:30', 'unique:tipos_cliente,nome,' . $tipoCliente->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $tipoCliente->update($validated);
        return response()->json(['message' => 'Tipo de cliente atualizado com sucesso', 'tipo' => $tipoCliente]);
    }

    public function destroyTipoCliente(TipoCliente $tipoCliente): JsonResponse
    {
        if ($tipoCliente->clientes()->exists()) { return response()->json(['message' => 'Não é possível excluir tipo de cliente em uso'], 422); }
        $tipoCliente->delete();
        return response()->json(['message' => 'Tipo de cliente excluído com sucesso']);
    }

    // ========== CRUD completo - Status ==========
    public function showStatusCliente(StatusCliente $statusCliente): JsonResponse { return response()->json($statusCliente); }

    public function updateStatusCliente(Request $request, StatusCliente $statusCliente): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:20', 'unique:status_cliente,nome,' . $statusCliente->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $statusCliente->update($validated);
        return response()->json(['message' => 'Status de cliente atualizado com sucesso', 'status' => $statusCliente]);
    }

    public function destroyStatusCliente(StatusCliente $statusCliente): JsonResponse
    {
        if ($statusCliente->clientes()->exists()) { return response()->json(['message' => 'Não é possível excluir status de cliente em uso'], 422); }
        $statusCliente->delete();
        return response()->json(['message' => 'Status de cliente excluído com sucesso']);
    }

    public function storeStatusConta(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_conta,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $status = StatusConta::create($validated);
        return response()->json(['message' => 'Status de conta criado com sucesso', 'status' => $status], 201);
    }

    public function showStatusConta(StatusConta $statusConta): JsonResponse { return response()->json($statusConta); }

    public function updateStatusConta(Request $request, StatusConta $statusConta): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:20', 'unique:status_conta,nome,' . $statusConta->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $statusConta->update($validated);
        return response()->json(['message' => 'Status de conta atualizado com sucesso', 'status' => $statusConta]);
    }

    public function destroyStatusConta(StatusConta $statusConta): JsonResponse
    {
        if ($statusConta->contas()->exists()) { return response()->json(['message' => 'Não é possível excluir status de conta em uso'], 422); }
        $statusConta->delete();
        return response()->json(['message' => 'Status de conta excluído com sucesso']);
    }

    public function storeStatusCartao(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_cartao,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $status = StatusCartao::create($validated);
        return response()->json(['message' => 'Status de cartão criado com sucesso', 'status' => $status], 201);
    }

    public function showStatusCartao(StatusCartao $statusCartao): JsonResponse { return response()->json($statusCartao); }

    public function updateStatusCartao(Request $request, StatusCartao $statusCartao): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:20', 'unique:status_cartao,nome,' . $statusCartao->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $statusCartao->update($validated);
        return response()->json(['message' => 'Status de cartão atualizado com sucesso', 'status' => $statusCartao]);
    }

    public function destroyStatusCartao(StatusCartao $statusCartao): JsonResponse
    {
        if ($statusCartao->cartoes()->exists()) { return response()->json(['message' => 'Não é possível excluir status de cartão em uso'], 422); }
        $statusCartao->delete();
        return response()->json(['message' => 'Status de cartão excluído com sucesso']);
    }

    public function storeStatusPagamento(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_pagamento,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $status = StatusPagamento::create($validated);
        return response()->json(['message' => 'Status de pagamento criado com sucesso', 'status' => $status], 201);
    }

    public function showStatusPagamento(StatusPagamento $statusPagamento): JsonResponse { return response()->json($statusPagamento); }

    public function updateStatusPagamento(Request $request, StatusPagamento $statusPagamento): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:20', 'unique:status_pagamento,nome,' . $statusPagamento->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $statusPagamento->update($validated);
        return response()->json(['message' => 'Status de pagamento atualizado com sucesso', 'status' => $statusPagamento]);
    }

    public function destroyStatusPagamento(StatusPagamento $statusPagamento): JsonResponse
    {
        if ($statusPagamento->pagamentos()->exists()) { return response()->json(['message' => 'Não é possível excluir status de pagamento em uso'], 422); }
        $statusPagamento->delete();
        return response()->json(['message' => 'Status de pagamento excluído com sucesso']);
    }

    public function storeStatusSinistro(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_sinistro,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $status = StatusSinistro::create($validated);
        return response()->json(['message' => 'Status de sinistro criado com sucesso', 'status' => $status], 201);
    }

    public function showStatusSinistro(StatusSinistro $statusSinistro): JsonResponse { return response()->json($statusSinistro); }

    public function updateStatusSinistro(Request $request, StatusSinistro $statusSinistro): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:20', 'unique:status_sinistro,nome,' . $statusSinistro->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $statusSinistro->update($validated);
        return response()->json(['message' => 'Status de sinistro atualizado com sucesso', 'status' => $statusSinistro]);
    }

    public function destroyStatusSinistro(StatusSinistro $statusSinistro): JsonResponse
    {
        if ($statusSinistro->sinistros()->exists()) { return response()->json(['message' => 'Não é possível excluir status de sinistro em uso'], 422); }
        $statusSinistro->delete();
        return response()->json(['message' => 'Status de sinistro excluído com sucesso']);
    }

    public function storeStatusTransacao(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_transacao,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $status = StatusTransacao::create($validated);
        return response()->json(['message' => 'Status de transação criado com sucesso', 'status' => $status], 201);
    }

    public function showStatusTransacao(StatusTransacao $statusTransacao): JsonResponse { return response()->json($statusTransacao); }

    public function updateStatusTransacao(Request $request, StatusTransacao $statusTransacao): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:20', 'unique:status_transacao,nome,' . $statusTransacao->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $statusTransacao->update($validated);
        return response()->json(['message' => 'Status de transação atualizado com sucesso', 'status' => $statusTransacao]);
    }

    public function destroyStatusTransacao(StatusTransacao $statusTransacao): JsonResponse
    {
        if ($statusTransacao->transacoes()->exists()) { return response()->json(['message' => 'Não é possível excluir status de transação em uso'], 422); }
        $statusTransacao->delete();
        return response()->json(['message' => 'Status de transação excluído com sucesso']);
    }

    public function storeStatusApolice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_apolice,nome'],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $status = StatusApolice::create($validated);
        return response()->json(['message' => 'Status de apólice criado com sucesso', 'status' => $status], 201);
    }

    public function showStatusApolice(StatusApolice $statusApolice): JsonResponse { return response()->json($statusApolice); }

    public function updateStatusApolice(Request $request, StatusApolice $statusApolice): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:20', 'unique:status_apolice,nome,' . $statusApolice->id],
            'descricao' => ['nullable', 'string', 'max:255']
        ]);
        $statusApolice->update($validated);
        return response()->json(['message' => 'Status de apólice atualizado com sucesso', 'status' => $statusApolice]);
    }

    public function destroyStatusApolice(StatusApolice $statusApolice): JsonResponse
    {
        if ($statusApolice->apolices()->exists()) { return response()->json(['message' => 'Não é possível excluir status de apólice em uso'], 422); }
        $statusApolice->delete();
        return response()->json(['message' => 'Status de apólice excluído com sucesso']);
    }
}
