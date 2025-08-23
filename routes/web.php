<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteWebController;
use App\Http\Controllers\ContaWebController;
use App\Http\Controllers\CartaoWebController;
use App\Http\Controllers\TransacaoWebController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeguroWebController;
use App\Http\Controllers\RelatorioWebController;
use App\Http\Controllers\AdminWebController;

// Rotas de autenticação
Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Rotas de reset de senha
Route::get('/password/reset', [AuthWebController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [AuthWebController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthWebController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthWebController::class, 'reset'])->name('password.update');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
	// Perfil do usuário
	Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
	Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
	// Relatórios
	Route::prefix('relatorios')->name('relatorios.')->group(function () {
		Route::get('/clientes', [RelatorioWebController::class, 'clientes'])->name('clientes');
		Route::get('/transacoes', [RelatorioWebController::class, 'transacoes'])->name('transacoes');
		Route::get('/contas', [RelatorioWebController::class, 'contas'])->name('contas');
		Route::get('/cartoes', [RelatorioWebController::class, 'cartoes'])->name('cartoes');
		Route::get('/seguros', [RelatorioWebController::class, 'seguros'])->name('seguros');
		Route::get('/auditoria', [RelatorioWebController::class, 'auditoria'])->name('auditoria');
	});

	// Dashboard
	Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
	Route::get('/dashboard', [DashboardController::class, 'index']);

	// Recursos básicos referenciados pelo dashboard
	// Cartões - web actions: bloquear / ativar / substituir (POST)
	Route::post('cartoes/{carto}/bloquear', [CartaoWebController::class, 'bloquear'])->name('cartoes.web.bloquear');
	Route::post('cartoes/{carto}/ativar', [CartaoWebController::class, 'ativar'])->name('cartoes.web.ativar');
	Route::post('cartoes/{carto}/substituir', [CartaoWebController::class, 'substituir'])->name('cartoes.web.substituir');
	Route::resource('cartoes', CartaoWebController::class);
	Route::resource('transacoes', TransacaoWebController::class);

	// AJAX search transactions by account number (used by Listar Transações UI)
	Route::post('transacoes/search-by-conta', [TransacaoWebController::class, 'searchByConta'])->name('transacoes.searchByConta');

	// Seguros (prefix)
	Route::prefix('seguros')->name('seguros.')->group(function () {
		Route::resource('apolices', SeguroWebController::class);
		Route::resource('sinistros', SeguroWebController::class);
	});

	// Administração (admin)
	Route::prefix('admin')->name('admin.')->group(function () {
		// Usuarios tem controller dedicado para permitir injeção de FormRequests e respostas JSON
		Route::resource('usuarios', \App\Http\Controllers\AdminUsuarioController::class);
		// Agencias e perfis continuam usando AdminWebController multiplexado
	Route::resource('agencias', AdminWebController::class);
	Route::resource('perfis', \App\Http\Controllers\AdminPerfilController::class);
	Route::resource('permissoes', \App\Http\Controllers\AdminPermissaoController::class);
		Route::resource('clientes', ClienteWebController::class);

		// BI flow and auxiliary endpoints (placed before resource 'contas' to avoid route parameter conflicts)
		Route::get('contas/find-by-bi', [\App\Http\Controllers\ContaWebController::class, 'findByBiForm'])->name('contas.findByBi.form');
		Route::post('contas/find-by-bi', [\App\Http\Controllers\ContaWebController::class, 'findByBi'])->name('contas.findByBi');
		Route::get('contas/create-for-client/{cliente}', [\App\Http\Controllers\ContaWebController::class, 'createForClient'])->name('contas.createForClient');
		Route::get('contas/generate-account', [\App\Http\Controllers\ContaWebController::class, 'generateAccount'])->name('contas.generateAccount');

		Route::resource('contas', ContaWebController::class);

	// Generic lookups manager: admin/lookups/{key}
	Route::prefix('lookups')->name('lookups.')->group(function () {
		Route::get('{key}', [\App\Http\Controllers\AdminLookupController::class, 'index'])->name('index');
		Route::get('{key}/create', [\App\Http\Controllers\AdminLookupController::class, 'create'])->name('create');
		Route::post('{key}', [\App\Http\Controllers\AdminLookupController::class, 'store'])->name('store');
		Route::get('{key}/{id}/edit', [\App\Http\Controllers\AdminLookupController::class, 'edit'])->name('edit');
		Route::put('{key}/{id}', [\App\Http\Controllers\AdminLookupController::class, 'update'])->name('update');
		Route::delete('{key}/{id}', [\App\Http\Controllers\AdminLookupController::class, 'destroy'])->name('destroy');
	});

		// Dashboard por agência
		Route::get('agencias/{agencia}/dashboard', [\App\Http\Controllers\AdminWebController::class, 'agenciasDashboard'])->name('agencias.dashboard');
		// API: dados do dashboard (JSON) - consultas otimizadas
		Route::get('agencias/{agencia}/dashboard/data', [\App\Http\Controllers\AdminWebController::class, 'agenciasDashboardData'])->name('agencias.dashboard.data');
		// Outros recursos administrativos podem ser adicionados aqui
	});
});

// (Removed public fallback route for generate-account) - use authenticated route in admin group

