<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\TipoCliente;
use App\Models\StatusCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with(['tipoCliente', 'statusCliente']);

        // Filtros
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('bi')) {
            $query->where('bi', 'like', '%' . $request->bi . '%');
        }

        if ($request->filled('tipo_cliente_id')) {
            $query->where('tipo_cliente_id', $request->tipo_cliente_id);
        }

        if ($request->filled('status_cliente_id')) {
            $query->where('status_cliente_id', $request->status_cliente_id);
        }

        $clientes = $query->paginate(15);
        $tiposCliente = TipoCliente::all();
        $statusCliente = StatusCliente::all();

        return view('admin.clientes.index', compact('clientes', 'tiposCliente', 'statusCliente'));
    }

    public function create()
    {
        $tiposCliente = TipoCliente::all();
        $statusCliente = StatusCliente::all();
        
        return view('admin.clientes.create', compact('tiposCliente', 'statusCliente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'bi' => 'required|string|max:20|unique:clientes,bi',
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|in:M,F',
            'tipo_cliente_id' => 'required|exists:tipos_cliente,id',
            'status_cliente_id' => 'required|exists:status_cliente,id',
            'endereco' => 'nullable|string|max:500',
            'cidade' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        $data['usuario_criacao'] = Auth::id();

        Cliente::create($data);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente criado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['tipoCliente', 'statusCliente', 'contas.tipoConta', 'contas.statusConta']);
        
        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $tiposCliente = TipoCliente::all();
        $statusCliente = StatusCliente::all();
        
        return view('admin.clientes.edit', compact('cliente', 'tiposCliente', 'statusCliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'bi' => 'required|string|max:20|unique:clientes,bi,' . $cliente->id,
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|in:M,F',
            'tipo_cliente_id' => 'required|exists:tipos_cliente,id',
            'status_cliente_id' => 'required|exists:status_cliente,id',
            'endereco' => 'nullable|string|max:500',
            'cidade' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        $data['usuario_atualizacao'] = Auth::id();

        $cliente->update($data);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        
        return redirect()->route('admin.clientes.index')->with('success', 'Cliente excluído com sucesso!');
    }
}