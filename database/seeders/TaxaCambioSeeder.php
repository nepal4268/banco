<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaxaCambio;
use App\Models\Moeda;

class TaxaCambioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $moedaAOA = Moeda::where('codigo', 'AOA')->first();
        $moedaUSD = Moeda::where('codigo', 'USD')->first();
        $moedaEUR = Moeda::where('codigo', 'EUR')->first();

        if (!$moedaAOA || !$moedaUSD || !$moedaEUR) {
            $this->command->warn('⚠️ Moedas não encontradas. Execute primeiro os seeders das moedas.');
            return;
        }

        $taxas = [
            // USD para AOA
            [
                'moeda_origem_id' => $moedaUSD->id,
                'moeda_destino_id' => $moedaAOA->id,
                'taxa_compra' => 825.50,  // Banco compra USD
                'taxa_venda' => 830.00,   // Banco vende USD
                'ativa' => true,
            ],
            // AOA para USD
            [
                'moeda_origem_id' => $moedaAOA->id,
                'moeda_destino_id' => $moedaUSD->id,
                'taxa_compra' => 0.001205,  // 1/830 (inverso da venda)
                'taxa_venda' => 0.001212,   // 1/825.50 (inverso da compra)
                'ativa' => true,
            ],
            // EUR para AOA
            [
                'moeda_origem_id' => $moedaEUR->id,
                'moeda_destino_id' => $moedaAOA->id,
                'taxa_compra' => 890.75,
                'taxa_venda' => 896.25,
                'ativa' => true,
            ],
            // AOA para EUR
            [
                'moeda_origem_id' => $moedaAOA->id,
                'moeda_destino_id' => $moedaEUR->id,
                'taxa_compra' => 0.001116,  // 1/896.25
                'taxa_venda' => 0.001123,   // 1/890.75
                'ativa' => true,
            ],
            // USD para EUR
            [
                'moeda_origem_id' => $moedaUSD->id,
                'moeda_destino_id' => $moedaEUR->id,
                'taxa_compra' => 0.9245,
                'taxa_venda' => 0.9285,
                'ativa' => true,
            ],
            // EUR para USD
            [
                'moeda_origem_id' => $moedaEUR->id,
                'moeda_destino_id' => $moedaUSD->id,
                'taxa_compra' => 1.0770,
                'taxa_venda' => 1.0815,
                'ativa' => true,
            ],
        ];

        foreach ($taxas as $taxaData) {
            TaxaCambio::create($taxaData);
        }

        $this->command->info('✅ Taxas de câmbio criadas com sucesso!');
        $this->command->info('💱 Taxas atuais:');
        $this->command->info('   USD → AOA: 825.50 (compra) / 830.00 (venda)');
        $this->command->info('   EUR → AOA: 890.75 (compra) / 896.25 (venda)');
        $this->command->info('   USD → EUR: 0.9245 (compra) / 0.9285 (venda)');
    }
}