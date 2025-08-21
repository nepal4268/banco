<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Conta;
use App\Models\Transacao;
use App\Models\Cartao;
use App\Models\Apolice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estatísticas principais
        $totalClientes = Cliente::count();
        $totalContas = Conta::count();
        $totalTransacoes = Transacao::count();
        $totalCartoes = Cartao::count();

        // Transações por mês (últimos 6 meses)
        $transacoesPorMes = Transacao::select(
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('YEAR(created_at) as ano'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(valor) as valor_total')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(6))
        ->groupBy('ano', 'mes')
        ->orderBy('ano', 'desc')
        ->orderBy('mes', 'desc')
        ->get();

        // Clientes por tipo
        $clientesPorTipo = Cliente::select('tipos_cliente.nome', DB::raw('COUNT(*) as total'))
            ->join('tipos_cliente', 'clientes.tipo_cliente_id', '=', 'tipos_cliente.id')
            ->groupBy('tipos_cliente.id', 'tipos_cliente.nome')
            ->get();

        // Contas por status (ajustado para status_conta no singular)
        $contasPorStatus = Conta::select('status_conta.nome', DB::raw('COUNT(*) as total'))
            ->join('status_conta', 'contas.status_conta_id', '=', 'status_conta.id')
            ->groupBy('status_conta.id', 'status_conta.nome')
            ->get();

        // Últimas transações
        $ultimasTransacoes = Transacao::with(['conta.cliente', 'tipoTransacao'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Saldo total das contas
        $saldoTotal = Conta::sum('saldo');

        // Novos clientes este mês
        $novosClientesMes = Cliente::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Apólices ativas
        $apolicesAtivas = Apolice::whereHas('statusApolice', function($q) {
            $q->where('nome', 'Ativa'); // cuidado com maiúsculas/minúsculas
        })->count();

        return view('dashboard', compact(
            'totalClientes',
            'totalContas', 
            'totalTransacoes',
            'totalCartoes',
            'transacoesPorMes',
            'clientesPorTipo',
            'contasPorStatus',
            'ultimasTransacoes',
            'saldoTotal',
            'novosClientesMes',
            'apolicesAtivas'
        ));
    }
}
