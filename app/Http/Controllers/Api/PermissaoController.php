<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permissao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PermissaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/permissoes",
     *     summary="Listar permissões",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="grupo", in="query", @OA\Schema(type="string"), description="Filtrar por grupo"),
     *     @OA\Response(response=200, description="Lista de permissões")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Permissao::query();
        
        if ($request->filled('grupo')) {
            $query->where('grupo', $request->grupo);
        }
        
        $permissoes = $query->orderBy('grupo')->orderBy('nome')->get();
        
        // Agrupar por grupo para melhor organização
        $agrupadas = $permissoes->groupBy('grupo');
        
        return response()->json([
            'permissoes' => $permissoes,
            'agrupadas' => $agrupadas
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/permissoes",
     *     summary="Criar permissão",
     *     tags={"Gestão de Usuários"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "code", "grupo"},
     *             @OA\Property(property="nome", type="string", maxLength=50, example="Criar Cliente"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="cliente.create"),
     *             @OA\Property(property="descricao", type="string", example="Permite criar novos clientes"),
     *             @OA\Property(property="grupo", type="string", maxLength=30, example="Clientes")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Permissão criada")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:50', 'unique:permissoes,code'],
            'descricao' => ['nullable', 'string'],
            'grupo' => ['required', 'string', 'max:30'],
        ]);

        $permissao = Permissao::create($validated);
        
        return response()->json([
            'message' => 'Permissão criada com sucesso',
            'permissao' => $permissao
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/permissoes/{id}",
     *     summary="Obter permissão",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Permissão")
     * )
     */
    public function show(Permissao $permissao): JsonResponse
    {
        return response()->json($permissao);
    }

    /**
     * @OA\Put(
     *     path="/api/permissoes/{id}",
     *     summary="Atualizar permissão",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="nome", type="string", maxLength=50),
     *         @OA\Property(property="code", type="string", maxLength=50),
     *         @OA\Property(property="descricao", type="string"),
     *         @OA\Property(property="grupo", type="string", maxLength=30)
     *     )),
     *     @OA\Response(response=200, description="Permissão atualizada")
     * )
     */
    public function update(Request $request, Permissao $permissao): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:50'],
            'code' => ['sometimes', 'string', 'max:50', 'unique:permissoes,code,' . $permissao->id],
            'descricao' => ['nullable', 'string'],
            'grupo' => ['sometimes', 'string', 'max:30'],
        ]);

        $permissao->update($validated);
        
        return response()->json([
            'message' => 'Permissão atualizada com sucesso',
            'permissao' => $permissao
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/permissoes/{id}",
     *     summary="Excluir permissão",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Permissão excluída")
     * )
     */
    public function destroy(Permissao $permissao): JsonResponse
    {
        $permissao->delete();
        
        return response()->json([
            'message' => 'Permissão excluída com sucesso'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/permissoes/grupos",
     *     summary="Listar grupos de permissões",
     *     tags={"Gestão de Usuários"},
     *     @OA\Response(response=200, description="Lista de grupos")
     * )
     */
    public function grupos(): JsonResponse
    {
        $grupos = Permissao::distinct('grupo')->orderBy('grupo')->pluck('grupo');
        
        return response()->json(['grupos' => $grupos]);
    }
}