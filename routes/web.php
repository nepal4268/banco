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
    Route::resource('clientes', ClienteWebController::class);

    // Contas
    Route::resource('contas', ContaWebController::class);

    // Cartões
    Route::resource('cartoes', CartaoWebController::class);

    // Transações
    Route::resource('transacoes', TransacaoWebController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);

    // Seguros
    Route::prefix('seguros')->name('seguros.')->group(function () {
        Route::resource('apolices', SeguroWebController::class);
        Route::resource('sinistros', SeguroWebController::class);
    });

    // Relatórios
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/clientes', [RelatorioWebController::class, 'clientes'])->name('clientes');
        Route::get('/transacoes', [RelatorioWebController::class, 'transacoes'])->name('transacoes');
        Route::get('/contas', [RelatorioWebController::class, 'contas'])->name('contas');
    });

    // Administração
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('usuarios', AdminWebController::class);
        Route::resource('agencias', AdminWebController::class);
        Route::resource('perfis', AdminWebController::class);
    });
});
