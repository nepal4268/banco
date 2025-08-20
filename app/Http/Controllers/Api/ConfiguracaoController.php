<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{
    TipoCliente, TipoConta, TipoCartao, TipoSeguro, TipoTransacao,
    StatusCliente, StatusConta, StatusCartao, StatusPagamento, 
    StatusSinistro, StatusTransacao, StatusApolice
};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConfiguracaoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/configuracoes/tipos",
     *     summary="Obter todos os tipos do sistema",
     *     tags={"Configuração"},
     *     @OA\Response(response=200, description="Tipos do sistema")
     * )
     */
    public function tipos(): JsonResponse
    {
        return response()->json([
            'tipos_cliente' => TipoCliente::all(),
            'tipos_conta' => TipoConta::all(),
            'tipos_cartao' => TipoCartao::all(),
            'tipos_seguro' => TipoSeguro::all(),
            'tipos_transacao' => TipoTransacao::all(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/configuracoes/status",
     *     summary="Obter todos os status do sistema",
     *     tags={"Configuração"},
     *     @OA\Response(response=200, description="Status do sistema")
     * )
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'status_cliente' => StatusCliente::all(),
            'status_conta' => StatusConta::all(),
            'status_cartao' => StatusCartao::all(),
            'status_pagamento' => StatusPagamento::all(),
            'status_sinistro' => StatusSinistro::all(),
            'status_transacao' => StatusTransacao::all(),
            'status_apolice' => StatusApolice::all(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/configuracoes/lookups",
     *     summary="Obter todos os lookups do sistema",
     *     tags={"Configuração"},
     *     @OA\Response(response=200, description="Todos os lookups")
     * )
     */
    public function lookups(): JsonResponse
    {
        return response()->json([
            'tipos' => [
                'cliente' => TipoCliente::all(),
                'conta' => TipoConta::all(),
                'cartao' => TipoCartao::all(),
                'seguro' => TipoSeguro::all(),
                'transacao' => TipoTransacao::all(),
            ],
            'status' => [
                'cliente' => StatusCliente::all(),
                'conta' => StatusConta::all(),
                'cartao' => StatusCartao::all(),
                'pagamento' => StatusPagamento::all(),
                'sinistro' => StatusSinistro::all(),
                'transacao' => StatusTransacao::all(),
                'apolice' => StatusApolice::all(),
            ]
        ]);
    }

    // Endpoints específicos para cada tipo
    
    /**
     * @OA\Get(path="/api/tipos-cliente", summary="Listar tipos de cliente", tags={"Configuração"})
     */
    public function tiposCliente(): JsonResponse
    {
        return response()->json(TipoCliente::all());
    }

    /**
     * @OA\Get(path="/api/tipos-conta", summary="Listar tipos de conta", tags={"Configuração"})
     */
    public function tiposConta(): JsonResponse
    {
        return response()->json(TipoConta::all());
    }

    /**
     * @OA\Get(path="/api/tipos-cartao", summary="Listar tipos de cartão", tags={"Configuração"})
     */
    public function tiposCartao(): JsonResponse
    {
        return response()->json(TipoCartao::all());
    }

    /**
     * @OA\Get(path="/api/tipos-seguro", summary="Listar tipos de seguro", tags={"Configuração"})
     */
    public function tiposSeguro(): JsonResponse
    {
        return response()->json(TipoSeguro::all());
    }

    /**
     * @OA\Get(path="/api/tipos-transacao", summary="Listar tipos de transação", tags={"Configuração"})
     */
    public function tiposTransacao(): JsonResponse
    {
        return response()->json(TipoTransacao::all());
    }

    // Endpoints para status

    /**
     * @OA\Get(path="/api/status-cliente", summary="Listar status de cliente", tags={"Configuração"})
     */
    public function statusCliente(): JsonResponse
    {
        return response()->json(StatusCliente::all());
    }

    /**
     * @OA\Get(path="/api/status-conta", summary="Listar status de conta", tags={"Configuração"})
     */
    public function statusConta(): JsonResponse
    {
        return response()->json(StatusConta::all());
    }

    /**
     * @OA\Get(path="/api/status-cartao", summary="Listar status de cartão", tags={"Configuração"})
     */
    public function statusCartao(): JsonResponse
    {
        return response()->json(StatusCartao::all());
    }

    /**
     * @OA\Get(path="/api/status-pagamento", summary="Listar status de pagamento", tags={"Configuração"})
     */
    public function statusPagamento(): JsonResponse
    {
        return response()->json(StatusPagamento::all());
    }

    /**
     * @OA\Get(path="/api/status-sinistro", summary="Listar status de sinistro", tags={"Configuração"})
     */
    public function statusSinistro(): JsonResponse
    {
        return response()->json(StatusSinistro::all());
    }

    /**
     * @OA\Get(path="/api/status-transacao", summary="Listar status de transação", tags={"Configuração"})
     */
    public function statusTransacao(): JsonResponse
    {
        return response()->json(StatusTransacao::all());
    }

    /**
     * @OA\Get(path="/api/status-apolice", summary="Listar status de apólice", tags={"Configuração"})
     */
    public function statusApolice(): JsonResponse
    {
        return response()->json(StatusApolice::all());
    }

    /**
     * @OA\Post(
     *     path="/api/tipos-cliente",
     *     summary="Criar tipo de cliente",
     *     tags={"Configuração"},
     *     @OA\RequestBody(@OA\JsonContent(
     *         required={"nome"},
     *         @OA\Property(property="nome", type="string", maxLength=30)
     *     )),
     *     @OA\Response(response=201, description="Tipo criado")
     * )
     */
    public function storeTipoCliente(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:30', 'unique:tipos_cliente,nome']
        ]);

        $tipo = TipoCliente::create($validated);
        
        return response()->json([
            'message' => 'Tipo de cliente criado com sucesso',
            'tipo' => $tipo
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/status-cliente",
     *     summary="Criar status de cliente",
     *     tags={"Configuração"},
     *     @OA\RequestBody(@OA\JsonContent(
     *         required={"nome"},
     *         @OA\Property(property="nome", type="string", maxLength=20)
     *     )),
     *     @OA\Response(response=201, description="Status criado")
     * )
     */
    public function storeStatusCliente(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:20', 'unique:status_cliente,nome']
        ]);

        $status = StatusCliente::create($validated);
        
        return response()->json([
            'message' => 'Status de cliente criado com sucesso',
            'status' => $status
        ], 201);
    }

    // Métodos similares podem ser criados para outros tipos/status conforme necessário
}