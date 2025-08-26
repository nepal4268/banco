<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContaOperacaoRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Conta;
use App\Http\Requests\ContaRequest;
use App\Services\TransactionService;
use App\Models\Pagamento;
use Illuminate\Validation\Rule;

class ContaController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/contas",
     *   summary="Listar contas",
     *   tags={"Contas"},
     *   @OA\Parameter(name="cliente_id", in="query", required=false, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="agencia_id", in="query", required=false, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="status_conta_id", in="query", required=false, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Lista paginada")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Conta::with(['cliente', 'agencia', 'tipoConta', 'moeda', 'statusConta']);

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->integer('cliente_id'));
        }
        if ($request->filled('agencia_id')) {
            $query->where('agencia_id', $request->integer('agencia_id'));
        }
        if ($request->filled('status_conta_id')) {
            $query->where('status_conta_id', $request->integer('status_conta_id'));
        }

        $perPage = $request->get('per_page', 15);
        $result = $query->paginate($perPage);
        return response()->json($result);
    }

    // Form endpoints não são necessários para API JSON

    /**
     * @OA\Post(
     *   path="/api/contas",
     *   summary="Criar conta",
     *   tags={"Contas"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"cliente_id","agencia_id","tipo_conta_id","moeda_id","status_conta_id"},
     *       @OA\Property(property="cliente_id", type="integer"),
     *       @OA\Property(property="agencia_id", type="integer"),
     *       @OA\Property(property="tipo_conta_id", type="integer"),
     *       @OA\Property(property="moeda_id", type="integer"),
     *       @OA\Property(property="saldo", type="number", format="float", description="Opcional. Normalmente 0"),
     *       @OA\Property(property="status_conta_id", type="integer"),
     *       @OA\Property(property="numero_conta", type="string", readOnly=true),
     *       @OA\Property(property="iban", type="string", readOnly=true)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Conta criada")
     * )
     */
    public function store(ContaRequest $request): JsonResponse
    {
        $conta = Conta::create($request->validated());
        $conta->load(['cliente', 'agencia', 'tipoConta', 'moeda', 'statusConta']);
        return response()->json(['message' => 'Conta criada com sucesso', 'conta' => $conta], 201);
    }

    /**
     * @OA\Get(
     *   path="/api/contas/{id}",
     *   summary="Obter conta",
     *   tags={"Contas"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Conta"),
     *   @OA\Response(response=404, description="Não encontrada")
     * )
     */
    public function show(Conta $conta): JsonResponse
    {
        $conta->load(['cliente', 'agencia', 'tipoConta', 'moeda', 'statusConta']);
        return response()->json($conta);
    }

    // Form endpoints não são necessários para API JSON

    /**
     * @OA\Put(
     *   path="/api/contas/{id}",
     *   summary="Atualizar conta",
     *   tags={"Contas"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(@OA\JsonContent(
     *       @OA\Property(property="agencia_id", type="integer", readOnly=true),
     *       @OA\Property(property="tipo_conta_id", type="integer"),
     *       @OA\Property(property="moeda_id", type="integer"),
     *       @OA\Property(property="status_conta_id", type="integer"),
     *       @OA\Property(property="numero_conta", type="string", readOnly=true),
     *       @OA\Property(property="iban", type="string", readOnly=true)
     *   )),
     *   @OA\Response(response=200, description="Conta atualizada")
     * )
     */
    public function update(ContaRequest $request, Conta $conta): JsonResponse
    {
        $conta->update($request->validated());
        $conta->load(['cliente', 'agencia', 'tipoConta', 'moeda', 'statusConta']);
        return response()->json(['message' => 'Conta atualizada com sucesso', 'conta' => $conta]);
    }

    /**
     * @OA\Delete(
     *   path="/api/contas/{id}",
     *   summary="Remover conta",
     *   tags={"Contas"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Excluída")
     * )
     */
    public function destroy(Conta $conta): JsonResponse
    {
        $conta->delete();
        return response()->json(['message' => 'Conta excluída com sucesso']);
    }

    /**
     * @OA\Post(
     *   path="/api/contas/{id}/depositar",
     *   summary="Depositar em conta",
     *   tags={"Contas"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(@OA\JsonContent(
     *       required={"valor","moeda_id"},
     *       @OA\Property(property="valor", type="number", format="float", example=100.50),
     *       @OA\Property(property="moeda_id", type="integer", example=1),
     *       @OA\Property(property="descricao", type="string", example="Depósito em numerário"),
     *       @OA\Property(property="referencia_externa", type="string", example="EXT-12345")
     *   )),
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Depósito efetuado"),
     *       @OA\Property(property="transacao", type="object")
     *     )
     *   )
     * )
     */
    public function depositar(Conta $conta, ContaOperacaoRequest $request, TransactionService $service): JsonResponse
    {
    $data = $request->validated();
    $depositante = $data['depositante'] ?? null;
    $transacao = $service->deposit($conta, (float)$data['valor'], (int)$data['moeda_id'], $data['descricao'] ?? null, $data['referencia_externa'] ?? null, $depositante);
        return response()->json(['message' => 'Depósito efetuado', 'transacao' => $transacao]);
    }

    /**
     * @OA\Post(
     *   path="/api/contas/{id}/levantar",
     *   summary="Levantamento em conta",
     *   tags={"Contas"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(@OA\JsonContent(
     *       required={"valor","moeda_id"},
     *       @OA\Property(property="valor", type="number", format="float", example=50.00),
     *       @OA\Property(property="moeda_id", type="integer", example=1),
     *       @OA\Property(property="descricao", type="string", example="Levantamento no balcão"),
     *       @OA\Property(property="referencia_externa", type="string", example="EXT-98765")
     *   )),
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Levantamento efetuado"),
     *       @OA\Property(property="transacao", type="object")
     *     )
     *   )
     * )
     */
    public function levantar(Conta $conta, ContaOperacaoRequest $request, TransactionService $service): JsonResponse
    {
        $data = $request->validated();
        // If BI provided, enforce it belongs to the account owner
        if (!empty($data['bi'])) {
            $cliente = $conta->cliente;
            $clienteBi = $cliente?->bi ?? null;
            if (!$clienteBi || trim($clienteBi) !== trim($data['bi'])) {
                return response()->json(['error' => 'BI do titular não confere com a conta informada.'], 400);
            }
        }

    $transacao = $service->withdraw($conta, (float)$data['valor'], (int)$data['moeda_id'], $data['descricao'] ?? null, $data['referencia_externa'] ?? null);
    // Return updated account so client can display new balance
    $contaAtualizada = Conta::with(['cliente','agencia','moeda','statusConta'])->find($conta->id);
    return response()->json(['message' => 'Levantamento efetuado', 'transacao' => $transacao, 'conta' => $contaAtualizada]);
    }

    /**
     * @OA\Post(
     *   path="/api/contas/{id}/pagar",
     *   summary="Efetuar pagamento a parceiro",
     *   tags={"Contas"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(@OA\JsonContent(
     *       required={"parceiro","referencia","valor","moeda_id"},
     *       @OA\Property(property="parceiro", type="string"),
     *       @OA\Property(property="referencia", type="string"),
     *       @OA\Property(property="valor", type="number", format="float"),
     *       @OA\Property(property="moeda_id", type="integer"),
     *       @OA\Property(property="descricao", type="string")
     *   )),
     *   @OA\Response(response=200, description="Pagamento efetuado")
     * )
     */
    public function pagar(Conta $conta, Request $request, TransactionService $service): JsonResponse
    {
        $data = $request->validate([
            'parceiro' => ['required', 'string', 'max:100'],
            'referencia' => ['required', 'string', 'max:100'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'moeda_id' => ['required', 'integer'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        $pagamento = $service->pay(
            $conta,
            $data['parceiro'],
            $data['referencia'],
            (float)$data['valor'],
            (int)$data['moeda_id'],
            $data['descricao'] ?? null
        );

        return response()->json(['message' => 'Pagamento efetuado', 'pagamento' => $pagamento]);
    }
}