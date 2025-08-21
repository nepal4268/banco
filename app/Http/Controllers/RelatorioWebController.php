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
        $clientesPorTipo = Cliente::select('tipos_cliente.nome', DB::raw('COUNT(*) as total'))
            ->join('tipos_cliente', 'clientes.tipo_cliente_id', '=', 'tipos_cliente.id')
            ->groupBy('tipos_cliente.id', 'tipos_cliente.nome')
            ->get();

        $clientesPorStatus = Cliente::select('status_cliente.nome', DB::raw('COUNT(*) as total'))
            ->join('status_cliente', 'clientes.status_cliente_id', '=', 'status_cliente.id')
            ->groupBy('status_cliente.id', 'status_cliente.nome')
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
        $transacoesPorTipo = Transacao::select('tipos_transacao.nome', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor) as valor_total'))
            ->join('tipos_transacao', 'transacoes.tipo_transacao_id', '=', 'tipos_transacao.id')
            ->groupBy('tipos_transacao.id', 'tipos_transacao.nome')
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
        $contasPorTipo = Conta::select('tipos_conta.nome', DB::raw('COUNT(*) as total'), DB::raw('SUM(saldo) as saldo_total'))
            ->join('tipos_conta', 'contas.tipo_conta_id', '=', 'tipos_conta.id')
            ->groupBy('tipos_conta.id', 'tipos_conta.nome')
            ->get();

        $contasPorStatus = Conta::select('status_conta.nome', DB::raw('COUNT(*) as total'))
            ->join('status_conta', 'contas.status_conta_id', '=', 'status_conta.id')
            ->groupBy('status_conta.id', 'status_conta.nome')
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