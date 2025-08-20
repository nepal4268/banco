<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perfil;
use App\Models\Permissao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PerfilController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/perfis",
     *     summary="Listar perfis de usuário",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Itens por página"),
     *     @OA\Response(response=200, description="Lista de perfis")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Perfil::with(['permissoes']);
        
        $perPage = $request->get('per_page', 15);
        $perfis = $query->paginate($perPage);
        
        return response()->json($perfis);
    }

    /**
     * @OA\Post(
     *     path="/api/perfis",
     *     summary="Criar perfil",
     *     tags={"Gestão de Usuários"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", maxLength=50, example="Gerente"),
     *             @OA\Property(property="descricao", type="string", example="Perfil para gerentes de agência"),
     *             @OA\Property(property="permissoes", type="array", @OA\Items(type="integer"), description="IDs das permissões")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Perfil criado")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:50', 'unique:perfis,nome'],
            'descricao' => ['nullable', 'string'],
            'permissoes' => ['sometimes', 'array'],
            'permissoes.*' => ['integer', 'exists:permissoes,id'],
        ]);

        $perfil = Perfil::create($validated);
        
        // Associar permissões se fornecidas
        if (isset($validated['permissoes'])) {
            $perfil->permissoes()->attach($validated['permissoes']);
        }
        
        $perfil->load(['permissoes']);
        
        return response()->json([
            'message' => 'Perfil criado com sucesso',
            'perfil' => $perfil
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/perfis/{id}",
     *     summary="Obter perfil",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Perfil")
     * )
     */
    public function show(Perfil $perfil): JsonResponse
    {
        $perfil->load(['permissoes', 'usuarios']);
        return response()->json($perfil);
    }

    /**
     * @OA\Put(
     *     path="/api/perfis/{id}",
     *     summary="Atualizar perfil",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="nome", type="string", maxLength=50),
     *         @OA\Property(property="descricao", type="string"),
     *         @OA\Property(property="permissoes", type="array", @OA\Items(type="integer"))
     *     )),
     *     @OA\Response(response=200, description="Perfil atualizado")
     * )
     */
    public function update(Request $request, Perfil $perfil): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'string', 'max:50', 'unique:perfis,nome,' . $perfil->id],
            'descricao' => ['nullable', 'string'],
            'permissoes' => ['sometimes', 'array'],
            'permissoes.*' => ['integer', 'exists:permissoes,id'],
        ]);

        $perfil->update(collect($validated)->except('permissoes')->toArray());
        
        // Atualizar permissões se fornecidas
        if (isset($validated['permissoes'])) {
            $perfil->permissoes()->sync($validated['permissoes']);
        }
        
        $perfil->load(['permissoes']);
        
        return response()->json([
            'message' => 'Perfil atualizado com sucesso',
            'perfil' => $perfil
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/perfis/{id}",
     *     summary="Excluir perfil",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Perfil excluído")
     * )
     */
    public function destroy(Perfil $perfil): JsonResponse
    {
        // Verificar se o perfil tem usuários
        if ($perfil->usuarios()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir perfil que possui usuários associados'
            ], 422);
        }

        $perfil->delete();
        
        return response()->json([
            'message' => 'Perfil excluído com sucesso'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/perfis/{id}/permissoes",
     *     summary="Adicionar permissões ao perfil",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         required={"permissoes"},
     *         @OA\Property(property="permissoes", type="array", @OA\Items(type="integer"))
     *     )),
     *     @OA\Response(response=200, description="Permissões adicionadas")
     * )
     */
    public function adicionarPermissoes(Request $request, Perfil $perfil): JsonResponse
    {
        $validated = $request->validate([
            'permissoes' => ['required', 'array'],
            'permissoes.*' => ['integer', 'exists:permissoes,id'],
        ]);

        $perfil->permissoes()->syncWithoutDetaching($validated['permissoes']);
        
        return response()->json([
            'message' => 'Permissões adicionadas com sucesso'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/perfis/{id}/permissoes/{permissao}",
     *     summary="Remover permissão do perfil",
     *     tags={"Gestão de Usuários"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="permissao", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Permissão removida")
     * )
     */
    public function removerPermissao(Perfil $perfil, Permissao $permissao): JsonResponse
    {
        $perfil->permissoes()->detach($permissao->id);
        
        return response()->json([
            'message' => 'Permissão removida com sucesso'
        ]);
    }
}