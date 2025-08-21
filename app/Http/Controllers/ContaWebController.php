<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Cliente;
use App\Models\TipoConta;
use App\Models\StatusConta;
use App\Models\Moeda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContaWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Conta::with(['cliente', 'tipoConta', 'statusConta', 'moeda']);

        // Filtros
        if ($request->filled('numero_conta')) {
            $query->where('numero_conta', 'like', '%' . $request->numero_conta . '%');
        }

        if ($request->filled('cliente_nome')) {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->cliente_nome . '%');
            });
        }

        if ($request->filled('tipo_conta_id')) {
            $query->where('tipo_conta_id', $request->tipo_conta_id);
        }

        if ($request->filled('status_conta_id')) {
            $query->where('status_conta_id', $request->status_conta_id);
        }

        $contas = $query->paginate(15);
        $tiposConta = TipoConta::all();
        $statusConta = StatusConta::all();

        return view('admin.contas.index', compact('contas', 'tiposConta', 'statusConta'));
    }

    public function create()
    {
        $clientes = Cliente::where('status_cliente_id', function($query) {
            $query->select('id')->from('status_clientes')->where('nome', 'ativo');
        })->get();
        $tiposConta = TipoConta::all();
        $statusConta = StatusConta::all();
        $moedas = Moeda::all();
        
        return view('admin.contas.create', compact('clientes', 'tiposConta', 'statusConta', 'moedas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_conta_id' => 'required|exists:tipo_contas,id',
            'status_conta_id' => 'required|exists:status_contas,id',
            'moeda_id' => 'required|exists:moedas,id',
            'saldo_inicial' => 'required|numeric|min:0',
            'limite_credito' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['saldo'] = $request->saldo_inicial;
        $data['numero_conta'] = $this->gerarNumeroConta();
        $data['usuario_criacao'] = Auth::id();

        Conta::create($data);

        return redirect()->route('contas.index')->with('success', 'Conta criada com sucesso!');
    }

    public function show(Conta $conta)
    {
        $conta->load(['cliente', 'tipoConta', 'statusConta', 'moeda', 'transacoes.tipoTransacao']);
        
        return view('admin.contas.show', compact('conta'));
    }

    public function edit(Conta $conta)
    {
        $clientes = Cliente::where('status_cliente_id', function($query) {
            $query->select('id')->from('status_clientes')->where('nome', 'ativo');
        })->get();
        $tiposConta = TipoConta::all();
        $statusConta = StatusConta::all();
        $moedas = Moeda::all();
        
        return view('admin.contas.edit', compact('conta', 'clientes', 'tiposConta', 'statusConta', 'moedas'));
    }

    public function update(Request $request, Conta $conta)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_conta_id' => 'required|exists:tipo_contas,id',
            'status_conta_id' => 'required|exists:status_contas,id',
            'moeda_id' => 'required|exists:moedas,id',
            'limite_credito' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        $conta->update($data);

        return redirect()->route('contas.index')->with('success', 'Conta atualizada com sucesso!');
    }

    public function destroy(Conta $conta)
    {
        $conta->delete();
        
        return redirect()->route('contas.index')->with('success', 'Conta excluída com sucesso!');
    }

    private function gerarNumeroConta()
    {
        do {
            $numero = '1000' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Conta::where('numero_conta', $numero)->exists());

        return $numero;
    }
}