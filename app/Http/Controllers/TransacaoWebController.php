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
}