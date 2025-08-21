<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Transacao;
use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatorioWebController extends Controller
{
    public function clientes(Request $request)
    {
        $query = Cliente::with(['tipoCliente', 'statusCliente']);

        // Filtros por data
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        // Estatísticas
        $totalClientes = $query->count();
        $clientesPorTipo = Cliente::select('tipo_clientes.nome', DB::raw('COUNT(*) as total'))
            ->join('tipo_clientes', 'clientes.tipo_cliente_id', '=', 'tipo_clientes.id')
            ->groupBy('tipo_clientes.id', 'tipo_clientes.nome')
            ->get();

        $clientesPorStatus = Cliente::select('status_clientes.nome', DB::raw('COUNT(*) as total'))
            ->join('status_clientes', 'clientes.status_cliente_id', '=', 'status_clientes.id')
            ->groupBy('status_clientes.id', 'status_clientes.nome')
            ->get();

        $clientes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.relatorios.clientes', compact(
            'clientes', 
            'totalClientes', 
            'clientesPorTipo', 
            'clientesPorStatus'
        ));
    }

    public function transacoes(Request $request)
    {
        $query = Transacao::with(['conta.cliente', 'tipoTransacao', 'statusTransacao']);

        // Filtros por data
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        // Estatísticas
        $totalTransacoes = $query->count();
        $valorTotal = $query->sum('valor');
        $transacoesPorTipo = Transacao::select('tipo_transacoes.nome', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor) as valor_total'))
            ->join('tipo_transacoes', 'transacoes.tipo_transacao_id', '=', 'tipo_transacoes.id')
            ->groupBy('tipo_transacoes.id', 'tipo_transacoes.nome')
            ->get();

        $transacoesPorMes = Transacao::select(
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('YEAR(created_at) as ano'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(valor) as valor_total')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('ano', 'mes')
        ->orderBy('ano', 'desc')
        ->orderBy('mes', 'desc')
        ->get();

        $transacoes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.relatorios.transacoes', compact(
            'transacoes', 
            'totalTransacoes', 
            'valorTotal',
            'transacoesPorTipo',
            'transacoesPorMes'
        ));
    }

    public function contas(Request $request)
    {
        $query = Conta::with(['cliente', 'tipoConta', 'statusConta', 'moeda']);

        // Filtros por data
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        // Estatísticas
        $totalContas = $query->count();
        $saldoTotal = $query->sum('saldo');
        $contasPorTipo = Conta::select('tipo_contas.nome', DB::raw('COUNT(*) as total'), DB::raw('SUM(saldo) as saldo_total'))
            ->join('tipo_contas', 'contas.tipo_conta_id', '=', 'tipo_contas.id')
            ->groupBy('tipo_contas.id', 'tipo_contas.nome')
            ->get();

        $contasPorStatus = Conta::select('status_contas.nome', DB::raw('COUNT(*) as total'))
            ->join('status_contas', 'contas.status_conta_id', '=', 'status_contas.id')
            ->groupBy('status_contas.id', 'status_contas.nome')
            ->get();

        $contas = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.relatorios.contas', compact(
            'contas', 
            'totalContas', 
            'saldoTotal',
            'contasPorTipo',
            'contasPorStatus'
        ));
    }
}