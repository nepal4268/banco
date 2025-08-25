<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Conta;
use App\Models\Transacao;
use App\Models\User;
use App\Services\TransactionService;

$conta = Conta::first();
if(!$conta){ echo "No conta found\n"; exit(1); }
echo "Conta ID: {$conta->id} Numero: {$conta->numero_conta}\n";
echo "Saldo antes: " . number_format($conta->saldo,2,'.',',') . "\n";

$service = new TransactionService();
try{
    $transacao = $service->deposit($conta, 0.01, $conta->moeda_id, 'Test deposit script', 'TEST-SCRIPT-'.time());
    echo "Deposit created transacao id: " . ($transacao->id ?? 'none') . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

$contaAfter = Conta::find($conta->id);
echo "Saldo depois: " . number_format($contaAfter->saldo,2,'.',',') . "\n";
$last = Transacao::where('conta_destino_id', $conta->id)->orderByDesc('id')->first();
if($last){ echo "Ultima transacao (destino): {$last->id} valor: {$last->valor} ref: {$last->referencia_externa}\n"; }
else{ echo "No transacao found\n"; }
