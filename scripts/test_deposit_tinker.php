<?php
// Quick script to run a deposit via TransactionService for testing.
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Conta;

$conta = Conta::first();
if(!$conta){ echo "NO_ACCOUNT\n"; exit(1); }
$service = app(\App\Services\TransactionService::class);
$trans = $service->deposit($conta, 1.23, $conta->moeda_id, 'Teste depósito via script', null, 'Fulano T.');
echo "TRANS_ID:" . $trans->id . "\n";
echo json_encode($trans->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
