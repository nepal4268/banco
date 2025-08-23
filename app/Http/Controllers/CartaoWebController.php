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

        $conta = null;
        if (request()->filled('conta_id')) {
            $conta = Conta::with('cliente')->find(request('conta_id'));
        }

        return view('admin.cartoes.create', compact('contas', 'tiposCartao', 'statusCartao', 'conta'));
    }

    public function store(\App\Http\Requests\CartaoRequest $request)
    {
        $data = $request->validated();

        if (empty($data['numero_cartao'])) {
            $data['numero_cartao'] = $this->gerarNumeroCartao();
        }

        $data['cvv'] = rand(100, 999);
        $data['usuario_criacao'] = Auth::id();

        $cartao = Cartao::create($data);

        // Redirect back to conta view if possible
        if ($cartao->conta_id) {
            return redirect()->route('admin.contas.show', $cartao->conta_id)->with('success', 'Cartão criado com sucesso!');
        }

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
            'tipo_cartao_id' => 'required|exists:tipos_cartao,id',
            'status_cartao_id' => 'required|exists:status_cartao,id',
            'validade' => 'required|date|after:today',
            'limite' => ['nullable','numeric','min:0'],
        ]);

        $data = $request->only(['conta_id','tipo_cartao_id','status_cartao_id','validade','limite']);
        $data['usuario_atualizacao'] = Auth::id();

        $cartao->update($data);

        return redirect()->route('cartoes.index')->with('success', 'Cartão atualizado com sucesso!');
    }

    public function destroy(Cartao $cartao)
    {
        $cartao->delete();
        
        return redirect()->route('cartoes.index')->with('success', 'Cartão excluído com sucesso!');
    }

    // Bloquear cartão (web)
    public function bloquear(Request $request, Cartao $cartao)
    {
        $statusBloqueado = StatusCartao::where('nome', 'Bloqueado')->first();
        if (!$statusBloqueado) {
            return back()->with('error', 'Status Bloqueado não encontrado');
        }

        $cartao->update(['status_cartao_id' => $statusBloqueado->id]);
        return back()->with('success', 'Cartão bloqueado com sucesso');
    }

    // Ativar cartão (web)
    public function ativar(Request $request, Cartao $cartao)
    {
        $statusAtivo = StatusCartao::where('nome', 'Ativo')->first();
        if (!$statusAtivo) {
            return back()->with('error', 'Status Ativo não encontrado');
        }

        $cartao->update(['status_cartao_id' => $statusAtivo->id]);
        return back()->with('success', 'Cartão ativado com sucesso');
    }

    // Substituir cartão: generate new card linked to same conta, set old to Cancelado
    public function substituir(Request $request, Cartao $cartao)
    {
        $statusCancelado = StatusCartao::where('nome', 'Cancelado')->first();
        $statusAtivo = StatusCartao::where('nome', 'Ativo')->first();
        // Determine eligibility: allowed when card status is Bloqueado|Expirado|Cancelado
        // or when the linked conta is not active (inactive or missing). This follows the UI rule.
        $allowed = ['Bloqueado', 'Expirado', 'Cancelado'];
        $statusNome = optional($cartao->statusCartao)->nome;
        $conta = $cartao->conta;
        $contaAtiva = $conta && optional($conta->statusConta)->nome === 'Ativo';

        if (!in_array($statusNome, $allowed) && $contaAtiva) {
            return back()->with('error', 'Substituição somente permitida para cartões Bloqueado, Expirado, Cancelado ou quando a conta não estiver activa.');
        }

        // mark old as cancelado
        if ($statusCancelado) {
            $cartao->update(['status_cartao_id' => $statusCancelado->id]);
        }

        // Cannot create a replacement card without a conta due to DB FK constraints
        if (empty($conta)) {
            return back()->with('error', 'Não é possível criar substituto: conta associada não encontrada.');
        }

        // create replacement card linked to same conta
        $novo = Cartao::create([
            'conta_id' => $conta->id,
            'tipo_cartao_id' => $cartao->tipo_cartao_id,
            'numero_cartao' => $this->gerarNumeroCartao(),
            'validade' => now()->addYears(3)->endOfMonth()->toDateString(),
            'limite' => $cartao->limite,
            'status_cartao_id' => $statusAtivo ? $statusAtivo->id : $cartao->status_cartao_id,
            'cvv' => rand(100,999),
            'usuario_criacao' => Auth::id(),
        ]);

        // Provide the last 4 digits to the user
        $last4 = $novo->numero_cartao ? substr($novo->numero_cartao, -4) : null;

        return back()->with('success', 'Cartão substituído com sucesso' . ($last4 ? ' (últimos 4: ' . $last4 . ')' : ''));
    }

    private function gerarNumeroCartao()
    {
        do {
            $numero = '4000' . str_pad(rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
            $hash = hash('sha256', $numero);
        } while (Cartao::where('numero_cartao_hash', $hash)->exists());

        return $numero;
    }
}