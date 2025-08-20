<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use App\Models\Conta;
use App\Models\StatusPagamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PagamentoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/pagamentos",
     *     summary="Listar pagamentos",
     *     tags={"Pagamentos"},
     *     @OA\Parameter(name="conta_id", in="query", @OA\Schema(type="integer"), description="Filtrar por conta"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string"), description="Filtrar por status"),
     *     @OA\Parameter(name="parceiro", in="query", @OA\Schema(type="string"), description="Filtrar por parceiro"),
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date"), description="Data início"),
     *     @OA\Parameter(name="data_fim", in="query", @OA\Schema(type="string", format="date"), description="Data fim"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Itens por página"),
     *     @OA\Response(response=200, description="Lista de pagamentos")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pagamento::with(['conta.cliente', 'moeda', 'statusPagamento']);
        
        if ($request->filled('conta_id')) {
            $query->where('conta_id', $request->conta_id);
        }
        
        if ($request->filled('status')) {
            $query->whereHas('statusPagamento', function($q) use ($request) {
                $q->where('nome', $request->status);
            });
        }
        
        if ($request->filled('parceiro')) {
            $query->where('parceiro', 'like', '%' . $request->parceiro . '%');
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_pagamento', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('data_pagamento', '<=', $request->data_fim);
        }
        
        $perPage = $request->get('per_page', 15);
        return response()->json($query->orderBy('data_pagamento', 'desc')->paginate($perPage));
    }

    /**
     * @OA\Post(
     *     path="/api/pagamentos",
     *     summary="Criar pagamento",
     *     tags={"Pagamentos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"conta_id", "parceiro", "referencia", "valor", "moeda_id"},
     *             @OA\Property(property="conta_id", type="integer", example=1),
     *             @OA\Property(property="parceiro", type="string", maxLength=100, example="UNITEL"),
     *             @OA\Property(property="referencia", type="string", maxLength=100, example="244912345678"),
     *             @OA\Property(property="valor", type="number", format="decimal", example=50.00),
     *             @OA\Property(property="moeda_id", type="integer", example=1),
     *             @OA\Property(property="data_pagamento", type="string", format="datetime", example="2025-01-15 14:30:00"),
     *             @OA\Property(property="status_pagamento_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Pagamento criado")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conta_id' => ['required', 'integer', 'exists:contas,id'],
            'parceiro' => ['required', 'string', 'max:100'],
            'referencia' => ['required', 'string', 'max:100'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'moeda_id' => ['required', 'integer', 'exists:moedas,id'],
            'data_pagamento' => ['sometimes', 'date'],
            'status_pagamento_id' => ['sometimes', 'integer', 'exists:status_pagamento,id'],
        ]);

        // Define data atual se não fornecida
        if (!isset($validated['data_pagamento'])) {
            $validated['data_pagamento'] = now();
        }

        // Define status padrão se não fornecido
        if (!isset($validated['status_pagamento_id'])) {
            $statusPendente = StatusPagamento::where('nome', 'Pendente')->first();
            if ($statusPendente) {
                $validated['status_pagamento_id'] = $statusPendente->id;
            }
        }

        $pagamento = Pagamento::create($validated);
        $pagamento->load(['conta.cliente', 'moeda', 'statusPagamento']);
        
        return response()->json([
            'message' => 'Pagamento criado com sucesso',
            'pagamento' => $pagamento
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/pagamentos/{id}",
     *     summary="Obter pagamento",
     *     tags={"Pagamentos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Pagamento")
     * )
     */
    public function show(Pagamento $pagamento): JsonResponse
    {
        $pagamento->load(['conta.cliente', 'moeda', 'statusPagamento']);
        return response()->json($pagamento);
    }

    /**
     * @OA\Put(
     *     path="/api/pagamentos/{id}",
     *     summary="Atualizar pagamento",
     *     tags={"Pagamentos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="parceiro", type="string", maxLength=100),
     *         @OA\Property(property="referencia", type="string", maxLength=100),
     *         @OA\Property(property="valor", type="number", format="decimal"),
     *         @OA\Property(property="status_pagamento_id", type="integer"),
     *         @OA\Property(property="data_pagamento", type="string", format="datetime")
     *     )),
     *     @OA\Response(response=200, description="Pagamento atualizado")
     * )
     */
    public function update(Request $request, Pagamento $pagamento): JsonResponse
    {
        $validated = $request->validate([
            'parceiro' => ['sometimes', 'string', 'max:100'],
            'referencia' => ['sometimes', 'string', 'max:100'],
            'valor' => ['sometimes', 'numeric', 'min:0.01'],
            'status_pagamento_id' => ['sometimes', 'integer', 'exists:status_pagamento,id'],
            'data_pagamento' => ['sometimes', 'date'],
        ]);

        $pagamento->update($validated);
        $pagamento->load(['conta.cliente', 'moeda', 'statusPagamento']);
        
        return response()->json([
            'message' => 'Pagamento atualizado com sucesso',
            'pagamento' => $pagamento
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/pagamentos/{id}",
     *     summary="Excluir pagamento",
     *     tags={"Pagamentos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Pagamento excluído")
     * )
     */
    public function destroy(Pagamento $pagamento): JsonResponse
    {
        // Verificar se o pagamento pode ser excluído (por exemplo, se não está processado)
        if ($pagamento->statusPagamento && $pagamento->statusPagamento->nome === 'Processado') {
            return response()->json([
                'message' => 'Não é possível excluir pagamento já processado'
            ], 422);
        }

        $pagamento->delete();
        
        return response()->json([
            'message' => 'Pagamento excluído com sucesso'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/pagamentos/{id}/processar",
     *     summary="Processar pagamento",
     *     tags={"Pagamentos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Pagamento processado")
     * )
     */
    public function processar(Pagamento $pagamento): JsonResponse
    {
        if ($pagamento->statusPagamento && $pagamento->statusPagamento->nome === 'Processado') {
            return response()->json([
                'message' => 'Pagamento já foi processado'
            ], 422);
        }

        $statusProcessado = StatusPagamento::where('nome', 'Processado')->first();
        if (!$statusProcessado) {
            return response()->json([
                'message' => 'Status "Processado" não encontrado'
            ], 400);
        }

        $pagamento->update([
            'status_pagamento_id' => $statusProcessado->id,
            'data_pagamento' => now()
        ]);

        return response()->json([
            'message' => 'Pagamento processado com sucesso',
            'pagamento' => $pagamento->load(['conta.cliente', 'moeda', 'statusPagamento'])
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/pagamentos/{id}/cancelar",
     *     summary="Cancelar pagamento",
     *     tags={"Pagamentos"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Pagamento cancelado")
     * )
     */
    public function cancelar(Pagamento $pagamento): JsonResponse
    {
        if ($pagamento->statusPagamento && $pagamento->statusPagamento->nome === 'Processado') {
            return response()->json([
                'message' => 'Não é possível cancelar pagamento já processado'
            ], 422);
        }

        $statusCancelado = StatusPagamento::where('nome', 'Cancelado')->first();
        if (!$statusCancelado) {
            return response()->json([
                'message' => 'Status "Cancelado" não encontrado'
            ], 400);
        }

        $pagamento->update(['status_pagamento_id' => $statusCancelado->id]);

        return response()->json([
            'message' => 'Pagamento cancelado com sucesso',
            'pagamento' => $pagamento->load(['conta.cliente', 'moeda', 'statusPagamento'])
        ]);
    }
}