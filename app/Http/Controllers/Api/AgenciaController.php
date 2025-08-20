<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AgenciaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/agencias",
     *     summary="Listar agências",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="ativa", in="query", @OA\Schema(type="boolean"), description="Filtrar por agências ativas"),
     *     @OA\Parameter(name="codigo_agencia", in="query", @OA\Schema(type="string"), description="Filtrar por código"),
     *     @OA\Response(response=200, description="Lista de agências")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Agencia::query();
        
        if ($request->has('ativa')) {
            $query->where('ativa', $request->boolean('ativa'));
        }
        
        if ($request->filled('codigo_agencia')) {
            $query->where('codigo_agencia', 'like', '%' . $request->codigo_agencia . '%');
        }
        
        return response()->json($query->get());
    }

    /**
     * @OA\Post(
     *     path="/api/agencias",
     *     summary="Criar agência",
     *     tags={"Configuração"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo_agencia", "nome", "endereco"},
     *             @OA\Property(property="codigo_banco", type="string", maxLength=4, example="0042"),
     *             @OA\Property(property="codigo_agencia", type="string", maxLength=4, example="0001"),
     *             @OA\Property(property="nome", type="string", maxLength=100, example="Agência Central"),
     *             @OA\Property(property="endereco", type="string", example="Rua da Independência, 123"),
     *             @OA\Property(property="telefone", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="ativa", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Agência criada")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_banco' => ['sometimes', 'string', 'max:4'],
            'codigo_agencia' => ['required', 'string', 'max:4', 'unique:agencias,codigo_agencia'],
            'nome' => ['required', 'string', 'max:100'],
            'endereco' => ['required', 'string'],
            'telefone' => ['sometimes', 'array'],
            'telefone.*' => ['string'],
            'email' => ['sometimes', 'email'],
            'ativa' => ['sometimes', 'boolean'],
        ]);

        // Define código do banco padrão se não fornecido
        if (!isset($validated['codigo_banco'])) {
            $validated['codigo_banco'] = Agencia::getCodigoBancoPadrao();
        }

        $agencia = Agencia::create($validated);
        
        return response()->json([
            'message' => 'Agência criada com sucesso',
            'agencia' => $agencia
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/agencias/{id}",
     *     summary="Obter agência",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Agência")
     * )
     */
    public function show(Agencia $agencia): JsonResponse
    {
        $agencia->load(['contas']);
        return response()->json($agencia);
    }

    /**
     * @OA\Put(
     *     path="/api/agencias/{id}",
     *     summary="Atualizar agência",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="codigo_banco", type="string", maxLength=4),
     *         @OA\Property(property="codigo_agencia", type="string", maxLength=4),
     *         @OA\Property(property="nome", type="string", maxLength=100),
     *         @OA\Property(property="endereco", type="string"),
     *         @OA\Property(property="telefone", type="array", @OA\Items(type="string")),
     *         @OA\Property(property="email", type="string", format="email"),
     *         @OA\Property(property="ativa", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Agência atualizada")
     * )
     */
    public function update(Request $request, Agencia $agencia): JsonResponse
    {
        $validated = $request->validate([
            'codigo_banco' => ['sometimes', 'string', 'max:4'],
            'codigo_agencia' => ['sometimes', 'string', 'max:4', Rule::unique('agencias', 'codigo_agencia')->ignore($agencia->id)],
            'nome' => ['sometimes', 'string', 'max:100'],
            'endereco' => ['sometimes', 'string'],
            'telefone' => ['sometimes', 'array'],
            'telefone.*' => ['string'],
            'email' => ['sometimes', 'email'],
            'ativa' => ['sometimes', 'boolean'],
        ]);

        $agencia->update($validated);
        
        return response()->json([
            'message' => 'Agência atualizada com sucesso',
            'agencia' => $agencia
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/agencias/{id}",
     *     summary="Excluir agência",
     *     tags={"Configuração"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Agência excluída")
     * )
     */
    public function destroy(Agencia $agencia): JsonResponse
    {
        // Verificar se a agência tem contas
        if ($agencia->contas()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir agência que possui contas'
            ], 422);
        }

        $agencia->delete();
        
        return response()->json([
            'message' => 'Agência excluída com sucesso'
        ]);
    }
}