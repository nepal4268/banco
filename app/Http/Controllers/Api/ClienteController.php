<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\TipoCliente;
use App\Models\StatusCliente;
use Illuminate\Http\Request;
use App\Http\Requests\ClienteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/clientes",
     *   summary="Listar clientes",
     *   tags={"Clientes"},
     *   @OA\Parameter(name="nome", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="bi", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="tipo_cliente_id", in="query", @OA\Schema(type="integer")),
     *   @OA\Parameter(name="status_cliente_id", in="query", @OA\Schema(type="integer")),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Lista paginada de clientes")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Cliente::with(['tipoCliente', 'statusCliente']);

        // Filtros
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('bi')) {
            $query->where('bi', 'like', '%' . $request->bi . '%');
        }

        if ($request->filled('tipo_cliente_id')) {
            $query->where('tipo_cliente_id', $request->tipo_cliente_id);
        }

        if ($request->filled('status_cliente_id')) {
            $query->where('status_cliente_id', $request->status_cliente_id);
        }

        // Paginação
        $perPage = $request->get('per_page', 15);
        $clientes = $query->paginate($perPage);

        return response()->json($clientes);
    }

    /**
     * @OA\Post(
     *   path="/api/clientes",
     *   summary="Criar cliente",
     *   tags={"Clientes"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nome","sexo","bi","tipo_cliente_id","status_cliente_id"},
     *       @OA\Property(property="nome", type="string", maxLength=100),
     *       @OA\Property(property="sexo", type="string", enum={"M","F"}),
     *       @OA\Property(property="bi", type="string", maxLength=25),
     *       @OA\Property(property="email", type="string", format="email"),
     *       @OA\Property(property="telefone", type="array", @OA\Items(type="string")),
     *       @OA\Property(property="data_nascimento", type="string", format="date"),
     *       @OA\Property(property="endereco", type="string"),
     *       @OA\Property(property="cidade", type="string"),
     *       @OA\Property(property="provincia", type="string"),
     *       @OA\Property(property="tipo_cliente_id", type="integer"),
     *       @OA\Property(property="status_cliente_id", type="integer")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Cliente criado")
     * )
     */
    public function store(ClienteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cliente = Cliente::create($validated);
        $cliente->load(['tipoCliente', 'statusCliente']);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'cliente' => $cliente
        ], 201);
    }

    /**
     * @OA\Get(
     *   path="/api/clientes/{id}",
     *   summary="Obter cliente",
     *   tags={"Clientes"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Cliente")
     * )
     */
    public function show(Cliente $cliente): JsonResponse
    {
        $cliente->load(['tipoCliente', 'statusCliente', 'contas.tipoConta', 'contas.moeda', 'contas.statusConta']);

        return response()->json($cliente);
    }

    /**
     * @OA\Put(
     *   path="/api/clientes/{id}",
     *   summary="Atualizar cliente",
     *   tags={"Clientes"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(@OA\JsonContent(
     *       @OA\Property(property="nome", type="string", maxLength=100),
     *       @OA\Property(property="sexo", type="string", enum={"M","F"}),
     *       @OA\Property(property="bi", type="string", maxLength=25),
     *       @OA\Property(property="email", type="string", format="email"),
     *       @OA\Property(property="telefone", type="array", @OA\Items(type="string")),
     *       @OA\Property(property="data_nascimento", type="string", format="date"),
     *       @OA\Property(property="endereco", type="string"),
     *       @OA\Property(property="cidade", type="string"),
     *       @OA\Property(property="provincia", type="string"),
     *       @OA\Property(property="tipo_cliente_id", type="integer"),
     *       @OA\Property(property="status_cliente_id", type="integer")
     *   )),
     *   @OA\Response(response=200, description="Cliente atualizado")
     * )
     */
    public function update(ClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $validated = $request->validated();

        $cliente->update($validated);
        $cliente->load(['tipoCliente', 'statusCliente']);

        return response()->json([
            'message' => 'Cliente atualizado com sucesso',
            'cliente' => $cliente
        ]);
    }

    /**
     * @OA\Delete(
     *   path="/api/clientes/{id}",
     *   summary="Excluir cliente",
     *   tags={"Clientes"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Excluído")
     * )
     */
    public function destroy(Cliente $cliente): JsonResponse
    {
        // Verificar se cliente tem contas ativas
        $contasAtivas = $cliente->contas()->whereHas('statusConta', function($query) {
            $query->where('nome', 'Ativa');
        })->count();

        if ($contasAtivas > 0) {
            return response()->json([
                'message' => 'Não é possível excluir cliente com contas ativas'
            ], 422);
        }

        $cliente->delete();

        return response()->json([
            'message' => 'Cliente excluído com sucesso'
        ]);
    }

    /**
     * @OA\Get(
     *   path="/api/clientes/lookups",
     *   summary="Listas de apoio para cliente",
     *   tags={"Clientes"},
     *   @OA\Response(response=200, description="Listas de apoio")
     * )
     */
    public function lookups(): JsonResponse
    {
        return response()->json([
            'tipos_cliente' => TipoCliente::all(),
            'status_cliente' => StatusCliente::all(),
        ]);
    }
}