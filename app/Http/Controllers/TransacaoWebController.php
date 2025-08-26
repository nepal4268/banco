<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use App\Models\TipoTransacao;
use App\Models\StatusTransacao;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use App\Models\Conta;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TransacaoWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Transacao::with(['conta.cliente', 'tipoTransacao', 'statusTransacao']);

        // Filtros
        if ($request->filled('cliente_nome')) {
            $query->whereHas('conta.cliente', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->cliente_nome . '%');
            });
        }

        if ($request->filled('numero_conta')) {
            $query->whereHas('conta', function($q) use ($request) {
                $q->where('numero_conta', 'like', '%' . $request->numero_conta . '%');
            });
        }

        if ($request->filled('tipo_transacao_id')) {
            $query->where('tipo_transacao_id', $request->tipo_transacao_id);
        }

        if ($request->filled('status_transacao_id')) {
            $query->where('status_transacao_id', $request->status_transacao_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $transacoes = $query->orderBy('created_at', 'desc')->paginate(15);
        $tiposTransacao = TipoTransacao::all();
        $statusTransacao = StatusTransacao::all();

        return view('admin.transacoes.index', compact('transacoes', 'tiposTransacao', 'statusTransacao'));
    }


    public function searchByConta(Request $request)
    {
        $request->validate([
            'numero_conta' => 'required|string'
        ]);

        $numero = trim($request->numero_conta);

        // find account by numero_conta
        $conta = \App\Models\Conta::where('numero_conta', $numero)->first();
        if(!$conta){
            return response()->json(['error' => 'Conta não encontrada.'], 404);
        }

        // Fetch all transactions for this account (either as origin or destination), grouped by year-month (desc)
        $transacoesAll = Transacao::with(['tipoTransacao','statusTransacao','moeda'])
            ->where(function($q) use ($conta){
                $q->where('conta_origem_id', $conta->id)->orWhere('conta_destino_id', $conta->id);
            })
            ->orderBy('created_at','desc')
            ->get()
            ->groupBy(function($t){ return $t->created_at->format('Y-m'); });

        $html = view('admin.transacoes._by_conta_results', ['transacoesGrouped' => $transacoesAll, 'conta' => $conta])->render();
        return response()->json(['html' => $html]);
    }

    public function porConta(Request $request)
    {
        // apenas renderiza a página com o campo e o JS que chama searchByConta
        return view('admin.transacoes.por_conta');
    }

    // AJAX: find conta by numero (returns JSON conta or 404)
    public function findConta(Request $request)
    {
        $request->validate(['numero_conta' => 'required|string']);
        $numero = trim($request->numero_conta);
    // Allow the AJAX lookup to return account info for authenticated users.
    $conta = Conta::with(['cliente','agencia','moeda','statusConta'])->where('numero_conta', $numero)->first();
        if(!$conta) return response()->json(['error' => 'Conta não encontrada'], 404);

        $lastTransactions = \App\Models\Transacao::with(['tipoTransacao','statusTransacao','moeda'])
            ->where(function($q) use ($conta){
                $q->where('conta_origem_id', $conta->id)->orWhere('conta_destino_id', $conta->id);
            })
            ->orderBy('created_at','desc')
            ->limit(5)
            ->get()
            ->map(function($t){
                return [
                    'id' => $t->id,
                    'data' => $t->created_at->format('Y-m-d H:i:s'),
                    'tipo' => $t->tipoTransacao->nome ?? null,
                    'valor' => (float)$t->valor,
                    'moeda' => $t->moeda->codigo ?? null,
                    'descricao' => $t->descricao ?? null,
                ];
            });

        return response()->json(['conta' => $conta, 'lastTransactions' => $lastTransactions]);
    }

    // Perform deposit via TransactionService (AJAX)
    public function depositarConta(Request $request, Conta $conta, TransactionService $service)
    {
        // require permission to create transactions
        if (!$request->user() || !($request->user()->isAdmin() || $request->user()->hasPermission('transacoes.create'))) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $data = $request->validate([
            'valor' => ['required','numeric','gt:0'],
            'moeda_id' => ['required','integer'],
            'descricao' => ['nullable','string','max:255'],
            'referencia_externa' => ['nullable','string','max:100']
        ]);
        Log::info('depositarConta called', ['user_id' => optional($request->user())->id, 'user_email' => optional($request->user())->email, 'conta_id' => $conta->id, 'payload' => $data]);
        try{
            $transacao = $service->deposit($conta, (float)$data['valor'], (int)$data['moeda_id'], $data['descricao'] ?? null, $data['referencia_externa'] ?? null);
            Log::info('depositarConta result', ['transacao_id' => $transacao->id ?? null]);
            $contaFresh = Conta::with(['moeda'])->find($conta->id);
            $lastTransactions = \App\Models\Transacao::with(['tipoTransacao','statusTransacao','moeda'])
                ->where(function($q) use ($contaFresh){ $q->where('conta_origem_id', $contaFresh->id)->orWhere('conta_destino_id', $contaFresh->id); })
                ->orderBy('created_at','desc')->limit(5)->get();
            return response()->json(['message' => 'Depósito efetuado', 'transacao' => $transacao, 'conta' => $contaFresh, 'lastTransactions' => $lastTransactions]);
        }catch(\Exception $e){
            Log::error('depositarConta error', ['error' => $e->getMessage(), 'payload' => $data, 'conta_id' => $conta->id]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function levantarConta(Request $request, Conta $conta, TransactionService $service)
    {
        if (!$request->user() || !($request->user()->isAdmin() || $request->user()->hasPermission('transacoes.create'))) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $data = $request->validate([
            'valor' => ['required','numeric','gt:0'],
            'moeda_id' => ['required','integer'],
            'descricao' => ['nullable','string','max:255'],
            'referencia_externa' => ['nullable','string','max:100']
        ]);
        Log::info('levantarConta called', ['user_id' => optional($request->user())->id, 'user_email' => optional($request->user())->email, 'conta_id' => $conta->id, 'payload' => $data]);
        try{
            $transacao = $service->withdraw($conta, (float)$data['valor'], (int)$data['moeda_id'], $data['descricao'] ?? null, $data['referencia_externa'] ?? null);
            Log::info('levantarConta result', ['transacao_id' => $transacao->id ?? null]);
            $contaFresh = Conta::with(['moeda'])->find($conta->id);
            $lastTransactions = \App\Models\Transacao::with(['tipoTransacao','statusTransacao','moeda'])
                ->where(function($q) use ($contaFresh){ $q->where('conta_origem_id', $contaFresh->id)->orWhere('conta_destino_id', $contaFresh->id); })
                ->orderBy('created_at','desc')->limit(5)->get();
            return response()->json(['message' => 'Levantamento efetuado', 'transacao' => $transacao, 'conta' => $contaFresh, 'lastTransactions' => $lastTransactions]);
        }catch(\Exception $e){
            Log::error('levantarConta error', ['error' => $e->getMessage(), 'payload' => $data, 'conta_id' => $conta->id]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function pagarConta(Request $request, Conta $conta, TransactionService $service)
    {
        if (!$request->user() || !($request->user()->isAdmin() || $request->user()->hasPermission('transacoes.create'))) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $data = $request->validate([
            'parceiro' => ['required','string','max:100'],
            'referencia' => ['required','string','max:100'],
            'valor' => ['required','numeric','gt:0'],
            'moeda_id' => ['required','integer'],
            'descricao' => ['nullable','string','max:255'],
        ]);
        Log::info('pagarConta called', ['user_id' => optional($request->user())->id, 'user_email' => optional($request->user())->email, 'conta_id' => $conta->id, 'payload' => $data]);
        try{
            $pagamento = $service->pay($conta, $data['parceiro'], $data['referencia'], (float)$data['valor'], (int)$data['moeda_id'], $data['descricao'] ?? null);
            Log::info('pagarConta result', ['pagamento_id' => $pagamento->id ?? null]);
            $contaFresh = Conta::with(['moeda'])->find($conta->id);
            $lastTransactions = \App\Models\Transacao::with(['tipoTransacao','statusTransacao','moeda'])
                ->where(function($q) use ($contaFresh){ $q->where('conta_origem_id', $contaFresh->id)->orWhere('conta_destino_id', $contaFresh->id); })
                ->orderBy('created_at','desc')->limit(5)->get();
            return response()->json(['message' => 'Pagamento efetuado', 'pagamento' => $pagamento, 'conta' => $contaFresh, 'lastTransactions' => $lastTransactions]);
        }catch(\Exception $e){
            Log::error('pagarConta error', ['error' => $e->getMessage(), 'payload' => $data, 'conta_id' => $conta->id]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function transferirConta(Request $request, Conta $conta, TransactionService $service)
    {
        if (!$request->user() || !($request->user()->isAdmin() || $request->user()->hasPermission('transacoes.create'))) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $data = $request->validate([
            'conta_destino_numero' => ['required','string'],
            'valor' => ['required','numeric','gt:0'],
            'moeda_id' => ['required','integer'],
            'descricao' => ['nullable','string','max:255'],
            'referencia_externa' => ['nullable','string','max:100']
        ]);
        Log::info('transferirConta called', ['user_id' => optional($request->user())->id, 'user_email' => optional($request->user())->email, 'conta_origem_id' => $conta->id, 'payload' => $data]);
        $dest = Conta::where('numero_conta', trim($data['conta_destino_numero']))->first();
        if(!$dest) return response()->json(['error' => 'Conta destino não encontrada'], 404);
        try{
            $transacao = $service->transferInternal($conta, $dest, (float)$data['valor'], (int)$data['moeda_id'], $data['descricao'] ?? null, $data['referencia_externa'] ?? null);
            Log::info('transferirConta result', ['transacao_id' => $transacao->id ?? null]);
            $contaFresh = Conta::with(['moeda'])->find($conta->id);
            $lastTransactions = \App\Models\Transacao::with(['tipoTransacao','statusTransacao','moeda'])
                ->where(function($q) use ($contaFresh){ $q->where('conta_origem_id', $contaFresh->id)->orWhere('conta_destino_id', $contaFresh->id); })
                ->orderBy('created_at','desc')->limit(5)->get();
            return response()->json(['message' => 'Transferência efetuada', 'transacao' => $transacao, 'conta' => $contaFresh, 'lastTransactions' => $lastTransactions]);
        }catch(\Exception $e){
            Log::error('transferirConta error', ['error' => $e->getMessage(), 'payload' => $data, 'conta_origem_id' => $conta->id]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function exportByContaMonthCsv($contaId, $ym)
    {
        $conta = \App\Models\Conta::find($contaId);
        if(!$conta) abort(404);

        try{
            $start = \Carbon\Carbon::parse($ym . '-01')->startOfMonth();
            $end = \Carbon\Carbon::parse($ym . '-01')->endOfMonth();
        }catch(\Exception $e){ abort(400); }

        $transacoes = Transacao::with(['tipoTransacao','statusTransacao'])
            ->where('conta_id', $conta->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at','desc')
            ->get();

        $filename = sprintf('transacoes_%s_%s.csv', $conta->numero_conta, $ym);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($transacoes) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','DataHora','Tipo','Valor','Status','Descricao']);
            foreach($transacoes as $t){
                fputcsv($out, [
                    $t->id,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->tipoTransacao->nome ?? '',
                    $t->valor,
                    $t->statusTransacao->nome ?? '',
                    $t->descricao ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}