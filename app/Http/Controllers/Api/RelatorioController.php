<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transacao;
use App\Models\Conta;
use App\Models\Cliente;
use App\Models\LogAcao;
use Illuminate\Http\Request;
use App\Http\Requests\RelatorioTransacoesRequest;
use App\Http\Requests\RelatorioExtratoRequest;
use App\Http\Requests\RelatorioAuditoriaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RelatorioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/relatorios/dashboard",
     *     summary="Dashboard com métricas gerais",
     *     tags={"Relatórios"},
     *     @OA\Response(response=200, description="Métricas do dashboard")
     * )
     */
    public function dashboard(): JsonResponse
    {
        $metricas = [
            'clientes' => [
                'total' => Cliente::count(),
                'ativos' => Cliente::whereHas('statusCliente', function($q) {
                    $q->where('nome', 'Ativo');
                })->count(),
                'novos_mes' => Cliente::whereMonth('created_at', now()->month)->count()
            ],
            'contas' => [
                'total' => Conta::count(),
                'ativas' => Conta::whereHas('statusConta', function($q) {
                    $q->where('nome', 'Ativa');
                })->count(),
                'saldo_total' => Conta::sum('saldo')
            ],
            'transacoes' => [
                'hoje' => Transacao::whereDate('created_at', today())->count(),
                'mes' => Transacao::whereMonth('created_at', now()->month)->count(),
                'volume_mes' => Transacao::whereMonth('created_at', now()->month)->sum('valor')
            ]
        ];

        return response()->json($metricas);
    }

    /**
     * @OA\Get(
     *     path="/api/relatorios/transacoes",
     *     summary="Relatório detalhado de transações",
     *     tags={"Relatórios"},
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date"), description="Data início"),
     *     @OA\Parameter(name="data_fim", in="query", @OA\Schema(type="string", format="date"), description="Data fim"),
     *     @OA\Parameter(name="tipo", in="query", @OA\Schema(type="string"), description="Tipo de transação"),
     *     @OA\Parameter(name="moeda", in="query", @OA\Schema(type="string"), description="Código da moeda"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Itens por página"),
     *     @OA\Response(response=200, description="Relatório de transações")
     * )
     */
    public function transacoes(RelatorioTransacoesRequest $request): JsonResponse
    {
        $request->validated();

        $query = Transacao::with([
            'contaOrigem.cliente', 
            'contaDestino.cliente', 
            'tipoTransacao', 
            'moeda',
            'statusTransacao'
        ]);

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        if ($request->filled('tipo')) {
            $query->whereHas('tipoTransacao', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->tipo . '%');
            });
        }

        if ($request->filled('moeda')) {
            $query->whereHas('moeda', function($q) use ($request) {
                $q->where('codigo', $request->moeda);
            });
        }

        // Estatísticas resumidas
        $estatisticas = [
            'total_transacoes' => $query->count(),
            'volume_total' => $query->sum('valor'),
            'por_tipo' => $query->select('tipo_transacao_id')
                ->selectRaw('COUNT(*) as quantidade, SUM(valor) as volume')
                ->with('tipoTransacao:id,nome')
                ->groupBy('tipo_transacao_id')
                ->get(),
            'por_moeda' => $query->select('moeda_id')
                ->selectRaw('COUNT(*) as quantidade, SUM(valor) as volume')
                ->with('moeda:id,codigo,nome')
                ->groupBy('moeda_id')
                ->get()
        ];

        $perPage = $request->get('per_page', 50);
        $transacoes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'estatisticas' => $estatisticas,
            'transacoes' => $transacoes
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/relatorios/contas/{id}/extrato",
     *     summary="Extrato detalhado da conta",
     *     tags={"Relatórios"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="data_fim", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Extrato da conta")
     * )
     */
    public function extrato(RelatorioExtratoRequest $request, Conta $conta): JsonResponse
    {
        $request->validated();

        $dataInicio = $request->data_inicio ?? now()->subMonth()->toDateString();
        $dataFim = $request->data_fim ?? now()->toDateString();

        // Transações da conta
        $transacoesQuery = Transacao::where(function($q) use ($conta) {
                $q->where('conta_origem_id', $conta->id)
                  ->orWhere('conta_destino_id', $conta->id);
            })
            ->whereDate('created_at', '>=', $dataInicio)
            ->whereDate('created_at', '<=', $dataFim)
            ->with(['contaOrigem', 'contaDestino', 'tipoTransacao', 'statusTransacao'])
            ->orderBy('created_at', 'asc');

        $transacoesRaw = $transacoesQuery->get();

        // Calcular saldo corrente (running balance)
        $saldo = (float) $conta->saldo;
        // Para calcular saldo anterior por linha, iteramos do fim para o início
        $linhas = [];
        for ($i = $transacoesRaw->count() - 1; $i >= 0; $i--) {
            $t = $transacoesRaw[$i];
            $isSaida = ($t->conta_origem_id == $conta->id);
            $valor = (float) $t->valor;
            $saldoAnterior = $saldo;
            if ($isSaida) {
                $saldo = round($saldo + $valor, 2); // invertendo para obter saldo anterior
            } else {
                $saldo = round($saldo - $valor, 2);
            }
            $linhas[$i] = [
                'id' => $t->id,
                'data' => $t->created_at,
                'descricao' => $t->descricao,
                'tipo' => $isSaida ? 'saida' : 'entrada',
                'valor' => $t->valor,
                'saldo_anterior' => $saldo,
                'tipo_transacao' => $t->tipoTransacao->nome,
                'status' => $t->statusTransacao->nome,
                'referencia' => $t->referencia_externa
            ];
        }
        ksort($linhas);
        $transacoes = collect(array_values($linhas))->sortByDesc('data')->values();

        $resumo = [
            'conta' => $conta->load(['cliente', 'agencia', 'tipoConta', 'moeda']),
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ],
            'saldo_atual' => $conta->saldo,
            'total_entradas' => $transacoes->where('tipo', 'entrada')->sum('valor'),
            'total_saidas' => $transacoes->where('tipo', 'saida')->sum('valor'),
            'quantidade_transacoes' => $transacoes->count()
        ];

        return response()->json([
            'resumo' => $resumo,
            'transacoes' => $transacoes->values()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/relatorios/clientes/{cliente}/extrato",
     *     summary="Extrato agregado de todas as contas do cliente",
     *     tags={"Relatórios"},
     *     @OA\Parameter(name="cliente", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="data_fim", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Extrato agregado")
     * )
     */
    public function extratoCliente(RelatorioExtratoRequest $request, Cliente $cliente): JsonResponse
    {
        $request->validated();

        $dataInicio = $request->data_inicio ?? now()->subMonth()->toDateString();
        $dataFim = $request->data_fim ?? now()->toDateString();

        $contas = $cliente->contas()->with('moeda')->get();
        $contaIds = $contas->pluck('id')->all();

        $transacoes = Transacao::where(function($q) use ($contaIds) {
                $q->whereIn('conta_origem_id', $contaIds)
                  ->orWhereIn('conta_destino_id', $contaIds);
            })
            ->whereDate('created_at', '>=', $dataInicio)
            ->whereDate('created_at', '<=', $dataFim)
            ->with(['contaOrigem', 'contaDestino', 'tipoTransacao', 'statusTransacao', 'moeda'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($t) use ($contaIds) {
                $tipo = in_array($t->conta_origem_id, $contaIds) ? 'saida' : 'entrada';
                return [
                    'id' => $t->id,
                    'data' => $t->created_at,
                    'descricao' => $t->descricao,
                    'tipo' => $tipo,
                    'valor' => $t->valor,
                    'moeda' => $t->moeda->codigo,
                    'conta_origem_id' => $t->conta_origem_id,
                    'conta_destino_id' => $t->conta_destino_id,
                    'tipo_transacao' => $t->tipoTransacao->nome,
                    'status' => $t->statusTransacao->nome,
                    'referencia' => $t->referencia_externa
                ];
            });

        // Resumo por moeda
        $porMoeda = $transacoes->groupBy('moeda')->map(function(Collection $items) {
            return [
                'entradas' => $items->where('tipo', 'entrada')->sum('valor'),
                'saidas' => $items->where('tipo', 'saida')->sum('valor'),
                'quantidade' => $items->count(),
            ];
        });

        return response()->json([
            'cliente' => $cliente->only(['id','nome','bi','email']),
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim,
            ],
            'contas' => $contas,
            'por_moeda' => $porMoeda,
            'transacoes' => $transacoes,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/relatorios/auditoria",
     *     summary="Relatório de auditoria (logs de ações)",
     *     tags={"Relatórios"},
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="data_fim", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="acao", in="query", @OA\Schema(type="string"), description="Filtrar por ação"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Logs de auditoria")
     * )
     */
    public function auditoria(RelatorioAuditoriaRequest $request): JsonResponse
    {
        $request->validated();

        $query = LogAcao::orderBy('created_at', 'desc');

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        if ($request->filled('acao')) {
            $query->where('acao', 'like', '%' . $request->acao . '%');
        }

        $perPage = $request->get('per_page', 50);
        return response()->json($query->paginate($perPage));
    }
}