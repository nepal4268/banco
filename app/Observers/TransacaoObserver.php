<?php

namespace App\Observers;

use App\Models\Transacao;
use App\Models\LogAcao;

class TransacaoObserver
{
    /**
     * Handle the Transacao "created" event.
     */
    public function created(Transacao $transacao): void
    {
        $detalhes = "Transação criada (ID: {$transacao->id}, Valor: {$transacao->valor}). ";
        $detalhes .= "Origem: " . ($transacao->conta_origem_id ? "Conta ID {$transacao->conta_origem_id}" : "Externa") . ", ";
        $detalhes .= "Destino: " . ($transacao->conta_destino_id ? "Conta ID {$transacao->conta_destino_id}" : "Externa") . ".";
        
        LogAcao::create([
            'acao' => 'transacao_criada',
            'detalhes' => $detalhes,
        ]);
    }

    /**
     * Handle the Transacao "updated" event.
     */
    public function updated(Transacao $transacao): void
    {
        $changes = $transacao->getChanges();
        $original = $transacao->getOriginal();
        
        $detalhes = "Transação ID {$transacao->id} foi atualizada. ";
        
        // Log específico para mudanças de status
        if (isset($changes['status_transacao_id'])) {
            $detalhes .= "Status alterado. ";
        }
        
        // Log específico para mudanças de valor
        if (isset($changes['valor'])) {
            $valorAnterior = $original['valor'] ?? 0;
            $novoValor = $changes['valor'];
            $detalhes .= "Valor alterado de {$valorAnterior} para {$novoValor}. ";
        }
        
        $detalhes .= "Campos alterados: " . json_encode($changes);
        
        LogAcao::create([
            'acao' => 'transacao_atualizada',
            'detalhes' => $detalhes,
        ]);
    }

    /**
     * Handle the Transacao "deleted" event.
     */
    public function deleted(Transacao $transacao): void
    {
        LogAcao::create([
            'acao' => 'transacao_excluida',
            'detalhes' => "Transação ID {$transacao->id} (Valor: {$transacao->valor}) foi excluída.",
        ]);
    }

    /**
     * Handle the Transacao "restored" event.
     */
    public function restored(Transacao $transacao): void
    {
        LogAcao::create([
            'acao' => 'transacao_restaurada',
            'detalhes' => "Transação ID {$transacao->id} (Valor: {$transacao->valor}) foi restaurada.",
        ]);
    }
}