<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pagamento;
use App\Models\Conta;
use App\Models\Moeda;
use App\Models\StatusPagamento;

class PagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contas = Conta::all();
        $statusPago = StatusPagamento::where('nome', 'Pago')->first() ?? StatusPagamento::first();
        $statusPendente = StatusPagamento::where('nome', 'Pendente')->first() ?? StatusPagamento::first();

        foreach ($contas as $conta) {
            // 3-10 pagamentos por conta
            $qtd = fake()->numberBetween(3, 10);
            for ($i = 0; $i < $qtd; $i++) {
                Pagamento::create([
                    'conta_id' => $conta->id,
                    'parceiro' => fake()->randomElement(['ZAP', 'DSTV', 'ENDE', 'UNITEL', 'MOVICEL', 'Multicaixa', 'Seguro Saúde']),
                    'referencia' => strtoupper(fake()->bothify('REF-######')),
                    'valor' => fake()->randomFloat(2, 500, 200000),
                    'moeda_id' => $conta->moeda_id,
                    'data_pagamento' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
                    'status_pagamento_id' => fake()->boolean(80) ? $statusPago->id : $statusPendente->id,
                ]);
            }
        }

        $this->command->info('✅ Pagamentos criados com sucesso! (' . Pagamento::count() . ' total)');
    }
}

