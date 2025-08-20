<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\TipoCliente;
use App\Models\StatusCliente;
use Illuminate\Http\Request;
use App\Http\Requests\ClienteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
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

        // Paginação
        $perPage = $request->get('per_page', 15);
        $clientes = $query->paginate($perPage);

        return response()->json($clientes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClienteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cliente = Cliente::create($validated);
        $cliente->load(['tipoCliente', 'statusCliente']);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'cliente' => $cliente
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente): JsonResponse
    {
        $cliente->load(['tipoCliente', 'statusCliente', 'contas.tipoConta', 'contas.moeda', 'contas.statusConta']);

        return response()->json($cliente);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $validated = $request->validated();

        $cliente->update($validated);
        $cliente->load(['tipoCliente', 'statusCliente']);

        return response()->json([
            'message' => 'Cliente atualizado com sucesso',
            'cliente' => $cliente
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente): JsonResponse
    {
        // Verificar se cliente tem contas ativas
        $contasAtivas = $cliente->contas()->whereHas('statusConta', function($query) {
            $query->where('nome', 'Ativa');
        })->count();

        if ($contasAtivas > 0) {
            return response()->json([
                'message' => 'Não é possível excluir cliente com contas ativas'
            ], 422);
        }

        $cliente->delete();

        return response()->json([
            'message' => 'Cliente excluído com sucesso'
        ]);
    }

    /**
     * Get lookup data for client form
     */
    public function lookups(): JsonResponse
    {
        return response()->json([
            'tipos_cliente' => TipoCliente::all(),
            'status_cliente' => StatusCliente::all(),
        ]);
    }
}