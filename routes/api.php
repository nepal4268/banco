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
	Route::get('relatorios/clientes/{cliente}/contas', [RelatorioController::class, 'contasDoCliente']);
	Route::get('relatorios/movimentos', [RelatorioController::class, 'movimentos']);

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
	Route::get('tipos-cliente/{tipoCliente}', [ConfiguracaoController::class, 'showTipoCliente']);
	Route::put('tipos-cliente/{tipoCliente}', [ConfiguracaoController::class, 'updateTipoCliente']);
	Route::delete('tipos-cliente/{tipoCliente}', [ConfiguracaoController::class, 'destroyTipoCliente']);
	Route::get('tipos-conta', [ConfiguracaoController::class, 'tiposConta']);
	Route::post('tipos-conta', [ConfiguracaoController::class, 'storeTipoConta']);
	Route::get('tipos-conta/{tipoConta}', [ConfiguracaoController::class, 'showTipoConta']);
	Route::put('tipos-conta/{tipoConta}', [ConfiguracaoController::class, 'updateTipoConta']);
	Route::delete('tipos-conta/{tipoConta}', [ConfiguracaoController::class, 'destroyTipoConta']);
	Route::get('tipos-cartao', [ConfiguracaoController::class, 'tiposCartao']);
	Route::post('tipos-cartao', [ConfiguracaoController::class, 'storeTipoCartao']);
	Route::get('tipos-cartao/{tipoCartao}', [ConfiguracaoController::class, 'showTipoCartao']);
	Route::put('tipos-cartao/{tipoCartao}', [ConfiguracaoController::class, 'updateTipoCartao']);
	Route::delete('tipos-cartao/{tipoCartao}', [ConfiguracaoController::class, 'destroyTipoCartao']);
	Route::get('tipos-seguro', [ConfiguracaoController::class, 'tiposSeguro']);
	Route::post('tipos-seguro', [ConfiguracaoController::class, 'storeTipoSeguro']);
	Route::get('tipos-seguro/{tipoSeguro}', [ConfiguracaoController::class, 'showTipoSeguro']);
	Route::put('tipos-seguro/{tipoSeguro}', [ConfiguracaoController::class, 'updateTipoSeguro']);
	Route::delete('tipos-seguro/{tipoSeguro}', [ConfiguracaoController::class, 'destroyTipoSeguro']);
	Route::get('tipos-transacao', [ConfiguracaoController::class, 'tiposTransacao']);
	Route::post('tipos-transacao', [ConfiguracaoController::class, 'storeTipoTransacao']);
	Route::get('tipos-transacao/{tipoTransacao}', [ConfiguracaoController::class, 'showTipoTransacao']);
	Route::put('tipos-transacao/{tipoTransacao}', [ConfiguracaoController::class, 'updateTipoTransacao']);
	Route::delete('tipos-transacao/{tipoTransacao}', [ConfiguracaoController::class, 'destroyTipoTransacao']);

	// Endpoints específicos para status
	Route::get('status-cliente', [ConfiguracaoController::class, 'statusCliente']);
	Route::post('status-cliente', [ConfiguracaoController::class, 'storeStatusCliente']);
	Route::get('status-cliente/{statusCliente}', [ConfiguracaoController::class, 'showStatusCliente']);
	Route::put('status-cliente/{statusCliente}', [ConfiguracaoController::class, 'updateStatusCliente']);
	Route::delete('status-cliente/{statusCliente}', [ConfiguracaoController::class, 'destroyStatusCliente']);
	Route::get('status-conta', [ConfiguracaoController::class, 'statusConta']);
	Route::post('status-conta', [ConfiguracaoController::class, 'storeStatusConta']);
	Route::get('status-conta/{statusConta}', [ConfiguracaoController::class, 'showStatusConta']);
	Route::put('status-conta/{statusConta}', [ConfiguracaoController::class, 'updateStatusConta']);
	Route::delete('status-conta/{statusConta}', [ConfiguracaoController::class, 'destroyStatusConta']);
	Route::get('status-cartao', [ConfiguracaoController::class, 'statusCartao']);
	Route::post('status-cartao', [ConfiguracaoController::class, 'storeStatusCartao']);
	Route::get('status-cartao/{statusCartao}', [ConfiguracaoController::class, 'showStatusCartao']);
	Route::put('status-cartao/{statusCartao}', [ConfiguracaoController::class, 'updateStatusCartao']);
	Route::delete('status-cartao/{statusCartao}', [ConfiguracaoController::class, 'destroyStatusCartao']);
	Route::get('status-pagamento', [ConfiguracaoController::class, 'statusPagamento']);
	Route::post('status-pagamento', [ConfiguracaoController::class, 'storeStatusPagamento']);
	Route::get('status-pagamento/{statusPagamento}', [ConfiguracaoController::class, 'showStatusPagamento']);
	Route::put('status-pagamento/{statusPagamento}', [ConfiguracaoController::class, 'updateStatusPagamento']);
	Route::delete('status-pagamento/{statusPagamento}', [ConfiguracaoController::class, 'destroyStatusPagamento']);
	Route::get('status-sinistro', [ConfiguracaoController::class, 'statusSinistro']);
	Route::post('status-sinistro', [ConfiguracaoController::class, 'storeStatusSinistro']);
	Route::get('status-sinistro/{statusSinistro}', [ConfiguracaoController::class, 'showStatusSinistro']);
	Route::put('status-sinistro/{statusSinistro}', [ConfiguracaoController::class, 'updateStatusSinistro']);
	Route::delete('status-sinistro/{statusSinistro}', [ConfiguracaoController::class, 'destroyStatusSinistro']);
	Route::get('status-transacao', [ConfiguracaoController::class, 'statusTransacao']);
	Route::post('status-transacao', [ConfiguracaoController::class, 'storeStatusTransacao']);
	Route::get('status-transacao/{statusTransacao}', [ConfiguracaoController::class, 'showStatusTransacao']);
	Route::put('status-transacao/{statusTransacao}', [ConfiguracaoController::class, 'updateStatusTransacao']);
	Route::delete('status-transacao/{statusTransacao}', [ConfiguracaoController::class, 'destroyStatusTransacao']);
	Route::get('status-apolice', [ConfiguracaoController::class, 'statusApolice']);
	Route::post('status-apolice', [ConfiguracaoController::class, 'storeStatusApolice']);
	Route::get('status-apolice/{statusApolice}', [ConfiguracaoController::class, 'showStatusApolice']);
	Route::put('status-apolice/{statusApolice}', [ConfiguracaoController::class, 'updateStatusApolice']);
	Route::delete('status-apolice/{statusApolice}', [ConfiguracaoController::class, 'destroyStatusApolice']);

	// Pagamentos
	Route::apiResource('pagamentos', PagamentoController::class);
	Route::post('pagamentos/{pagamento}/processar', [PagamentoController::class, 'processar']);
	Route::post('pagamentos/{pagamento}/cancelar', [PagamentoController::class, 'cancelar']);

	// Seguros - atualizar/excluir
	Route::put('seguros/apolices/{apolice}', [SeguroController::class, 'updateApolice']);
	Route::delete('seguros/apolices/{apolice}', [SeguroController::class, 'destroyApolice']);
	Route::put('seguros/sinistros/{sinistro}', [SeguroController::class, 'updateSinistro']);
	Route::delete('seguros/sinistros/{sinistro}', [SeguroController::class, 'destroySinistro']);

	// Auditoria - Logs de Ação
	Route::get('logs', [LogAcaoController::class, 'index']);
	Route::get('logs/estatisticas', [LogAcaoController::class, 'estatisticas']);
	Route::get('logs/acoes', [LogAcaoController::class, 'acoes']);
	Route::get('logs/tabelas', [LogAcaoController::class, 'tabelas']);
	Route::get('logs/usuario/{usuarioId}', [LogAcaoController::class, 'logsPorUsuario']);
	Route::get('logs/{log}', [LogAcaoController::class, 'show']);
	Route::delete('logs/limpar', [LogAcaoController::class, 'limpar']);
});