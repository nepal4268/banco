<?php

namespace App\Observers;

use App\Models\Conta;
use App\Models\LogAcao;

class ContaObserver
{
    /**
     * Handle the Conta "created" event.
     */
    public function created(Conta $conta): void
    {
        LogAcao::create([
            'acao' => 'conta_criada',
            'detalhes' => "Conta {$conta->numero_conta} (ID: {$conta->id}, IBAN: {$conta->iban}) foi criada para o cliente ID {$conta->cliente_id}.",
        ]);
    }

    /**
     * Handle the Conta "updated" event.
     */
    public function updated(Conta $conta): void
    {
        $changes = $conta->getChanges();
        $original = $conta->getOriginal();
        
        $detalhes = "Conta {$conta->numero_conta} (ID: {$conta->id}) foi atualizada. ";
        
        // Log específico para mudanças de saldo
        if (isset($changes['saldo'])) {
            $saldoAnterior = $original['saldo'] ?? 0;
            $novoSaldo = $changes['saldo'];
            $diferenca = $novoSaldo - $saldoAnterior;
            $detalhes .= "Saldo alterado de {$saldoAnterior} para {$novoSaldo} (diferença: {$diferenca}). ";
        }
        
        $detalhes .= "Campos alterados: " . json_encode($changes);
        
        LogAcao::create([
            'acao' => 'conta_atualizada',
            'detalhes' => $detalhes,
        ]);
    }

    /**
     * Handle the Conta "deleted" event.
     */
    public function deleted(Conta $conta): void
    {
        LogAcao::create([
            'acao' => 'conta_excluida',
            'detalhes' => "Conta {$conta->numero_conta} (ID: {$conta->id}, IBAN: {$conta->iban}) foi excluída.",
        ]);
    }

    /**
     * Handle the Conta "restored" event.
     */
    public function restored(Conta $conta): void
    {
        LogAcao::create([
            'acao' => 'conta_restaurada',
            'detalhes' => "Conta {$conta->numero_conta} (ID: {$conta->id}, IBAN: {$conta->iban}) foi restaurada.",
        ]);
    }
}