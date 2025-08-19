<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartaoRequest;
use App\Models\Cartao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cartoes",
     *     summary="Listar cartões",
     *     tags={"Cartões"},
     *     @OA\Parameter(name="conta_id", in="query", @OA\Schema(type="integer"), description="Filtrar por conta"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string"), description="Filtrar por status"),
     *     @OA\Response(response=200, description="Lista paginada de cartões")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Cartao::with(['conta.cliente', 'tipoCartao', 'statusCartao']);
        
        if ($request->filled('conta_id')) {
            $query->where('conta_id', $request->conta_id);
        }
        
        if ($request->filled('status')) {
            $query->whereHas('statusCartao', function($q) use ($request) {
                $q->where('nome', $request->status);
            });
        }
        
        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    /**
     * @OA\Post(
     *     path="/api/cartoes",
     *     summary="Criar novo cartão",
     *     tags={"Cartões"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"conta_id", "tipo_cartao_id"},
     *             @OA\Property(property="conta_id", type="integer", example=1),
     *             @OA\Property(property="tipo_cartao_id", type="integer", example=1),
     *             @OA\Property(property="limite", type="number", format="decimal", example=50000.00),
     *             @OA\Property(property="validade", type="string", format="date", example="2027-12-31")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Cartão criado com sucesso")
     * )
     */
    public function store(CartaoRequest $request): JsonResponse
    {
        $cartao = Cartao::create($request->validated());
        return response()->json($cartao->load(['conta.cliente', 'tipoCartao', 'statusCartao']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/cartoes/{id}",
     *     summary="Obter detalhes do cartão",
     *     tags={"Cartões"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalhes do cartão")
     * )
     */
    public function show(Cartao $cartao): JsonResponse
    {
        return response()->json($cartao->load(['conta.cliente', 'tipoCartao', 'statusCartao']));
    }

    /**
     * @OA\Put(
     *     path="/api/cartoes/{id}",
     *     summary="Atualizar cartão",
     *     tags={"Cartões"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="limite", type="number", format="decimal", example=75000.00),
     *             @OA\Property(property="status_cartao_id", type="integer", example=2),
     *             @OA\Property(property="validade", type="string", format="date", example="2028-12-31")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cartão atualizado com sucesso")
     * )
     */
    public function update(CartaoRequest $request, Cartao $cartao): JsonResponse
    {
        $cartao->update($request->validated());
        return response()->json($cartao->load(['conta.cliente', 'tipoCartao', 'statusCartao']));
    }

    /**
     * @OA\Delete(
     *     path="/api/cartoes/{id}",
     *     summary="Excluir cartão",
     *     tags={"Cartões"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Cartão excluído com sucesso")
     * )
     */
    public function destroy(Cartao $cartao): JsonResponse
    {
        $cartao->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/api/cartoes/{id}/bloquear",
     *     summary="Bloquear cartão",
     *     tags={"Cartões"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="motivo", type="string", example="Solicitação do cliente")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cartão bloqueado com sucesso")
     * )
     */
    public function bloquear(Request $request, Cartao $cartao): JsonResponse
    {
        $request->validate(['motivo' => 'nullable|string|max:255']);
        
        $statusBloqueado = \App\Models\StatusCartao::where('nome', 'Bloqueado')->first();
        if (!$statusBloqueado) {
            return response()->json(['error' => 'Status "Bloqueado" não encontrado'], 400);
        }
        
        $cartao->update(['status_cartao_id' => $statusBloqueado->id]);
        
        return response()->json([
            'message' => 'Cartão bloqueado com sucesso',
            'cartao' => $cartao->load(['conta.cliente', 'tipoCartao', 'statusCartao'])
        ]);
    }
}