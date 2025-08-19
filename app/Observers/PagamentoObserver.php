<?php

namespace App\Observers;

use App\Models\Pagamento;
use App\Models\LogAcao;

class PagamentoObserver
{
    /**
     * Handle the Pagamento "created" event.
     */
    public function created(Pagamento $pagamento): void
    {
        LogAcao::create([
            'acao' => 'pagamento_criado',
            'detalhes' => "Pagamento criado (ID: {$pagamento->id}, Valor: {$pagamento->valor}, Parceiro: {$pagamento->parceiro}, Referência: {$pagamento->referencia}).",
        ]);
    }

    /**
     * Handle the Pagamento "updated" event.
     */
    public function updated(Pagamento $pagamento): void
    {
        $changes = $pagamento->getChanges();
        $original = $pagamento->getOriginal();
        
        $detalhes = "Pagamento ID {$pagamento->id} foi atualizado. ";
        
        // Log específico para mudanças de status
        if (isset($changes['status_pagamento_id'])) {
            $detalhes .= "Status do pagamento alterado. ";
        }
        
        $detalhes .= "Campos alterados: " . json_encode($changes);
        
        LogAcao::create([
            'acao' => 'pagamento_atualizado',
            'detalhes' => $detalhes,
        ]);
    }

    /**
     * Handle the Pagamento "deleted" event.
     */
    public function deleted(Pagamento $pagamento): void
    {
        LogAcao::create([
            'acao' => 'pagamento_excluido',
            'detalhes' => "Pagamento ID {$pagamento->id} (Valor: {$pagamento->valor}, Parceiro: {$pagamento->parceiro}) foi excluído.",
        ]);
    }

    /**
     * Handle the Pagamento "restored" event.
     */
    public function restored(Pagamento $pagamento): void
    {
        LogAcao::create([
            'acao' => 'pagamento_restaurado',
            'detalhes' => "Pagamento ID {$pagamento->id} (Valor: {$pagamento->valor}, Parceiro: {$pagamento->parceiro}) foi restaurado.",
        ]);
    }
}