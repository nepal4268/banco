<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Sistema Bancário API",
 *     version="1.0.0",
 *     description="API completa para sistema bancário com operações de contas, transações, cartões, seguros e relatórios",
 *     @OA\Contact(
 *         email="admin@banco.ao",
 *         name="Suporte Técnico"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor de Desenvolvimento"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token de autenticação Bearer obtido via endpoint /api/login"
 * )
 * 
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints para login, logout e gestão de tokens"
 * )
 * 
 * @OA\Tag(
 *     name="Clientes",
 *     description="Gestão de clientes do banco"
 * )
 * 
 * @OA\Tag(
 *     name="Contas",
 *     description="Gestão de contas bancárias, depósitos e levantamentos"
 * )
 * 
 * @OA\Tag(
 *     name="Transações",
 *     description="Transferências, histórico e operações financeiras"
 * )
 * 
 * @OA\Tag(
 *     name="Cartões",
 *     description="Gestão de cartões de débito e crédito"
 * )
 * 
 * @OA\Tag(
 *     name="Seguros",
 *     description="Apólices de seguro e gestão de sinistros"
 * )
 * 
 * @OA\Tag(
 *     name="Câmbio",
 *     description="Taxas de câmbio e operações de conversão"
 * )
 * 
 * @OA\Tag(
 *     name="Relatórios",
 *     description="Relatórios, extratos e dashboards"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}