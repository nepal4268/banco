<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Conta;
use App\Models\TipoCartao;
use App\Models\StatusCartao;
use App\Models\StatusConta;
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
        // Force creation to be tied to a conta (only accessible from conta details)
        $contaId = request('conta_id');
        if (empty($contaId)) {
            // redirect back with error when trying to access the create form directly
            return redirect()->back()->with('error', 'Criação de cartão somente permitida a partir dos detalhes da conta.');
        }

        $conta = Conta::with('cliente')->find($contaId);
        if (!$conta) {
            return redirect()->back()->with('error', 'Conta não encontrada. Criação de cartão cancelada.');
        }

        $tiposCartao = TipoCartao::all();
        $statusCartao = StatusCartao::all();

    // Determine tipos already associated with this conta
    $tiposAssociados = $conta->cartoes()->pluck('tipo_cartao_id')->toArray();

    return view('admin.cartoes.create', compact('tiposCartao', 'statusCartao', 'conta', 'tiposAssociados'));
    }

    public function store(\App\Http\Requests\CartaoRequest $request)
    {
        $data = $request->validated();

        // Ensure tipo_cartao is not already associated with this conta
        if (!empty($data['conta_id']) && !empty($data['tipo_cartao_id'])) {
            $exists = Cartao::where('conta_id', $data['conta_id'])
                ->where('tipo_cartao_id', $data['tipo_cartao_id'])
                ->whereNull('deleted_at')
                ->exists();
            if ($exists) {
                return redirect()->back()->withInput()->withErrors(['tipo_cartao_id' => 'Este tipo de cartão já está associado a esta conta.']);
            }
        }

        // If the user provided a number, ensure it's digits-only; otherwise generate
        if (empty($data['numero_cartao'])) {
            $data['numero_cartao'] = $this->gerarNumeroCartao();
        } else {
            $data['numero_cartao'] = preg_replace('/\D/', '', $data['numero_cartao']);
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

    public function show(Cartao $carto)
    {
        $cartao = $carto;
        $cartao->load(['conta.cliente', 'tipoCartao', 'statusCartao']);

        return view('admin.cartoes.show', compact('cartao'));
    }

    public function edit(Cartao $carto)
    {
    $cartao = $carto;
    $cartao->load(['conta.cliente', 'tipoCartao', 'statusCartao']);
        $contas = Conta::with('cliente')->whereHas('statusConta', function($q) {
            $q->where('nome', 'ativa');
        })->get();
        $tiposCartao = TipoCartao::all();
        $statusCartao = StatusCartao::all();

        return view('admin.cartoes.edit', compact('cartao', 'contas', 'tiposCartao', 'statusCartao'));
    }

    public function update(Request $request, Cartao $carto)
    {
        $cartao = $carto;
        // Editing a card via this view only allows changing the status; optionally handle substitution
        $request->validate([
            'status_cartao_id' => 'required|exists:status_cartao,id',
            'novo_numero' => ['nullable','string'],
        ]);

        $statusId = $request->input('status_cartao_id');
    $cartao->update([
            'status_cartao_id' => $statusId,
            'usuario_atualizacao' => Auth::id(),
        ]);

        // If a novo_numero was provided, perform substitution (mark old as cancelado and create a replacement)
        $novoNumero = $request->input('novo_numero');
        if (!empty($novoNumero)) {
            // Clean and validate digits
            $clean = preg_replace('/\D/', '', $novoNumero);
            if (!preg_match('/^\d{16}$/', $clean)) {
                return back()->withInput()->withErrors(['novo_numero' => 'Número inválido: deve conter exatamente 16 dígitos.']);
            }

            // Check uniqueness by hash
            $hash = hash('sha256', $clean);
            if (Cartao::where('numero_cartao_hash', $hash)->exists()) {
                return back()->withInput()->withErrors(['novo_numero' => 'Já existe um cartão com esse número.']);
            }

            $statusCancelado = StatusCartao::where('nome', 'Cancelado')->first();
            $statusAtivo = StatusCartao::where('nome', 'Ativo')->first();

            // mark old as cancelado
            if ($statusCancelado) {
                $cartao->update(['status_cartao_id' => $statusCancelado->id, 'usuario_atualizacao' => Auth::id()]);
            }

            // create replacement
            $novo = Cartao::create([
                'conta_id' => $cartao->conta_id,
                'tipo_cartao_id' => $cartao->tipo_cartao_id,
                'numero_cartao' => $clean,
                'validade' => now()->addYears(3)->endOfMonth()->toDateString(),
                'limite' => $cartao->limite,
                'status_cartao_id' => $statusAtivo ? $statusAtivo->id : $cartao->status_cartao_id,
                'cvv' => rand(100,999),
                'usuario_criacao' => Auth::id(),
            ]);

            $last4 = $novo->numero_cartao ? substr($novo->numero_cartao, -4) : null;

            if ($cartao->conta_id) {
                return redirect()->route('admin.contas.show', $cartao->conta_id)->with('success', 'Cartão substituído com sucesso' . ($last4 ? ' (últimos 4: ' . $last4 . ')' : ''));
            }

            return redirect()->route('cartoes.index')->with('success', 'Cartão substituído com sucesso' . ($last4 ? ' (últimos 4: ' . $last4 . ')' : ''));
        }

        // No substitution: normal status update
        if ($cartao->conta_id) {
            return redirect()->route('admin.contas.show', $cartao->conta_id)->with('success', 'Status do cartão atualizado com sucesso!');
        }

        return redirect()->route('cartoes.index')->with('success', 'Status do cartão atualizado com sucesso!');
    }

    public function destroy(Cartao $carto)
    {
        $cartao = $carto;
        $cartao->delete();

        return redirect()->route('cartoes.index')->with('success', 'Cartão excluído com sucesso!');
    }

    // Bloquear cartão (web)
    public function bloquear(Request $request, Cartao $carto)
    {
        $cartao = $carto;
        $statusBloqueado = StatusCartao::where('nome', 'Bloqueado')->first();
        if (!$statusBloqueado) {
            return back()->with('error', 'Status Bloqueado não encontrado');
        }

        $cartao->update(['status_cartao_id' => $statusBloqueado->id]);
        return back()->with('success', 'Cartão bloqueado com sucesso');
    }

    // Ativar cartão (web)
    public function ativar(Request $request, Cartao $carto)
    {
        $cartao = $carto;
        $statusAtivo = StatusCartao::where('nome', 'Ativo')->first();
        if (!$statusAtivo) {
            return back()->with('error', 'Status Ativo não encontrado');
        }

        $cartao->update(['status_cartao_id' => $statusAtivo->id]);
        return back()->with('success', 'Cartão ativado com sucesso');
    }

    // Substituir cartão: generate new card linked to same conta, set old to Cancelado
    public function substituir(Request $request, Cartao $carto)
    {
        $cartao = $carto;
        $statusCancelado = StatusCartao::where('nome', 'Cancelado')->first();
        $statusAtivo = StatusCartao::where('nome', 'Ativo')->first();
        // Determine eligibility using status IDs: allowed when card status is Bloqueado|Expirado|Cancelado
        // or when the linked conta is not active (inactive or missing). This follows the UI rule.
        $allowedStatusIds = StatusCartao::whereIn('nome', ['Bloqueado', 'Expirado', 'Cancelado'])->pluck('id')->toArray();
        $cartaoStatusId = $cartao->status_cartao_id;
        $conta = $cartao->conta;
        $contaAtivoId = StatusConta::where('nome', 'Ativo')->value('id');
        $contaAtiva = $conta && $conta->status_conta_id === $contaAtivoId;

        if (!in_array($cartaoStatusId, $allowedStatusIds) && $contaAtiva) {
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

        // Validate novo_numero if provided: must be 16 digits and unique
        $novoNumero = $request->input('novo_numero');
        if (!empty($novoNumero)) {
            // strip non-digits
            $clean = preg_replace('/\D/', '', $novoNumero);
            if (!preg_match('/^\d{16}$/', $clean)) {
                return back()->withInput()->withErrors(['novo_numero' => 'Número inválido: deve conter exatamente 16 dígitos.']);
            }
            $hash = hash('sha256', $clean);
            if (Cartao::where('numero_cartao_hash', $hash)->exists()) {
                return back()->withInput()->withErrors(['novo_numero' => 'Já existe um cartão com esse número.']);
            }
            $novoNumero = $clean;
        }

        // If not provided, generate
        if (empty($novoNumero)) {
            $novoNumero = $this->gerarNumeroCartao();
        }

        // create replacement card linked to same conta
        $novo = Cartao::create([
            'conta_id' => $conta->id,
            'tipo_cartao_id' => $cartao->tipo_cartao_id,
            'numero_cartao' => $novoNumero,
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