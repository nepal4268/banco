<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ContaController;
use App\Http\Controllers\Api\TransacaoController;
use App\Http\Controllers\Api\CartaoController;
use App\Http\Controllers\Api\SeguroController;
use App\Http\Controllers\Api\TaxaCambioController;
use App\Http\Controllers\Api\RelatorioController;
use App\Http\Controllers\Api\MoedaController;
use App\Http\Controllers\Api\AgenciaController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\PermissaoController;
use App\Http\Controllers\Api\ConfiguracaoController;
use App\Http\Controllers\Api\PagamentoController;
use App\Http\Controllers\Api\LogAcaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/me', [AuthController::class, 'me']);
	// Alteração de senha do usuário autenticado
	Route::post('/change-password', [AuthController::class, 'changePassword']);

	Route::get('/user', function (Request $request) {
		return $request->user();
	});

	// Lookups de clientes (deve vir antes do apiResource)
	Route::get('clientes/lookups', [ClienteController::class, 'lookups']);
	Route::apiResource('clientes', ClienteController::class);
	Route::apiResource('contas', ContaController::class);

	Route::get('transacoes', [TransacaoController::class, 'index']);
	Route::get('transacoes/{transacao}', [TransacaoController::class, 'show']);
	Route::post('transacoes/transferir', [TransacaoController::class, 'transferirInterno']);
	Route::post('transacoes/transferir-externo', [TransacaoController::class, 'transferirExterno']);
	Route::post('transacoes/cambio', [TransacaoController::class, 'cambio']);

	Route::post('contas/{conta}/depositar', [ContaController::class, 'depositar']);
	Route::post('contas/{conta}/levantar', [ContaController::class, 'levantar']);
	Route::post('contas/{conta}/pagar', [ContaController::class, 'pagar']);

	// Cartões
	Route::apiResource('cartoes', CartaoController::class);
	Route::post('cartoes/{cartao}/bloquear', [CartaoController::class, 'bloquear']);

	// Seguros
	Route::get('seguros/apolices', [SeguroController::class, 'indexApolices']);
	Route::post('seguros/apolices', [SeguroController::class, 'storeApolice']);
	Route::get('seguros/apolices/{apolice}', [SeguroController::class, 'showApolice']);
	Route::get('seguros/sinistros', [SeguroController::class, 'indexSinistros']);
	Route::post('seguros/sinistros', [SeguroController::class, 'storeSinistro']);
	Route::get('seguros/sinistros/{sinistro}', [SeguroController::class, 'showSinistro']);

	// Taxas de Câmbio
	Route::get('taxas-cambio', [TaxaCambioController::class, 'index']);
	Route::get('taxas-cambio/cotacao', [TaxaCambioController::class, 'cotacao']);
	Route::post('taxas-cambio', [TaxaCambioController::class, 'store']);
	Route::get('operacoes-cambio', [TaxaCambioController::class, 'historico']);

	// Relatórios
	Route::get('relatorios/dashboard', [RelatorioController::class, 'dashboard']);
	Route::get('relatorios/transacoes', [RelatorioController::class, 'transacoes']);
	Route::get('relatorios/contas/{conta}/extrato', [RelatorioController::class, 'extrato']);
	Route::get('relatorios/clientes/{cliente}/extrato', [RelatorioController::class, 'extratoCliente']);
	Route::get('relatorios/auditoria', [RelatorioController::class, 'auditoria']);

	// ========== NOVAS ROTAS ==========

	// Configuração - Moedas
	Route::apiResource('moedas', MoedaController::class);

	// Configuração - Agências
	Route::apiResource('agencias', AgenciaController::class);

	// Gestão de Usuários - Perfis
	Route::apiResource('perfis', PerfilController::class);
	Route::post('perfis/{perfil}/permissoes', [PerfilController::class, 'adicionarPermissoes']);
	Route::delete('perfis/{perfil}/permissoes/{permissao}', [PerfilController::class, 'removerPermissao']);

	// Gestão de Usuários - Permissões
	Route::apiResource('permissoes', PermissaoController::class);
	Route::get('permissoes/grupos', [PermissaoController::class, 'grupos']);

	// Configuração - Tipos e Status (Lookups)
	Route::get('configuracoes/tipos', [ConfiguracaoController::class, 'tipos']);
	Route::get('configuracoes/status', [ConfiguracaoController::class, 'status']);
	Route::get('configuracoes/lookups', [ConfiguracaoController::class, 'lookups']);

	// Endpoints específicos para tipos
	Route::get('tipos-cliente', [ConfiguracaoController::class, 'tiposCliente']);
	Route::post('tipos-cliente', [ConfiguracaoController::class, 'storeTipoCliente']);
	Route::get('tipos-conta', [ConfiguracaoController::class, 'tiposConta']);
	Route::get('tipos-cartao', [ConfiguracaoController::class, 'tiposCartao']);
	Route::get('tipos-seguro', [ConfiguracaoController::class, 'tiposSeguro']);
	Route::get('tipos-transacao', [ConfiguracaoController::class, 'tiposTransacao']);

	// Endpoints específicos para status
	Route::get('status-cliente', [ConfiguracaoController::class, 'statusCliente']);
	Route::post('status-cliente', [ConfiguracaoController::class, 'storeStatusCliente']);
	Route::get('status-conta', [ConfiguracaoController::class, 'statusConta']);
	Route::get('status-cartao', [ConfiguracaoController::class, 'statusCartao']);
	Route::get('status-pagamento', [ConfiguracaoController::class, 'statusPagamento']);
	Route::get('status-sinistro', [ConfiguracaoController::class, 'statusSinistro']);
	Route::get('status-transacao', [ConfiguracaoController::class, 'statusTransacao']);
	Route::get('status-apolice', [ConfiguracaoController::class, 'statusApolice']);

	// Pagamentos
	Route::apiResource('pagamentos', PagamentoController::class);
	Route::post('pagamentos/{pagamento}/processar', [PagamentoController::class, 'processar']);
	Route::post('pagamentos/{pagamento}/cancelar', [PagamentoController::class, 'cancelar']);

	// Auditoria - Logs de Ação
	Route::get('logs', [LogAcaoController::class, 'index']);
	Route::get('logs/estatisticas', [LogAcaoController::class, 'estatisticas']);
	Route::get('logs/acoes', [LogAcaoController::class, 'acoes']);
	Route::get('logs/tabelas', [LogAcaoController::class, 'tabelas']);
	Route::get('logs/usuario/{usuarioId}', [LogAcaoController::class, 'logsPorUsuario']);
	Route::get('logs/{log}', [LogAcaoController::class, 'show']);
	Route::delete('logs/limpar', [LogAcaoController::class, 'limpar']);
});