<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Conta;
use App\Services\TransactionService;

$numero = $argv[1] ?? '000100000001';
$valor = isset($argv[2]) ? (float)$argv[2] : 100.00;
$ref = $argv[3] ?? ('WEB-DEPOSIT-' . time());

$conta = Conta::where('numero_conta', $numero)->first();
if(!$conta){ echo "Conta {$numero} not found\n"; exit(1); }

echo "Conta ID: {$conta->id} Numero: {$conta->numero_conta}\n";
echo "Saldo antes: " . number_format($conta->saldo,2,'.',',') . " ({$conta->moeda_id})\n";

$service = app()->make(TransactionService::class);
try{
    $trans = $service->deposit($conta, $valor, $conta->moeda_id, 'Depósito via script', $ref);
    echo "Transacao criada ID: " . ($trans->id ?? 'none') . " Valor: " . number_format($trans->valor,2,'.',',') . " Ref: " . ($trans->referencia_externa ?? '') . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    exit(1);
}

$contaAfter = Conta::find($conta->id);
echo "Saldo depois: " . number_format($contaAfter->saldo,2,'.',',') . "\n";
