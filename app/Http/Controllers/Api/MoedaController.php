<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Moeda;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class MoedaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/moedas",
     *     summary="Listar moedas",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="ativa", in="query", @OA\Schema(type="boolean"), description="Filtrar por moedas ativas"),
     *     @OA\Response(response=200, description="Lista de moedas")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Moeda::query();
        
        if ($request->has('ativa')) {
            $query->where('ativa', $request->boolean('ativa'));
        }
        
        return response()->json($query->get());
    }

    /**
     * @OA\Post(
     *     path="/api/moedas",
     *     summary="Criar moeda",
     *     tags={"Configuração"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo", "nome", "simbolo"},
     *             @OA\Property(property="codigo", type="string", maxLength=3, example="USD"),
     *             @OA\Property(property="nome", type="string", maxLength=50, example="Dólar Americano"),
     *             @OA\Property(property="simbolo", type="string", maxLength=5, example="$")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Moeda criada")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:3', 'unique:moedas,codigo'],
            'nome' => ['required', 'string', 'max:50'],
            'simbolo' => ['required', 'string', 'max:5'],
        ]);

        $moeda = Moeda::create($validated);
        
        return response()->json([
            'message' => 'Moeda criada com sucesso',
            'moeda' => $moeda
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/moedas/{id}",
     *     summary="Obter moeda",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Moeda")
     * )
     */
    public function show(Moeda $moeda): JsonResponse
    {
        return response()->json($moeda);
    }

    /**
     * @OA\Put(
     *     path="/api/moedas/{id}",
     *     summary="Atualizar moeda",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="codigo", type="string", maxLength=3),
     *         @OA\Property(property="nome", type="string", maxLength=50),
     *         @OA\Property(property="simbolo", type="string", maxLength=5)
     *     )),
     *     @OA\Response(response=200, description="Moeda atualizada")
     * )
     */
    public function update(Request $request, Moeda $moeda): JsonResponse
    {
        $validated = $request->validate([
            'codigo' => ['sometimes', 'string', 'max:3', Rule::unique('moedas', 'codigo')->ignore($moeda->id)],
            'nome' => ['sometimes', 'string', 'max:50'],
            'simbolo' => ['sometimes', 'string', 'max:5'],
        ]);

        $moeda->update($validated);
        
        return response()->json([
            'message' => 'Moeda atualizada com sucesso',
            'moeda' => $moeda
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/moedas/{id}",
     *     summary="Excluir moeda",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Moeda excluída")
     * )
     */
    public function destroy(Moeda $moeda): JsonResponse
    {
        // Verificar se a moeda está sendo usada
        if ($moeda->contas()->exists() || $moeda->transacoes()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir moeda que está sendo utilizada'
            ], 422);
        }

        $moeda->delete();
        
        return response()->json([
            'message' => 'Moeda excluída com sucesso'
        ]);
    }
}