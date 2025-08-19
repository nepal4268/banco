<?php

namespace App\Observers;

use App\Models\Cliente;
use App\Models\LogAcao;
use Illuminate\Support\Facades\Log;

class ClienteObserver
{
    /**
     * Handle the Cliente "created" event.
     */
    public function created(Cliente $cliente): void
    {
        try {
            LogAcao::create([
                'acao' => 'cliente_criado',
                'detalhes' => "Cliente {$cliente->nome} (ID: {$cliente->id}, BI: {$cliente->bi}) foi criado.",
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar log de cliente criado: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Cliente "updated" event.
     */
    public function updated(Cliente $cliente): void
    {
        try {
            $changes = $cliente->getChanges();
            $original = $cliente->getOriginal();
            
            $detalhes = "Cliente {$cliente->nome} (ID: {$cliente->id}) foi atualizado. ";
            $detalhes .= "Campos alterados: " . json_encode($changes);
            
            LogAcao::create([
                'acao' => 'cliente_atualizado',
                'detalhes' => $detalhes,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar log de cliente atualizado: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Cliente "deleted" event.
     */
    public function deleted(Cliente $cliente): void
    {
        try {
            LogAcao::create([
                'acao' => 'cliente_excluido',
                'detalhes' => "Cliente {$cliente->nome} (ID: {$cliente->id}, BI: {$cliente->bi}) foi excluído.",
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar log de cliente excluído: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Cliente "restored" event.
     */
    public function restored(Cliente $cliente): void
    {
        try {
            LogAcao::create([
                'acao' => 'cliente_restaurado',
                'detalhes' => "Cliente {$cliente->nome} (ID: {$cliente->id}, BI: {$cliente->bi}) foi restaurado.",
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar log de cliente restaurado: ' . $e->getMessage());
        }
    }
}