<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteWebController;
use App\Http\Controllers\ContaWebController;
use App\Http\Controllers\CartaoWebController;
use App\Http\Controllers\TransacaoWebController;
use App\Http\Controllers\SeguroWebController;
use App\Http\Controllers\RelatorioWebController;
use App\Http\Controllers\AdminWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rotas de autenticação
Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Clientes
    Route::middleware(['permission:clientes.view'])->group(function () {
        Route::resource('clientes', ClienteWebController::class);
    });

    // Contas
    Route::middleware(['permission:contas.view'])->group(function () {
        Route::resource('contas', ContaWebController::class);
    });

    // Cartões
    Route::middleware(['permission:cartoes.view'])->group(function () {
        Route::resource('cartoes', CartaoWebController::class);
    });

    // Transações
    Route::middleware(['permission:transacoes.view'])->group(function () {
        Route::resource('transacoes', TransacaoWebController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    });

    // Seguros
    Route::middleware(['permission:seguros.view'])->group(function () {
        Route::prefix('seguros')->name('seguros.')->group(function () {
            Route::resource('apolices', SeguroWebController::class);
            Route::resource('sinistros', SeguroWebController::class);
        });
    });

    // Relatórios
    Route::middleware(['permission:relatorios.view'])->group(function () {
        Route::prefix('relatorios')->name('relatorios.')->group(function () {
            Route::get('/clientes', [RelatorioWebController::class, 'clientes'])->name('clientes');
            Route::get('/transacoes', [RelatorioWebController::class, 'transacoes'])->name('transacoes');
            Route::get('/contas', [RelatorioWebController::class, 'contas'])->name('contas');
            Route::get('/cartoes', [RelatorioWebController::class, 'cartoes'])->name('cartoes');
            Route::get('/seguros', [RelatorioWebController::class, 'seguros'])->name('seguros');
            Route::get('/auditoria', [RelatorioWebController::class, 'auditoria'])->name('auditoria');
        });
    });

    // Administração
    Route::middleware(['permission:admin.view'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            // Usuários
            Route::middleware(['permission:admin.usuarios'])->group(function () {
                Route::resource('usuarios', AdminWebController::class);
                Route::get('usuarios/{usuario}/permissoes', [AdminWebController::class, 'gerenciarPermissoes'])->name('usuarios.permissoes');
                Route::post('usuarios/{usuario}/permissoes', [AdminWebController::class, 'salvarPermissoes'])->name('usuarios.permissoes.save');
            });

            // Agências
            Route::middleware(['permission:admin.agencias'])->group(function () {
                Route::resource('agencias', AdminWebController::class);
            });

            // Perfis
            Route::middleware(['permission:admin.perfis'])->group(function () {
                Route::resource('perfis', AdminWebController::class);
                Route::get('perfis/{perfil}/permissoes', [AdminWebController::class, 'gerenciarPermissoesPerfil'])->name('perfis.permissoes');
                Route::post('perfis/{perfil}/permissoes', [AdminWebController::class, 'salvarPermissoesPerfil'])->name('perfis.permissoes.save');
            });

            // Permissões
            Route::middleware(['permission:admin.permissoes'])->group(function () {
                Route::resource('permissoes', AdminWebController::class);
            });

            // Configurações do Sistema
            Route::middleware(['permission:admin.config'])->group(function () {
                Route::get('configuracoes', [AdminWebController::class, 'configuracoes'])->name('configuracoes');
                Route::post('configuracoes', [AdminWebController::class, 'salvarConfiguracoes'])->name('configuracoes.save');
            });

            // Logs de Auditoria
            Route::middleware(['permission:admin.auditoria'])->group(function () {
                Route::get('logs', [AdminWebController::class, 'logs'])->name('logs');
                Route::get('logs/export', [AdminWebController::class, 'exportarLogs'])->name('logs.export');
            });
        });
    });

    // Gestão de Tabelas Administrativas
    Route::middleware(['permission:admin.tabelas'])->group(function () {
        Route::prefix('admin/tabelas')->name('admin.tabelas.')->group(function () {
            // Tipos de Cliente
            Route::resource('tipos-cliente', AdminWebController::class);
            
            // Status de Cliente
            Route::resource('status-cliente', AdminWebController::class);
            
            // Tipos de Conta
            Route::resource('tipos-conta', AdminWebController::class);
            
            // Status de Conta
            Route::resource('status-conta', AdminWebController::class);
            
            // Tipos de Cartão
            Route::resource('tipos-cartao', AdminWebController::class);
            
            // Status de Cartão
            Route::resource('status-cartao', AdminWebController::class);
            
            // Tipos de Transação
            Route::resource('tipos-transacao', AdminWebController::class);
            
            // Status de Transação
            Route::resource('status-transacao', AdminWebController::class);
            
            // Status de Pagamento
            Route::resource('status-pagamento', AdminWebController::class);
            
            // Tipos de Seguro
            Route::resource('tipos-seguro', AdminWebController::class);
            
            // Status de Apólice
            Route::resource('status-apolice', AdminWebController::class);
            
            // Status de Sinistro
            Route::resource('status-sinistro', AdminWebController::class);
            
            // Moedas
            Route::resource('moedas', AdminWebController::class);
            
            // Taxas de Câmbio
            Route::resource('taxas-cambio', AdminWebController::class);
        });
    });
});
