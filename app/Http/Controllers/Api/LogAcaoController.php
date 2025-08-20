<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogAcao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LogAcaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/logs",
     *     summary="Listar logs de ações",
     *     tags={"Auditoria"},
     *     @OA\Parameter(name="usuario_id", in="query", @OA\Schema(type="integer"), description="Filtrar por usuário"),
     *     @OA\Parameter(name="acao", in="query", @OA\Schema(type="string"), description="Filtrar por ação"),
     *     @OA\Parameter(name="tabela", in="query", @OA\Schema(type="string"), description="Filtrar por tabela"),
     *     @OA\Parameter(name="data_inicio", in="query", @OA\Schema(type="string", format="date"), description="Data início"),
     *     @OA\Parameter(name="data_fim", in="query", @OA\Schema(type="string", format="date"), description="Data fim"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Itens por página"),
     *     @OA\Response(response=200, description="Lista de logs")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = LogAcao::with(['usuario']);
        
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }
        
        if ($request->filled('acao')) {
            $query->where('acao', 'like', '%' . $request->acao . '%');
        }
        
        if ($request->filled('tabela')) {
            $query->where('tabela', $request->tabela);
        }
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        
        $perPage = $request->get('per_page', 50);
        return response()->json($query->orderBy('created_at', 'desc')->paginate($perPage));
    }

    /**
     * @OA\Get(
     *     path="/api/logs/{id}",
     *     summary="Obter log específico",
     *     tags={"Auditoria"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Log de ação")
     * )
     */
    public function show(LogAcao $log): JsonResponse
    {
        $log->load(['usuario']);
        return response()->json($log);
    }

    /**
     * @OA\Get(
     *     path="/api/logs/estatisticas",
     *     summary="Estatísticas dos logs",
     *     tags={"Auditoria"},
     *     @OA\Parameter(name="periodo", in="query", @OA\Schema(type="string", enum={"hoje", "semana", "mes", "ano"}), description="Período das estatísticas"),
     *     @OA\Response(response=200, description="Estatísticas dos logs")
     * )
     */
    public function estatisticas(Request $request): JsonResponse
    {
        $periodo = $request->get('periodo', 'mes');
        
        $query = LogAcao::query();
        
        switch ($periodo) {
            case 'hoje':
                $query->whereDate('created_at', today());
                break;
            case 'semana':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'mes':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'ano':
                $query->whereYear('created_at', now()->year);
                break;
        }
        
        $estatisticas = [
            'total_logs' => $query->count(),
            'por_acao' => $query->selectRaw('acao, COUNT(*) as total')
                               ->groupBy('acao')
                               ->orderBy('total', 'desc')
                               ->get(),
            'por_tabela' => $query->selectRaw('tabela, COUNT(*) as total')
                                 ->groupBy('tabela')
                                 ->orderBy('total', 'desc')
                                 ->get(),
            'por_usuario' => $query->selectRaw('usuario_id, COUNT(*) as total')
                                  ->with('usuario:id,nome')
                                  ->groupBy('usuario_id')
                                  ->orderBy('total', 'desc')
                                  ->limit(10)
                                  ->get(),
            'ultimas_24h' => LogAcao::where('created_at', '>=', now()->subDay())->count(),
        ];
        
        return response()->json($estatisticas);
    }

    /**
     * @OA\Get(
     *     path="/api/logs/acoes",
     *     summary="Listar tipos de ações registradas",
     *     tags={"Auditoria"},
     *     @OA\Response(response=200, description="Tipos de ações")
     * )
     */
    public function acoes(): JsonResponse
    {
        $acoes = LogAcao::distinct('acao')->orderBy('acao')->pluck('acao');
        
        return response()->json(['acoes' => $acoes]);
    }

    /**
     * @OA\Get(
     *     path="/api/logs/tabelas",
     *     summary="Listar tabelas monitoradas",
     *     tags={"Auditoria"},
     *     @OA\Response(response=200, description="Tabelas monitoradas")
     * )
     */
    public function tabelas(): JsonResponse
    {
        $tabelas = LogAcao::distinct('tabela')->orderBy('tabela')->pluck('tabela');
        
        return response()->json(['tabelas' => $tabelas]);
    }

    /**
     * @OA\Get(
     *     path="/api/logs/usuario/{usuario_id}",
     *     summary="Logs de um usuário específico",
     *     tags={"Auditoria"},
     *     @OA\Parameter(name="usuario_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), description="Itens por página"),
     *     @OA\Response(response=200, description="Logs do usuário")
     * )
     */
    public function logsPorUsuario(Request $request, int $usuarioId): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        
        $logs = LogAcao::with(['usuario'])
                       ->where('usuario_id', $usuarioId)
                       ->orderBy('created_at', 'desc')
                       ->paginate($perPage);
        
        return response()->json($logs);
    }

    /**
     * @OA\Delete(
     *     path="/api/logs/limpar",
     *     summary="Limpar logs antigos",
     *     tags={"Auditoria"},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="dias", type="integer", example=90, description="Logs mais antigos que X dias serão removidos")
     *     )),
     *     @OA\Response(response=200, description="Logs limpos")
     * )
     */
    public function limpar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'dias' => ['required', 'integer', 'min:1', 'max:365']
        ]);

        $dataLimite = now()->subDays($validated['dias']);
        $logsRemovidos = LogAcao::where('created_at', '<', $dataLimite)->count();
        
        LogAcao::where('created_at', '<', $dataLimite)->delete();
        
        return response()->json([
            'message' => "Logs antigos limpos com sucesso",
            'logs_removidos' => $logsRemovidos,
            'data_limite' => $dataLimite->format('Y-m-d H:i:s')
        ]);
    }
}