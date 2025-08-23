<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use App\Models\TipoTransacao;
use App\Models\StatusTransacao;
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

    public function show(Transacao $transacao)
    {
        $transacao->load(['conta.cliente', 'tipoTransacao', 'statusTransacao']);
        
        return view('admin.transacoes.show', compact('transacao'));
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

        // Determine available months for this account (format YYYY-MM) ordered desc
        $months = Transacao::where('conta_id', $conta->id)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, MIN(created_at) as first_date, MAX(created_at) as last_date")
            ->groupBy('ym')
            ->orderBy('ym','desc')
            ->get();

        if($months->isEmpty()){
            $html = view('admin.transacoes._by_conta_results', ['transacoes' => collect(), 'conta' => $conta, 'monthsPaginator' => null, 'currentYm' => null])->render();
            return response()->json(['html' => $html]);
        }

        $page = max(1, (int) $request->get('page', 1));
        $totalMonths = $months->count();
        if($page > $totalMonths) $page = $totalMonths;

        // pick the month for this page (1-based)
        $current = $months->values()->get($page - 1);
        $currentYm = $current->ym;

        // transactions for the selected month
        $start = \Carbon\Carbon::parse($currentYm . '-01')->startOfMonth();
        $end = \Carbon\Carbon::parse($currentYm . '-01')->endOfMonth();

        $transacoes = Transacao::with(['tipoTransacao','statusTransacao'])
            ->where('conta_id', $conta->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at','desc')
            ->get();

        // build a paginator for months (one month per page) so view can render page links
        $perPage = 1;
        $currentPage = $page;
        $monthsArray = $months->pluck('ym')->toArray();
        $monthsPaginator = new \Illuminate\Pagination\LengthAwarePaginator($monthsArray, $totalMonths, $perPage, $currentPage, [
            'path' => route('transacoes.searchByConta'),
            'query' => ['numero_conta' => $numero]
        ]);

        $html = view('admin.transacoes._by_conta_results', compact('transacoes','conta','monthsPaginator','currentYm'))->render();
        return response()->json(['html' => $html]);
    }
}