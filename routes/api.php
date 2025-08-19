<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ContaController;
use App\Http\Controllers\Api\TransacaoController;

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

	Route::get('/user', function (Request $request) {
		return $request->user();
	});

	Route::apiResource('clientes', ClienteController::class);
	Route::apiResource('contas', ContaController::class);

	Route::get('transacoes', [TransacaoController::class, 'index']);
	Route::get('transacoes/{transacao}', [TransacaoController::class, 'show']);
	Route::post('transacoes/transferir', [TransacaoController::class, 'transferirInterno']);
	Route::post('transacoes/transferir-externo', [TransacaoController::class, 'transferirExterno']);
	Route::post('transacoes/cambio', [TransacaoController::class, 'cambio']);

	Route::post('contas/{conta}/depositar', [ContaController::class, 'depositar']);
	Route::post('contas/{conta}/levantar', [ContaController::class, 'levantar']);
});