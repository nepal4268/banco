<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApoliceRequest;
use App\Models\Apolice;
use App\Models\Sinistro;
use Illuminate\Http\Request;
use App\Http\Requests\SinistroRequest;
use Illuminate\Http\JsonResponse;

class SeguroController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/seguros/apolices",
     *     summary="Listar apólices de seguro",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="cliente_id", in="query", @OA\Schema(type="integer"), description="Filtrar por cliente"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string"), description="Filtrar por status"),
     *     @OA\Response(response=200, description="Lista paginada de apólices")
     * )
     */
    public function indexApolices(Request $request): JsonResponse
    {
        $query = Apolice::with(['cliente', 'tipoSeguro', 'statusApolice']);
        
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        
        if ($request->filled('status')) {
            $query->whereHas('statusApolice', function($q) use ($request) {
                $q->where('nome', $request->status);
            });
        }
        
        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    /**
     * @OA\Post(
     *     path="/api/seguros/apolices",
     *     summary="Criar nova apólice",
     *     tags={"Seguros"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cliente_id", "tipo_seguro_id", "valor_segurado", "premio"},
     *             @OA\Property(property="cliente_id", type="integer", example=1),
     *             @OA\Property(property="tipo_seguro_id", type="integer", example=1),
     *             @OA\Property(property="valor_segurado", type="number", format="decimal", example=100000.00),
     *             @OA\Property(property="premio", type="number", format="decimal", example=5000.00),
     *             @OA\Property(property="data_inicio", type="string", format="date", example="2025-01-01"),
     *             @OA\Property(property="data_fim", type="string", format="date", example="2025-12-31")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Apólice criada com sucesso")
     * )
     */
    /**
     * @OA\RequestBody(
     *   request="ApoliceRequest",
     *   required=true,
     *   @OA\JsonContent(
     *     required={"cliente_id","tipo_seguro_id","numero_apolice","inicio_vigencia","fim_vigencia","status_apolice_id","premio_mensal"},
     *     @OA\Property(property="cliente_id", type="integer"),
     *     @OA\Property(property="tipo_seguro_id", type="integer"),
     *     @OA\Property(property="numero_apolice", type="string", maxLength=50),
     *     @OA\Property(property="inicio_vigencia", type="string", format="date"),
     *     @OA\Property(property="fim_vigencia", type="string", format="date"),
     *     @OA\Property(property="status_apolice_id", type="integer"),
     *     @OA\Property(property="premio_mensal", type="number", format="float")
     *   )
     * )
     */
    public function storeApolice(ApoliceRequest $request): JsonResponse
    {
        $apolice = Apolice::create($request->validated());
        return response()->json($apolice->load(['cliente', 'tipoSeguro', 'statusApolice']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/seguros/apolices/{id}",
     *     summary="Obter detalhes da apólice",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalhes da apólice")
     * )
     */
    public function showApolice(Apolice $apolice): JsonResponse
    {
        return response()->json($apolice->load(['cliente', 'tipoSeguro', 'statusApolice', 'sinistros']));
    }

    /**
     * @OA\Put(
     *     path="/api/seguros/apolices/{id}",
     *     summary="Atualizar apólice",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(ref="#/components/requestBodies/ApoliceRequest"),
     *     @OA\Response(response=200, description="Apólice atualizada")
     * )
     */
    public function updateApolice(ApoliceRequest $request, Apolice $apolice): JsonResponse
    {
        $apolice->update($request->validated());
        return response()->json($apolice->load(['cliente', 'tipoSeguro', 'statusApolice']));
    }

    /**
     * @OA\Delete(
     *     path="/api/seguros/apolices/{id}",
     *     summary="Excluir apólice",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Apólice excluída")
     * )
     */
    public function destroyApolice(Apolice $apolice): JsonResponse
    {
        if ($apolice->sinistros()->exists()) {
            return response()->json(['message' => 'Não é possível excluir apólice com sinistros associados'], 422);
        }
        $apolice->delete();
        return response()->json(['message' => 'Apólice excluída com sucesso']);
    }

    /**
     * @OA\Get(
     *     path="/api/seguros/sinistros",
     *     summary="Listar sinistros",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="apolice_id", in="query", @OA\Schema(type="integer"), description="Filtrar por apólice"),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string"), description="Filtrar por status"),
     *     @OA\Response(response=200, description="Lista paginada de sinistros")
     * )
     */
    public function indexSinistros(Request $request): JsonResponse
    {
        $query = Sinistro::with(['apolice.cliente', 'statusSinistro']);
        
        if ($request->filled('apolice_id')) {
            $query->where('apolice_id', $request->apolice_id);
        }
        
        if ($request->filled('status')) {
            $query->whereHas('statusSinistro', function($q) use ($request) {
                $q->where('nome', $request->status);
            });
        }
        
        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    /**
     * @OA\Post(
     *     path="/api/seguros/sinistros",
     *     summary="Registrar novo sinistro",
     *     tags={"Seguros"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"apolice_id", "descricao", "valor_sinistro"},
     *             @OA\Property(property="apolice_id", type="integer", example=1),
     *             @OA\Property(property="descricao", type="string", example="Acidente de trânsito"),
     *             @OA\Property(property="valor_sinistro", type="number", format="decimal", example=25000.00),
     *             @OA\Property(property="data_ocorrencia", type="string", format="date", example="2025-01-15")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Sinistro registrado com sucesso")
     * )
     */
    public function storeSinistro(SinistroRequest $request): JsonResponse
    {
        $sinistro = Sinistro::create($request->validated());
        return response()->json($sinistro->load(['apolice.cliente', 'statusSinistro']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/seguros/sinistros/{id}",
     *     summary="Obter detalhes do sinistro",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalhes do sinistro")
     * )
     */
    public function showSinistro(Sinistro $sinistro): JsonResponse
    {
        return response()->json($sinistro->load(['apolice.cliente', 'statusSinistro']));
    }

    /**
     * @OA\Put(
     *     path="/api/seguros/sinistros/{id}",
     *     summary="Atualizar sinistro",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(ref="#/components/requestBodies/SinistroRequest"),
     *     @OA\Response(response=200, description="Sinistro atualizado")
     * )
     */
    /**
     * @OA\RequestBody(
     *   request="SinistroRequest",
     *   required=true,
     *   @OA\JsonContent(
     *     required={"apolice_id","descricao","valor_sinistro"},
     *     @OA\Property(property="apolice_id", type="integer"),
     *     @OA\Property(property="descricao", type="string"),
     *     @OA\Property(property="valor_sinistro", type="number", format="float"),
     *     @OA\Property(property="data_ocorrencia", type="string", format="date"),
     *     @OA\Property(property="status_sinistro_id", type="integer")
     *   )
     * )
     */
    public function updateSinistro(SinistroRequest $request, Sinistro $sinistro): JsonResponse
    {
        $sinistro->update($request->validated());
        return response()->json($sinistro->load(['apolice.cliente', 'statusSinistro']));
    }

    /**
     * @OA\Delete(
     *     path="/api/seguros/sinistros/{id}",
     *     summary="Excluir sinistro",
     *     tags={"Seguros"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sinistro excluído")
     * )
     */
    public function destroySinistro(Sinistro $sinistro): JsonResponse
    {
        $sinistro->delete();
        return response()->json(['message' => 'Sinistro excluído com sucesso']);
    }
}