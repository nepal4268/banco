<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TransacaoCambioRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TransferenciaInternaRequest;
use App\Http\Requests\TransferenciaExternaRequest;
use App\Models\Transacao;
use App\Models\Conta;
use App\Services\TransactionService;

class TransacaoController extends Controller
{
    /**
     * @OA\Get(path="/api/transacoes", summary="Listar transações", tags={"Transações"},
     *   @OA\Parameter(name="conta_id", in="query", @OA\Schema(type="integer")),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Lista paginada")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transacao::with(['contaOrigem', 'contaDestino', 'tipoTransacao', 'moeda', 'statusTransacao']);
        if ($request->filled('conta_id')) {
            $id = (int)$request->conta_id;
            $query->where(function ($q) use ($id) {
                $q->where('conta_origem_id', $id)->orWhere('conta_destino_id', $id);
            });
        }
        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    // Criação genérica de transações não é exposta diretamente; usar endpoints específicos abaixo.

    /**
     * @OA\Get(path="/api/transacoes/{id}", summary="Obter transação", tags={"Transações"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Transação")
     * )
     */
    public function show(Transacao $transacao): JsonResponse
    {
        $transacao->load(['contaOrigem', 'contaDestino', 'tipoTransacao', 'moeda', 'statusTransacao']);
        return response()->json($transacao);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    // Transações não são editáveis neste contexto

    // Transações não são removidas neste contexto

    /**
     * @OA\Post(path="/api/transacoes/transferir", summary="Transferência interna", tags={"Transações"},
     *   @OA\RequestBody(@OA\JsonContent(
     *       required={"conta_origem_id","conta_destino_id","valor","moeda_id"},
     *       @OA\Property(property="conta_origem_id", type="integer"),
     *       @OA\Property(property="conta_destino_id", type="integer"),
     *       @OA\Property(property="iban_destino", type="string", maxLength=34, description="Opcional: IBAN do destino ao invés de conta interna"),
     *       @OA\Property(property="valor", type="number", format="float"),
     *       @OA\Property(property="moeda_id", type="integer"),
     *       @OA\Property(property="descricao", type="string")
     *   )),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function transferirInterno(TransferenciaInternaRequest $request, TransactionService $service): JsonResponse
    {
        $data = $request->validated();
        $origem = Conta::findOrFail($data['conta_origem_id']);

        // Permitir informar IBAN do destino no lugar de conta interna
        if (!empty($data['iban_destino'])) {
            $destino = Conta::where('iban', $data['iban_destino'])->firstOrFail();
        } else {
            $destino = Conta::findOrFail($data['conta_destino_id']);
        }
        // Server-side validation: internal transfers must be between accounts with the same currency
        if($origem && $destino && $origem->moeda_id !== $destino->moeda_id){
            \Illuminate\Support\Facades\Log::info('transferirInterno blocked - different currencies', ['origem_id' => $origem->id, 'origem_moeda' => $origem->moeda_id, 'destino_id' => $destino->id, 'destino_moeda' => $destino->moeda_id, 'payload' => $data]);
            return response()->json(['error' => 'Contas têm moedas diferentes; transferência interna não permitida.'], 400);
        }
        // Optional BI check for origin account owner
        if (!empty($data['bi_origem'])) {
            $cliente = $origem->cliente;
            $clienteBi = $cliente?->bi ?? null;
            if (!$clienteBi || trim($clienteBi) !== trim($data['bi_origem'])) {
                return response()->json(['error' => 'BI do titular da conta de origem não confere.'], 400);
            }
        }
        $transacao = $service->transferInternal($origem, $destino, (float)$data['valor'], (int)$data['moeda_id'], $data['descricao'] ?? null);
        return response()->json(['message' => 'Transferência efetuada', 'transacao' => $transacao]);
    }

    /**
     * @OA\Post(path="/api/transacoes/transferir-externo", summary="Transferência de/para externo", tags={"Transações"},
     *   @OA\RequestBody(@OA\JsonContent(
     *       required={"valor","moeda_id"},
     *       @OA\Property(property="conta_origem_id", type="integer"),
     *       @OA\Property(property="conta_destino_id", type="integer"),
     *       @OA\Property(property="origem_externa", type="boolean"),
     *       @OA\Property(property="destino_externa", type="boolean"),
     *       @OA\Property(property="conta_externa_origem", type="string"),
     *       @OA\Property(property="banco_externo_origem", type="string"),
     *       @OA\Property(property="conta_externa_destino", type="string"),
     *       @OA\Property(property="banco_externo_destino", type="string"),
     *       @OA\Property(property="valor", type="number", format="float"),
     *       @OA\Property(property="moeda_id", type="integer"),
     *       @OA\Property(property="descricao", type="string"),
     *       @OA\Property(property="referencia_externa", type="string")
     *   )),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function transferirExterno(TransferenciaExternaRequest $request, TransactionService $service): JsonResponse
    {
        $data = $request->validated();

        $contaOrigem = !empty($data['conta_origem_id']) && empty($data['origem_externa'])
            ? Conta::findOrFail($data['conta_origem_id']) : null;
        $contaDestino = !empty($data['conta_destino_id']) && empty($data['destino_externa'])
            ? Conta::findOrFail($data['conta_destino_id']) : null;

        $transacao = $service->transferExternal(
            $contaOrigem,
            $contaDestino,
            (float)$data['valor'],
            (int)$data['moeda_id'],
            [
                'conta_externa_origem' => $data['conta_externa_origem'] ?? null,
                'banco_externo_origem' => $data['banco_externo_origem'] ?? null,
                'conta_externa_destino' => $data['conta_externa_destino'] ?? null,
                'banco_externo_destino' => $data['banco_externo_destino'] ?? null,
            ],
            $data['descricao'] ?? null,
            $data['referencia_externa'] ?? null
        );
        return response()->json(['message' => 'Transferência processada', 'transacao' => $transacao]);
    }

    /**
     * @OA\Post(path="/api/transacoes/cambio", summary="Operação de câmbio entre contas internas", tags={"Transações"},
     *   @OA\RequestBody(@OA\JsonContent(
     *       required={"conta_origem_id","conta_destino_id","moeda_origem_id","moeda_destino_id","valor_origem"},
     *       @OA\Property(property="cliente_id", type="integer"),
     *       @OA\Property(property="conta_origem_id", type="integer"),
     *       @OA\Property(property="conta_destino_id", type="integer"),
     *       @OA\Property(property="moeda_origem_id", type="integer"),
     *       @OA\Property(property="moeda_destino_id", type="integer"),
     *       @OA\Property(property="valor_origem", type="number", format="float", example=100.00),
     *       @OA\Property(property="descricao", type="string")
     *   )),
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Operação de câmbio concluída"),
     *       @OA\Property(property="operacao", type="object")
     *     )
     *   )
     * )
     */
    public function cambio(TransacaoCambioRequest $request, TransactionService $service): JsonResponse
    {
        $data = $request->validated();

        $op = $service->exchange(
            $data['cliente_id'] ?? null,
            Conta::findOrFail($data['conta_origem_id']),
            Conta::findOrFail($data['conta_destino_id']),
            (int)$data['moeda_origem_id'],
            (int)$data['moeda_destino_id'],
            (float)$data['valor_origem'],
            $data['descricao'] ?? null
        );

        return response()->json(['message' => 'Operação de câmbio concluída', 'operacao' => $op]);
    }
}
