<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Cliente;
use App\Models\TipoConta;
use App\Models\StatusConta;
use App\Models\Moeda;
use App\Models\Agencia;
use Illuminate\Http\Request;
use App\Http\Requests\ContaStoreRequest;
use App\Http\Requests\ContaUpdateRequest;
use Illuminate\Support\Facades\Auth;
use function bcmod;

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

    // Form to enter BI
    public function findByBiForm()
    {
        return view('admin.contas.find-by-bi');
    }

    // Handle BI lookup
    public function findByBi(Request $request)
    {
        $request->validate([ 'bi' => 'required|string' ]);

        $cliente = Cliente::where('bi', $request->bi)->first();
        // If AJAX/JSON request, return structured JSON and do not redirect
        if ($request->expectsJson()) {
            if (!$cliente) {
                return response()->json([ 'error' => 'Cliente não encontrado. Cadastre primeiro.', 'action' => 'register' ], 404);
            }

            $contas = $cliente->contas()->with(['tipoConta', 'moeda', 'statusConta'])->get()->map(function($c){
                return [
                    'id' => $c->id,
                    'numero_conta' => $c->numero_conta,
                    'tipo_conta_id' => $c->tipo_conta_id,
                    'tipo_conta_nome' => $c->tipoConta->nome ?? null,
                    'moeda_id' => $c->moeda_id,
                    'moeda_codigo' => $c->moeda->codigo ?? null,
                    'status' => $c->statusConta->nome ?? null,
                ];
            });

            // Determine if cliente is pessoa fisica (simple heuristic on tipoCliente name)
            $isPessoaFisica = false;
            if ($cliente->tipoCliente && isset($cliente->tipoCliente->nome)) {
                $nomeTipo = mb_strtolower($cliente->tipoCliente->nome);
                if (str_contains($nomeTipo, 'fisic') || str_contains($nomeTipo, 'pessoa')) {
                    $isPessoaFisica = true;
                }
            }

            return response()->json([
                'cliente_id' => $cliente->id,
                'cliente_nome' => $cliente->nome,
                'cliente_bi' => $cliente->bi,
                'is_pessoa_fisica' => $isPessoaFisica,
                'contas' => $contas,
            ]);
        }

        // Non-AJAX fallback: redirect to create form for client
        if (!$cliente) {
            return redirect()->route('admin.clientes.create')->with('error', 'Cliente não encontrado. Cadastre primeiro.');
        }

        return redirect()->route('admin.contas.createForClient', $cliente->id);
    }

    // Create account form pre-filled for a given client
    public function createForClient(Cliente $cliente)
    {
        $tiposConta = TipoConta::all();
        $statusConta = StatusConta::all();
        $moedas = Moeda::all();
        $agencias = Agencia::all();

        // Pass existing contas summary so frontend can restrict tipo/moeda combos
        $existingContas = $cliente->contas()->with(['tipoConta','moeda'])->get()->map(function($c){
            return [ 'tipo_conta_id' => $c->tipo_conta_id, 'moeda_id' => $c->moeda_id, 'tipo_nome' => $c->tipoConta->nome ?? null, 'moeda_codigo' => $c->moeda->codigo ?? null ];
        });

        return view('admin.contas.create', compact('cliente', 'tiposConta', 'statusConta', 'moedas', 'agencias', 'existingContas'));
    }

    /**
     * Generate an account number and IBAN for frontend via AJAX.
     */
    public function generateAccount(Request $request)
    {
        // Expect agencia_id as numeric id; try to load agency to get codes
        $agenciaIdParam = $request->query('agencia_id', auth()->user()->agencia_id ?? null);
        $agencia = null;
        if ($agenciaIdParam) {
            $agencia = Agencia::find($agenciaIdParam);
        }

    // If we can load an Agencia, prefer model-driven generation to keep format consistent
    if ($agencia) {
            // Use the model helper which uses agencia codes and sequential logic
            $numero = Conta::gerarNumeroConta($agencia);

            // Create temporary Conta instance to compute IBAN via model method
            $tmp = new Conta();
            $tmp->agencia = $agencia;
            $tmp->numero_conta = $numero;
            $iban = $tmp->gerarIban();

            return response()->json(['numero_conta' => $numero, 'iban' => $iban]);
        }

        // Fallback when no agencia available: generate using default codes
    $codigoAgencia = str_pad(intval($agenciaIdParam ?? 0), 4, '0', STR_PAD_LEFT);
    // default bank code is 0042
    $codigoBanco = '0042';
        $accountPart = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $numero = $codigoAgencia . $accountPart;
        $bban = $codigoBanco . $codigoAgencia . $accountPart;

        // Calculate IBAN check digits (mod-97) using string arithmetic
        $countryCode = 'AO';
        $rearranged = $bban . $countryCode . '00';
        $converted = '';
        foreach (str_split($rearranged) as $ch) {
            if (ctype_alpha($ch)) {
                $converted .= ord($ch) - 55;
            } else {
                $converted .= $ch;
            }
        }

        // compute mod 97 in chunks without bcmod if not available
        $remainder = 0;
        $len = strlen($converted);
        $pos = 0;
        while ($pos < $len) {
            $take = min(9, $len - $pos);
            $num = $remainder . substr($converted, $pos, $take);
            $remainder = intval($num) % 97;
            $pos += $take;
        }
        $checkDigits = 98 - $remainder;
        $checkDigits = str_pad($checkDigits, 2, '0', STR_PAD_LEFT);

        $iban = $countryCode . $checkDigits . $bban;

        return response()->json(['numero_conta' => $numero, 'iban' => $iban]);
    }

    public function create()
    {
        $clientes = Cliente::where('status_cliente_id', function($query) {
            $query->select('id')->from('status_cliente')->where('nome', 'ativo');
        })->get();
    $tiposConta = TipoConta::all();
    $statusConta = StatusConta::all();
    $moedas = Moeda::all();
    $agencias = Agencia::all();
        
    return view('admin.contas.create', compact('clientes', 'tiposConta', 'statusConta', 'moedas', 'agencias'));
    }

    public function store(ContaStoreRequest $request)
    {
        $data = $request->validated();
        $data['saldo'] = $request->saldo_inicial;
        // Prefer frontend-generated numero/iban when present
        if ($request->filled('numero_conta')) {
            $data['numero_conta'] = $request->numero_conta;
        } else {
            $data['numero_conta'] = $this->gerarNumeroConta();
        }
        if ($request->filled('iban')) {
            $data['iban'] = $request->iban;
        }

        // Ensure agencia_id exists: prefer request, then user's agencia, otherwise null -> DB may require it
        if ($request->filled('agencia_id')) {
            $data['agencia_id'] = $request->agencia_id;
        } elseif (Auth::user() && !Auth::user()->isAdmin()) {
            $data['agencia_id'] = Auth::user()->agencia_id;
        }
        $data['usuario_criacao'] = Auth::id();

    // make sure numero_conta and iban are strings (avoid numeric trimming)
    $data['numero_conta'] = (string) $data['numero_conta'];
    if (isset($data['iban'])) $data['iban'] = (string) $data['iban'];

    $conta = Conta::create($data);

        // If created for a specific client, redirect to that client's contas list
        if (!empty($data['cliente_id'])) {
            return redirect()->route('admin.clientes.show', $data['cliente_id'])->with('success', 'Conta criada com sucesso!');
        }

        return redirect()->route('admin.contas.index')->with('success', 'Conta criada com sucesso!');
    }

    public function show(Conta $conta)
    {
        $conta->load([
            'cliente', 
            'tipoConta', 
            'statusConta', 
            'moeda', 
            'transacoesOrigem.tipoTransacao',
            'transacoesDestino.tipoTransacao'
        ]);
        
        // Combine as transações de origem e destino para a view
        $transacoes = $conta->transacoes()->get();
        
        return view('admin.contas.show', compact('conta', 'transacoes'));
    }

    public function edit(Conta $conta)
    {
        $clientes = Cliente::where('status_cliente_id', function($query) {
            $query->select('id')->from('status_cliente')->where('nome', 'ativo');
        })->get();
    $tiposConta = TipoConta::all();
    $statusConta = StatusConta::all();
    $moedas = Moeda::all();
    $agencias = Agencia::all();
        
    return view('admin.contas.edit', compact('conta', 'clientes', 'tiposConta', 'statusConta', 'moedas', 'agencias'));
    }

    public function update(ContaUpdateRequest $request, Conta $conta)
    {
        $data = $request->validated();
        $data['usuario_atualizacao'] = Auth::id();

        $conta->update($data);

    return redirect()->route('admin.contas.index')->with('success', 'Conta atualizada com sucesso!');
    }

    public function destroy(Conta $conta)
    {
        $conta->delete();
        
    return redirect()->route('admin.contas.index')->with('success', 'Conta excluída com sucesso!');
    }

    private function gerarNumeroConta()
    {
        do {
            $numero = '1000' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Conta::where('numero_conta', $numero)->exists());

        return $numero;
    }
}