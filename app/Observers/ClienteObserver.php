<?php

namespace App\Observers;

use App\Models\Cliente;
use App\Models\LogAcao;

class ClienteObserver
{
    /**
     * Handle the Cliente "created" event.
     */
    public function created(Cliente $cliente): void
    {
        LogAcao::create([
            'acao' => 'cliente_criado',
            'detalhes' => "Cliente {$cliente->nome} (ID: {$cliente->id}, BI: {$cliente->bi}) foi criado.",
        ]);
    }

    /**
     * Handle the Cliente "updated" event.
     */
    public function updated(Cliente $cliente): void
    {
        $changes = $cliente->getChanges();
        $original = $cliente->getOriginal();
        
        $detalhes = "Cliente {$cliente->nome} (ID: {$cliente->id}) foi atualizado. ";
        $detalhes .= "Campos alterados: " . json_encode($changes);
        
        LogAcao::create([
            'acao' => 'cliente_atualizado',
            'detalhes' => $detalhes,
        ]);
    }

    /**
     * Handle the Cliente "deleted" event.
     */
    public function deleted(Cliente $cliente): void
    {
        LogAcao::create([
            'acao' => 'cliente_excluido',
            'detalhes' => "Cliente {$cliente->nome} (ID: {$cliente->id}, BI: {$cliente->bi}) foi excluído.",
        ]);
    }

    /**
     * Handle the Cliente "restored" event.
     */
    public function restored(Cliente $cliente): void
    {
        LogAcao::create([
            'acao' => 'cliente_restaurado',
            'detalhes' => "Cliente {$cliente->nome} (ID: {$cliente->id}, BI: {$cliente->bi}) foi restaurado.",
        ]);
    }
}