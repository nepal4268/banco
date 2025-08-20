<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OperacaoCambio;
use App\Models\Conta;
use App\Models\Moeda;
use App\Models\TaxaCambio;
use Illuminate\Support\Carbon;

class OperacaoCambioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contas = Conta::with('moeda')->get();
        $moedas = Moeda::pluck('id', 'codigo');

        foreach ($contas as $contaOrigem) {
            // Selecionar outra conta do mesmo cliente para destino (quando possível)
            $contasCliente = Conta::where('cliente_id', $contaOrigem->cliente_id)
                ->where('id', '!=', $contaOrigem->id)
                ->get();

            if ($contasCliente->isEmpty()) {
                continue;
            }

            $contaDestino = $contasCliente->random();

            if ($contaOrigem->moeda_id === $contaDestino->moeda_id) {
                continue; // Câmbio requer moedas diferentes
            }

            // Obter taxa mais recente
            $taxa = TaxaCambio::where('moeda_origem_id', $contaOrigem->moeda_id)
                ->where('moeda_destino_id', $contaDestino->moeda_id)
                ->orderByDesc('data_taxa')
                ->orderByDesc('id')
                ->first();

            if (!$taxa) {
                continue;
            }

            $valorOrigem = fake()->randomFloat(2, 1000, 100000);
            if (function_exists('bcmul')) {
                $valorDestino = (float) bcmul((string) $valorOrigem, (string) $taxa->taxa_venda, 2);
            } else {
                $valorDestino = round($valorOrigem * (float) $taxa->taxa_venda, 2);
            }

            OperacaoCambio::create([
                'cliente_id' => $contaOrigem->cliente_id,
                'conta_origem_id' => $contaOrigem->id,
                'conta_destino_id' => $contaDestino->id,
                'moeda_origem_id' => $contaOrigem->moeda_id,
                'moeda_destino_id' => $contaDestino->moeda_id,
                'valor_origem' => $valorOrigem,
                'valor_destino' => $valorDestino,
                'taxa_utilizada' => $taxa->taxa_venda,
                'data_operacao' => Carbon::now()->subDays(fake()->numberBetween(1, 180)),
            ]);
        }

        $this->command->info('✅ Operações de câmbio criadas com sucesso! (' . OperacaoCambio::count() . ' total)');
    }
}

