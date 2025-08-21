<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Conta;
use App\Models\TipoCartao;
use App\Models\StatusCartao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartaoWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Cartao::with(['conta.cliente', 'tipoCartao', 'statusCartao']);

        // Filtros
        if ($request->filled('numero_cartao')) {
            $query->where('numero_cartao', 'like', '%' . $request->numero_cartao . '%');
        }

        if ($request->filled('cliente_nome')) {
            $query->whereHas('conta.cliente', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->cliente_nome . '%');
            });
        }

        if ($request->filled('tipo_cartao_id')) {
            $query->where('tipo_cartao_id', $request->tipo_cartao_id);
        }

        if ($request->filled('status_cartao_id')) {
            $query->where('status_cartao_id', $request->status_cartao_id);
        }

        $cartoes = $query->paginate(15);
        $tiposCartao = TipoCartao::all();
        $statusCartao = StatusCartao::all();

        return view('admin.cartoes.index', compact('cartoes', 'tiposCartao', 'statusCartao'));
    }

    public function create()
    {
        $contas = Conta::with('cliente')->whereHas('statusConta', function($q) {
            $q->where('nome', 'ativa');
        })->get();
        $tiposCartao = TipoCartao::all();
        $statusCartao = StatusCartao::all();
        
        return view('admin.cartoes.create', compact('contas', 'tiposCartao', 'statusCartao'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'conta_id' => 'required|exists:contas,id',
            'tipo_cartao_id' => 'required|exists:tipo_cartoes,id',
            'status_cartao_id' => 'required|exists:status_cartoes,id',
            'limite_diario' => 'required|numeric|min:0',
            'limite_mensal' => 'required|numeric|min:0',
            'data_validade' => 'required|date|after:today',
        ]);

        $data = $request->all();
        $data['numero_cartao'] = $this->gerarNumeroCartao();
        $data['cvv'] = rand(100, 999);
        $data['usuario_criacao'] = Auth::id();

        Cartao::create($data);

        return redirect()->route('cartoes.index')->with('success', 'Cartão criado com sucesso!');
    }

    public function show(Cartao $cartao)
    {
        $cartao->load(['conta.cliente', 'tipoCartao', 'statusCartao']);
        
        return view('admin.cartoes.show', compact('cartao'));
    }

    public function edit(Cartao $cartao)
    {
        $contas = Conta::with('cliente')->whereHas('statusConta', function($q) {
            $q->where('nome', 'ativa');
        })->get();
        $tiposCartao = TipoCartao::all();
        $statusCartao = StatusCartao::all();
        
        return view('admin.cartoes.edit', compact('cartao', 'contas', 'tiposCartao', 'statusCartao'));
    }

    public function update(Request $request, Cartao $cartao)
    {
        $request->validate([
            'conta_id' => 'required|exists:contas,id',
            'tipo_cartao_id' => 'required|exists:tipo_cartoes,id',
            'status_cartao_id' => 'required|exists:status_cartoes,id',
            'limite_diario' => 'required|numeric|min:0',
            'limite_mensal' => 'required|numeric|min:0',
            'data_validade' => 'required|date|after:today',
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        $cartao->update($data);

        return redirect()->route('cartoes.index')->with('success', 'Cartão atualizado com sucesso!');
    }

    public function destroy(Cartao $cartao)
    {
        $cartao->delete();
        
        return redirect()->route('cartoes.index')->with('success', 'Cartão excluído com sucesso!');
    }

    private function gerarNumeroCartao()
    {
        do {
            $numero = '4000' . str_pad(rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (Cartao::where('numero_cartao', $numero)->exists());

        return $numero;
    }
}