<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaxaCambio;
use App\Models\OperacaoCambio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaxaCambioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/taxas-cambio",
     *     summary="Listar taxas de câmbio atuais",
     *     tags={"Câmbio"},
     *     @OA\Parameter(name="moeda_origem", in="query", @OA\Schema(type="string"), description="Código da moeda origem"),
     *     @OA\Parameter(name="moeda_destino", in="query", @OA\Schema(type="string"), description="Código da moeda destino"),
     *     @OA\Response(response=200, description="Lista de taxas de câmbio")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = TaxaCambio::with(['moedaOrigem', 'moedaDestino'])
            ->where('ativa', true)
            ->orderBy('updated_at', 'desc');
        
        if ($request->filled('moeda_origem')) {
            $query->whereHas('moedaOrigem', function($q) use ($request) {
                $q->where('codigo', $request->moeda_origem);
            });
        }
        
        if ($request->filled('moeda_destino')) {
            $query->whereHas('moedaDestino', function($q) use ($request) {
                $q->where('codigo', $request->moeda_destino);
            });
        }
        
        return response()->json($query->get());
    }

    /**
     * @OA\Get(
     *     path="/api/taxas-cambio/cotacao",
     *     summary="Obter cotação específica",
     *     tags={"Câmbio"},
     *     @OA\Parameter(name="moeda_origem", in="query", required=true, @OA\Schema(type="string"), description="Código da moeda origem"),
     *     @OA\Parameter(name="moeda_destino", in="query", required=true, @OA\Schema(type="string"), description="Código da moeda destino"),
     *     @OA\Parameter(name="valor", in="query", @OA\Schema(type="number"), description="Valor para conversão"),
     *     @OA\Response(response=200, description="Cotação atual")
     * )
     */
    public function cotacao(Request $request): JsonResponse
    {
        $request->validate([
            'moeda_origem' => 'required|string|size:3',
            'moeda_destino' => 'required|string|size:3',
            'valor' => 'nullable|numeric|min:0'
        ]);

        $taxa = TaxaCambio::with(['moedaOrigem', 'moedaDestino'])
            ->whereHas('moedaOrigem', function($q) use ($request) {
                $q->where('codigo', $request->moeda_origem);
            })
            ->whereHas('moedaDestino', function($q) use ($request) {
                $q->where('codigo', $request->moeda_destino);
            })
            ->where('ativa', true)
            ->first();

        if (!$taxa) {
            return response()->json(['error' => 'Taxa de câmbio não encontrada'], 404);
        }

        $response = [
            'moeda_origem' => $taxa->moedaOrigem->codigo,
            'moeda_destino' => $taxa->moedaDestino->codigo,
            'taxa_compra' => $taxa->taxa_compra,
            'taxa_venda' => $taxa->taxa_venda,
            'data_atualizacao' => $taxa->updated_at
        ];

        if ($request->filled('valor')) {
            $valor = floatval($request->valor);
            $response['conversao'] = [
                'valor_origem' => $valor,
                'valor_convertido_compra' => $valor * $taxa->taxa_compra,
                'valor_convertido_venda' => $valor * $taxa->taxa_venda
            ];
        }

        return response()->json($response);
    }

    /**
     * @OA\Post(
     *     path="/api/taxas-cambio",
     *     summary="Criar/atualizar taxa de câmbio",
     *     tags={"Câmbio"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"moeda_origem_id", "moeda_destino_id", "taxa_compra", "taxa_venda"},
     *             @OA\Property(property="moeda_origem_id", type="integer", example=1),
     *             @OA\Property(property="moeda_destino_id", type="integer", example=2),
     *             @OA\Property(property="taxa_compra", type="number", format="decimal", example=825.50),
     *             @OA\Property(property="taxa_venda", type="number", format="decimal", example=830.00),
     *             @OA\Property(property="ativa", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Taxa criada/atualizada com sucesso")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'moeda_origem_id' => 'required|integer|exists:moedas,id',
            'moeda_destino_id' => 'required|integer|exists:moedas,id|different:moeda_origem_id',
            'taxa_compra' => 'required|numeric|min:0',
            'taxa_venda' => 'required|numeric|min:0',
            'ativa' => 'boolean'
        ]);

        // Desativar taxa anterior se existir
        TaxaCambio::where('moeda_origem_id', $request->moeda_origem_id)
            ->where('moeda_destino_id', $request->moeda_destino_id)
            ->update(['ativa' => false]);

        $taxa = TaxaCambio::create($validated);
        
        return response()->json($taxa->load(['moedaOrigem', 'moedaDestino']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/operacoes-cambio",
     *     summary="Histórico de operações de câmbio",
     *     tags={"Câmbio"},
     *     @OA\Parameter(name="conta_id", in="query", @OA\Schema(type="integer"), description="Filtrar por conta"),
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date"), description="Data início"),
     *     @OA\Parameter(name="data_fim", in="query", @OA\Schema(type="string", format="date"), description="Data fim"),
     *     @OA\Response(response=200, description="Histórico de operações")
     * )
     */
    public function historico(Request $request): JsonResponse
    {
        $query = OperacaoCambio::with([
            'contaOrigem.cliente', 
            'contaDestino.cliente', 
            'moedaOrigem', 
            'moedaDestino'
        ])->orderBy('created_at', 'desc');
        
        if ($request->filled('conta_id')) {
            $contaId = $request->conta_id;
            $query->where(function($q) use ($contaId) {
                $q->where('conta_origem_id', $contaId)
                  ->orWhere('conta_destino_id', $contaId);
            });
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        
        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }
}